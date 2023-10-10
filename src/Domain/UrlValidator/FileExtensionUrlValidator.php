<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\UrlValidator;

use ProfessionalWiki\ExternalContent\Domain\UrlExtensionExtractor;
use ProfessionalWiki\ExternalContent\Domain\UrlValidator;

class FileExtensionUrlValidator implements UrlValidator {

	private array $allowedExtensions;

	public function __construct( string ...$allowedExtensions ) {
		$this->allowedExtensions = $allowedExtensions;
	}

	public function getErrorCode( string $url ): ?string {
		if ( $this->whitelistIsEmpty() || $this->extensionIsInWhitelist( $url ) ) {
			return null;
		}

		return 'extension-not-allowed';
	}

	private function whitelistIsEmpty(): bool {
		return $this->allowedExtensions === [];
	}

	private function extensionIsInWhitelist( string $url ): bool {
		return in_array(
			( new UrlExtensionExtractor() )->extractExtension( $url ),
			$this->allowedExtensions
		);
	}

}
