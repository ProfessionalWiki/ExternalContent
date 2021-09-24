<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

class UrlExpander {

	/**
	 * Turns relative URLs into full URLs.
	 */
	public function expand( string $url, string $baseUrl ): string {
		if ( str_contains( $url, '://' ) ) {
			return $url;
		}

		return $this->expandRelativeUrl( $url, $baseUrl );
	}

	private function expandRelativeUrl( string $url, string $baseUrl ): string {
		$afterDoubleSlash = (int)strpos( $baseUrl, '://' ) + 3;
		$lastSingleSlash = strrpos( substr( $baseUrl, $afterDoubleSlash ), '/' );

		if ( $lastSingleSlash === false ) {
			return $baseUrl . '/' . trim( $url, '/' );
		}

		return substr_replace(
			$baseUrl,
			trim( $url, '/' ),
			$lastSingleSlash + $afterDoubleSlash + 1
		);
	}

}
