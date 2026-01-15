<?php

namespace ProfessionalWiki\ExternalContent\Utils;

use Firebase\JWT\JWT;
use MediaWiki\Logger\LoggerFactory;
use MediaWiki\MediaWikiServices;
  use MediaWiki\Http\HttpRequestFactory;

class Utils
{
	private $appId;
	private $logger;
	private $privateKey;

	public function __construct()
	{
		$config = MediaWikiServices::getInstance()->getMainConfig()->get('ExternalContentBearerTokenCredentials');
		$this->logger = LoggerFactory::getInstance('ExternalContent_BearerToken');
		$this->appId = $config['github_app_id'] ?? '';
		$this->privateKey = $config['github_private_key'] ?? '';
	}

	public function fetchGitHubAccessToken($fileUrl)
	{

		$accessToken = '';

		//Get jwt token
		$jwtToken = $this->getJwtToken();
		if (empty($jwtToken)) {
			return $accessToken;
		}

		//Find org name from url
		$orgName = $this->getOrganizationName($fileUrl);
		if (empty($orgName)) {
			return $accessToken;
		}

		// Check installation id's exist or not in db if not get it from gitapi & store it in db for given orgname
		$orgInstallationIdData = $this->getOrgInstallationData($orgName);
	
		if (!empty($orgInstallationIdData)) {
			$dbAccessTokenCreatedAt = $orgInstallationIdData['gat_created_at'];
			$dbInstallationId = $orgInstallationIdData['github_app_installation_id'];
			$dbOrgName = $orgInstallationIdData['organisation_name'];
			$dbAccessToken = $orgInstallationIdData['gat_github_access_token'];

			$timeDiffInSecs = (int) (strtotime(date('Y-m-d H:i:s')) - strtotime($dbAccessTokenCreatedAt));
			$isTokenExpired = $timeDiffInSecs >= 3600; //600 seconds

			if ($isTokenExpired) {
				//delete old tokens
				$this->deleteTokenByOrgInfo($dbOrgName, $dbInstallationId);

				//insert new token
				$accessToken = $this->getAccessToken($dbOrgName, $jwtToken, $dbInstallationId);

				$this->storeInstallationData($dbOrgName, $dbInstallationId, $accessToken);
			} else {
				$accessToken = $dbAccessToken;
			}
		} else {
			// Get installation ids for orgname            
			$orgInstallationIds = $this->getInstallationIds($jwtToken);			

			$orgInstallationIds = $this->getOrgNamesInstallationIds($orgInstallationIds);

			$installationId = $orgInstallationIds[$orgName] ?? '';

			//get access token
			$accessToken = $this->getAccessToken($orgName, $jwtToken, $installationId);

			// store in db
			$return = $this->storeInstallationData($orgName, $installationId, $accessToken);
		}

		return $accessToken;
	}

	public function deleteTokenByOrgInfo($orgName, $installationId)
	{
		$dbw = \MediaWiki\MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection(DB_PRIMARY);

		// 1. Identify the goi_id based on the provided criteria
		$goiId = $dbw->newSelectQueryBuilder()
			->select('goi_id')
			->from('git_org_installation_ids')
			->where([
				'organisation_name' => $orgName,
				'github_app_installation_id' => $installationId
			])
			->caller(__METHOD__)
			->fetchField();

		if (!$goiId) {
			return 0; // No record found to delete
		}

		// 2. Delete the record from git_access_tokens
		$dbw->newDeleteQueryBuilder()
			->deleteFrom('git_access_tokens')
			->where(['goi_id' => $goiId])
			->caller(__METHOD__)
			->execute();

		return $dbw->affectedRows();
	}

