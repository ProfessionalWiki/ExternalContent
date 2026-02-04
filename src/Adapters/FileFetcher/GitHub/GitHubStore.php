<?php

declare(strict_types = 1);

namespace ProfessionalWiki\ExternalContent\Adapters\FileFetcher\GitHub;

use ProfessionalWiki\ExternalContent\Security\TokenEncryption;
use Wikimedia\Rdbms\ILoadBalancer;

class GitHubStore {
	private $loadBalancer;
	private $tokenEncryption;

	public function __construct( ILoadBalancer $loadBalancer, TokenEncryption $tokenEncryption ) {
		$this->loadBalancer = $loadBalancer;
		$this->tokenEncryption = $tokenEncryption;
	}

	public function getOrgInstallationData( string $orgName ): array {
	
		$dbr = $this->loadBalancer->getConnection( DB_REPLICA );
        $row = $dbr->newSelectQueryBuilder()
			->select( [
				'organisation_name',
				'github_app_installation_id',
				'gat_github_access_token',
				'gat_created_at'
			] )
			->from( 'externaldata_git_org_installation_ids', 'org' )
			->join( 'externaldata_git_access_tokens', 'tokens', 'org.goi_id = tokens.goi_id' )
			->where( [ 'organisation_name' => $orgName ] )
			->orderBy( 'gat_created_at', 'DESC' )
  			->limit( 1 )
			->caller( __METHOD__ )
			->fetchRow();

		if ( !$row ) {
			return [];
		}

		$data = (array)$row;

		// Decrypt the access token
		try {
			$data['gat_github_access_token'] = $this->tokenEncryption->decrypt( $data['gat_github_access_token'] );
		} catch ( \RuntimeException $e ) {
			wfLogWarning( 'Failed to decrypt GitHub access token: ' . $e->getMessage() );
			return [];
		}
		return $data;
	}

	public function storeInstallationData( string $orgName, string $installationId, string $accessToken ) {
		$dbw = $this->loadBalancer->getConnection( DB_PRIMARY );
		$dbw->startAtomic( __METHOD__ );

		try {
			$goiId = $dbw->newSelectQueryBuilder()
				->select( 'goi_id' )
				->from( 'externaldata_git_org_installation_ids' )
				->where( [
					'organisation_name' => $orgName,
					'github_app_installation_id' => $installationId
				] )
				->fetchField();

			if ( !$goiId ) {
				$dbw->insert( 'externaldata_git_org_installation_ids', [
					'organisation_name' => $orgName,
					'github_app_installation_id' => $installationId
				], __METHOD__ );
				$goiId = $dbw->insertId();
			}

			// Encrypt the access token before storing
			$encryptedToken = $this->tokenEncryption->encrypt( $accessToken );

			$dbw->insert( 'externaldata_git_access_tokens', [
				'goi_id' => $goiId,
				'gat_github_access_token' => $encryptedToken
			], __METHOD__ );

			$dbw->endAtomic( __METHOD__ );
		} catch ( \Exception $e ) {
			$dbw->rollback( __METHOD__ );
			wfLogWarning( 'GitHubStore::storeInstallationData failed: ' . $e->getMessage() );
			throw $e;
		}
	}

	public function deleteTokenByOrg( string $orgName, string $installationId ) {
		$dbw = $this->loadBalancer->getConnection( DB_PRIMARY );
		$goiId = $dbw->selectField(
			'externaldata_git_org_installation_ids',
			'goi_id',
			[ 'organisation_name' => $orgName, 'github_app_installation_id' => $installationId ],
			__METHOD__
		);

		if ( $goiId ) {
			$dbw->delete( 'externaldata_git_access_tokens', [ 'goi_id' => $goiId ], __METHOD__ );
		}
	}
}