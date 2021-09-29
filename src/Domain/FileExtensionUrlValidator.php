<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

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
			$this->extractExtension( $url ),
			$this->allowedExtensions
		);
	}

	private function extractExtension( string $url ): string {
		return pathinfo(
			parse_url( $url, PHP_URL_PATH ),
			PATHINFO_EXTENSION
		);
	}

}
