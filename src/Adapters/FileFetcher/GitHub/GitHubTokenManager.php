<?php

namespace ProfessionalWiki\ExternalContent\Adapters\FileFetcher\GitHub;
use ProfessionalWiki\ExternalContent\Adapters\FileFetcher\GitHub\GitHubApi;
use ProfessionalWiki\ExternalContent\Adapters\FileFetcher\GitHub\GitHubStore;

class GitHubTokenManager
{
	const TOKEN_VALIDITY_BUFFER = 3300; // 55 minutes
	private GitHubApi $api;
	private GitHubStore $store;

	public function __construct(GitHubApi $api, GitHubStore $store)
	{
		$this->api = $api;
		$this->store = $store;
	}

	public function getValidAccessToken(string $fileUrl): string
	{
		if (!$fileUrl) {
			return '';
		}

		$orgName = $this->extractOrgName($fileUrl);		

		if (!$orgName) {
			return '';
		}
		$data = $this->store->getOrgInstallationData($orgName);
		#px($data);	
		if ($data) {
			$createdAt = strtotime($data['gat_created_at']);
			if ((time() - $createdAt) < self::TOKEN_VALIDITY_BUFFER) { // 55 mins buffer
				return $data['gat_github_access_token'];
			}
			$this->store->deleteTokenByOrg($orgName, $data['github_app_installation_id']);
		}

		return $this->refreshAndStoreToken($orgName);
	}

	private function refreshAndStoreToken(string $orgName): string
	{
		$jwt = $this->api->getJwtToken();
		$installations = $this->api->getInstallationIds($jwt);

		$instId = null;
		foreach ($installations as $inst) {
			if (($inst['account']['login'] ?? '') === $orgName) {
				$instId = (string)$inst['id'];
				break;
			}
		}
		if ($instId) {
			$token = $this->api->fetchAccessToken($instId, $jwt);
			if ($token) {
				$this->store->storeInstallationData($orgName, $instId, $token);
				return $token;
			}
		}

		return '';
	}

	private function extractOrgName(string $url): string
	{
		$path = parse_url($url, PHP_URL_PATH);
		$parts = explode('/', trim($path, '/'));
		return $parts[0] ?? '';
	}
}
