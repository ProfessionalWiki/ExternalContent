<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\UrlValidator;

use ProfessionalWiki\ExternalContent\Domain\UrlValidator;

class WhitelistedDomainUrlValidator implements UrlValidator {

	private array $allowedDomains;

	public function __construct( string ...$allowedDomains ) {
		$this->allowedDomains = $allowedDomains;
	}

	public function getErrorCode( string $url ): ?string {
		if ( $this->whitelistIsEmpty() || $this->domainIsInWhitelist( $url ) ) {
			return null;
		}

		return 'domain-not-allowed';
	}

	private function whitelistIsEmpty(): bool {
		return $this->allowedDomains === [];
	}

	private function domainIsInWhitelist( string $url ): bool {
		return in_array( parse_url( $url, PHP_URL_HOST ), $this->allowedDomains );
	}

}
