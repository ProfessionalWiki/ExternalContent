<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters\FileFetcher;

class DomainCredentials {

	/**
	 * @var array<string, BasicAuthCredentials>
	 */
	private array $credentials = [];

	public function add( string $domainName, BasicAuthCredentials $credentials ): void {
		$this->credentials[$domainName] = $credentials;
	}

	public function getForDomain( string $domainName ): ?BasicAuthCredentials {
		return $this->credentials[$domainName] ?? null;
	}

	/**
	 * @param array<string, string[]> $domainCredentials
	 */
	public static function newFromArray( array $domainCredentials ): self {
		$instance = new self();

		foreach ( $domainCredentials as $domain => $credentials ) {
			$instance->add( $domain, new BasicAuthCredentials( $credentials[0], $credentials[1] ) );
		}

		return $instance;
	}

}
