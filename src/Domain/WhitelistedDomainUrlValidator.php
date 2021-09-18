<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

class WhitelistedDomainUrlValidator implements UrlValidator {

	private array $allowedDomains;

	public function __construct( string ...$allowedDomains ) {
		$this->allowedDomains = $allowedDomains;
	}

	public function getErrorCode( string $url ): ?string {
		if ( in_array( parse_url( $url, PHP_URL_HOST ), $this->allowedDomains ) ) {
			return null;
		}

		return 'domain-not-allowed';
	}

}
