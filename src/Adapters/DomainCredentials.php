<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters;

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

}
