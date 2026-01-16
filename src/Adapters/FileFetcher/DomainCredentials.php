<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters\FileFetcher;
use ProfessionalWiki\ExternalContent\GitHubUtils;

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

	public function getBearerTokenForDomain( string $domainName, string $fileUrl ): ? BearerTokenCredentials {
		$token = GitHubUtils::getAccessTokenForFileUrl( $fileUrl );
		$username = $this->bearerTokenCredentials[$domainName]->getUserName();
		
		return $this->bearerTokenCredentials[$domainName] = new BearerTokenCredentials( $username, $token) ?? null;
	}

	/**
	 * @param array<string, string[]> $basicAuthCredentials
	 * @param array<string, string> $bearerTokenCredentials
	 */
	public static function newFromArray( array $basicAuthCredentials, array $bearerTokenCredentials = [] ): self {
		$instance = new self();

		foreach ( $basicAuthCredentials as $domain => $credentials ) {
			$instance->addBasicAuth( $domain, new BasicAuthCredentials( $credentials[0], $credentials[1] ) );
		}
		
		foreach ( $bearerTokenCredentials as $domain => $credentials ) {			
			$instance->addBearerToken( $domain, new BearerTokenCredentials( $credentials[0], '') );
		}

		return $instance;
	}

}
