<?php

namespace ProfessionalWiki\ExternalContent;

class TokenManager {
	private $api;
	private $store;

	public function __construct( GitHubApi $api, GitHubStore $store) {
		$this->api = $api;
		$this->store = $store;
	}

	public function getValidAccessToken( string $fileUrl ): string {
		$orgName = $this->extractOrgName( $fileUrl );

		if ( !$orgName ){
            return '';
        }
		$data = $this->store->getOrgInstallationData( $orgName );       
    
		if ( $data ) {
			$createdAt = strtotime( $data['gat_created_at'] );
			if ( ( time() - $createdAt ) < 3300 ) { // 55 mins buffer
				return $data['gat_github_access_token'];
			}
			$this->store->deleteTokenByOrg( $orgName, $data['github_app_installation_id'] );
		}

		return $this->refreshAndStoreToken( $orgName );
	}

	private function refreshAndStoreToken( string $orgName ): string {
		$jwt = $this->api->getJwtToken();        
		$installations = $this->api->getInstallationIds( $jwt );
		
        foreach ( $installations as $inst ) {
			if ( ( $inst['account']['login'] ?? '' ) === $orgName ) {
				$instId = $inst['id'];
				break;
			}
		}
		if ( $instId ) {
			$token = $this->api->fetchAccessToken( $instId, $jwt, $orgName );
			if ( $token ) {
				$this->store->storeInstallationData( $orgName, $instId, $token );
				return $token;
			}
		}

		return '';
	}

	private function extractOrgName( string $url ): string {
		$path = parse_url( $url, PHP_URL_PATH );
		$parts = explode( '/', trim( $path, '/' ) );
		return $parts[0] ?? '';
	}
}