	public function storeInstallationData($orgName, $installationId, $accessToken)
	{
		$dbw = MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection(DB_PRIMARY);

		$dbw->startAtomic(__METHOD__);

		try {
			//Insert into git_org_installation_ids table
			$goiId = $dbw->newSelectQueryBuilder()
				->select('goi_id')
				->from('git_org_installation_ids')
				->where([
					'organisation_name' => $orgName,
					'github_app_installation_id' => $installationId
				])
				->caller(__METHOD__)
				->fetchField();

			// 2. If it doesn't exist, insert it
			if (!$goiId) {
				$inserted = $dbw->newInsertQueryBuilder()
					->insertInto('git_org_installation_ids')
					->row([
						'organisation_name' => $orgName,
						'github_app_installation_id' => $installationId
					])
					->caller(__METHOD__)
					->execute();
				$goiId = $dbw->insertId();
			}

			if ($goiId) {
				$dbw->newInsertQueryBuilder()
					->insertInto('git_access_tokens')
					->row([
						'goi_id' => $goiId,
						'gat_github_access_token' => $accessToken
					])
					->caller(__METHOD__)
					->execute();
			}
			$dbw->endAtomic(__METHOD__);
			return $goiId;
		} catch (\Exception $e) {
			$this->logger->info(" Error storing installation data: " . $e->getMessage());
			$dbw->rollback(__METHOD__);
		}
	}

	public function getAccessToken($orgName, $jwt, $installationId)
	{
		$accessToken = '';
		try {

			$url = "https://api.github.com/app/installations/$installationId/access_tokens";

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, ""); // GitHub expects POST body
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"Authorization: Bearer $jwt",
				"Accept: application/vnd.github+json",
				"X-GitHub-Api-Version: 2022-11-28",
				"User-Agent: $orgName"
			]);

			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

			$response = curl_exec($ch);

			if ($response == false) {
				// This will now catch protocol or connection errors
			} else {
				$data = json_decode($response, true);
				// Check for API-level errors (401, 403, 404)
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if ($httpCode !== 201) {
					error_log("GitHub API Error ($httpCode): " . $response);
				}
			}
			curl_close($ch);


			$data = json_decode($response, true);
			curl_close($ch);

			if (isset($data['token'])) {
				$accessToken = $data['token'];
			} else {
				echo "Error: " . ($data['message'] ?? 'Unknown error');
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return $accessToken;
	}

	public function getOrgNamesInstallationIds($iIds)
	{
		$return = [];
		foreach ($iIds as $value) {
			$return[$value['account']['login']] = $value['id'];
		}

		return $return;
	}

	public function getInstallationIds($jwtToken)
	{
		$installationIds = [];
		try {
			$curl = curl_init();

			curl_setopt_array($curl, [
				CURLOPT_URL => "https://api.github.com/app/installations",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => [
					"authorization: Bearer $jwtToken",
					"User-Agent: User"
				],
			]);

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				echo "cURL Error #:" . $err;
			} else {
				$response = !empty($response) ? json_decode($response, true) : [];
				$installationIds = $response;
			}
		} catch (Exception $e) {
			$installationIds[] = $e->getMessage();
		}
		return $installationIds;
	}

	public function getOrgInstallationData($orgName)
	{
		$dbr = MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection(DB_REPLICA);

		$queryBuilder = $dbr->newSelectQueryBuilder()
			->select([
				'organisation_name',
				'github_app_installation_id',
				'gat_github_access_token',
				'gat_created_at'
			])
			->from('git_org_installation_ids', 'org')
			->join('git_access_tokens', 'tokens', 'org.goi_id = tokens.goi_id')
			->where(['organisation_name' => $orgName]);

		$row =  $queryBuilder->caller(__METHOD__)->fetchRow();

		return $row ? (array)$row : [];
	}

	private function getOrganizationName($fileUrl)
	{
		$orgName = '';
		if (!filter_var($fileUrl, FILTER_VALIDATE_URL) !== false) {
			return $orgName;
		} else {
			$parsedUrls = parse_url($fileUrl);

			if (!empty($parsedUrls['path'])) {
				$pathArray = explode('/', $parsedUrls['path']);
				$orgName = $pathArray[1] ?? '';
			}
		}
		return $orgName;
	}

	private function getJwtToken()
	{
		$jwt = '';
		try {
			$payload = [
				'iat' => time(),          // Issued at time
				'exp' => time() + 600,    // Expiration time (10 minutes)
				'iss' => $this->appId,    // GitHub App ID
			];
			$jwt = JWT::encode($payload, $this->privateKey, 'RS256');
		} catch (Exception $e) {
			$this->logger->info(" Error generating JWT token: " . $e->getMessage());
		}
		return $jwt;
	}
}
