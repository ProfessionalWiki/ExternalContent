<?php

namespace ProfessionalWiki\ExternalContent;

use Wikimedia\Rdbms\ILoadBalancer;

class GitHubStore {
	private $loadBalancer;

	public function __construct( ILoadBalancer $loadBalancer ) {
		$this->loadBalancer = $loadBalancer;
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
			->from( 'git_org_installation_ids', 'org' )
			->join( 'git_access_tokens', 'tokens', 'org.goi_id = tokens.goi_id' )
			->where( [ 'organisation_name' => $orgName ] )
			->caller( __METHOD__ )
			->fetchRow();
		return $row ? (array)$row : [];
	}

	public function storeInstallationData( string $orgName, string $installationId, string $accessToken ) {
		$dbw = $this->loadBalancer->getConnection( DB_PRIMARY );
		$dbw->startAtomic( __METHOD__ );

		try {
			$goiId = $dbw->newSelectQueryBuilder()
				->select( 'goi_id' )
				->from( 'git_org_installation_ids' )
				->where( [
					'organisation_name' => $orgName,
					'github_app_installation_id' => $installationId
				] )
				->fetchField();

			if ( !$goiId ) {
				$dbw->insert( 'git_org_installation_ids', [
					'organisation_name' => $orgName,
					'github_app_installation_id' => $installationId
				], __METHOD__ );
				$goiId = $dbw->insertId();
			}

			$dbw->insert( 'git_access_tokens', [
				'goi_id' => $goiId,
				'gat_github_access_token' => $accessToken
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
			'git_org_installation_ids',
			'goi_id',
			[ 'organisation_name' => $orgName, 'github_app_installation_id' => $installationId ],
			__METHOD__
		);

		if ( $goiId ) {
			$dbw->delete( 'git_access_tokens', [ 'goi_id' => $goiId ], __METHOD__ );
		}
	}
}