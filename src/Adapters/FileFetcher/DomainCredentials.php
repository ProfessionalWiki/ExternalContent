<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters\FileFetcher;
use ProfessionalWiki\ExternalContent\Adapters\FileFetcher\GitHub\GitHubUtils;

class DomainCredentials {

	/**
	 * @var array<string, BasicAuthCredentials>
	 */
	private array $basicAuthCredentials = [];

	/**
	 * @var array<string, BearerTokenCredentials>
	 */
	private array $bearerTokenCredentials = [];

	public function addBasicAuth( string $domainName, BasicAuthCredentials $credentials ): void {
		$this->basicAuthCredentials[$domainName] = $credentials;
	}

	public function addBearerToken( string $domainName, BearerTokenCredentials $credentials ): void {		
		$this->bearerTokenCredentials[$domainName] = $credentials;
	}

	public function getBasicAuthForDomain( string $domainName ): ?BasicAuthCredentials {
		return $this->basicAuthCredentials[$domainName] ?? null;
	}

	public function getBearerTokenForDomain( string $domainName, string $fileUrl ): ?BearerTokenCredentials {		
		return $this->bearerTokenCredentials[$domainName] ?? null;
	}

	/**
	 * @param array<string, string[]> $basicAuthCredentials
	 * @param array<string, string> $bearerTokenCredentials
	 */
	public static function newFromArray( array $basicAuthCredentials, array $bearerTokenCredentials ): self {
		$instance = new self();

		foreach ( $basicAuthCredentials as $domain => $credentials ) {
			$instance->addBasicAuth( $domain, new BasicAuthCredentials( $credentials[0], $credentials[1] ) );
		}
		foreach ( $bearerTokenCredentials as $domain => $credentials ) {			
			$instance->addBearerToken( $domain, new BearerTokenCredentials($credentials['app_id'], $credentials['private_key'], $credentials['encryption_key'] ) );
		}

		return $instance;
	}

	private function hasBasicAuthorization( string $domain ): bool {
		return in_array ( $domain , array_keys($this->basicAuthCredentials) );
	}
	
	private function hasTokenAuthorization( string $domain ): bool {
		return in_array ( $domain , array_keys($this->bearerTokenCredentials) );
	}

	public function getAuthorizationHeader( string $domain, string $fileUrl ): string {
		$authHeader = '';
		if ( $this->hasBasicAuthorization( $domain ) ) {
			$basicAuth = $this->getBasicAuthForDomain( $domain );
			$authHeader = 'Basic ' . base64_encode( $basicAuth->getUsername() . ':' . $basicAuth->getPassword() );
		}		
		if ( $this->hasTokenAuthorization( $domain ) ) {			
			$token = $this->getAuthToken( $domain, $fileUrl );		
			if ( !empty( $token ) ) {
				$authHeader = 'Bearer ' . $token;
			}
		}
		return $authHeader;
	}

	private function getAuthToken( string $domain, string $fileUrl ): string {
		$token = '';
		try{
			$token = GitHubUtils::getAccessTokenForFileUrl( $this->bearerTokenCredentials[$domain], $fileUrl );
			$this->bearerTokenCredentials[$domain]->setToken($token);
		}catch(\Exception $e){
			wfLogWarning( 'Failed to get GitHub access token: ' . $e->getMessage() );
		}		
		return $token;
	}
}
