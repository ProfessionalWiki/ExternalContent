<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;

class HostAndPathModifier {

	public function modifyPath( string $url, callable $modificationFunction ): string {
		$parsedUrl = parse_url( $url );

		$this->assertRequiredUrlPartsArePresent( $parsedUrl );

		/**
		 * @psalm-suppress PossiblyUndefinedArrayOffset
		 * @psalm-suppress MixedAssignment
		 * @psalm-suppress MixedArrayAccess
		 */
		[ $parsedUrl['host'], $parsedUrl['path'] ] = $modificationFunction( $parsedUrl['host'], $parsedUrl['path'] ?? '' );

		return $this->buildUrl( $parsedUrl );
	}

	private function assertRequiredUrlPartsArePresent( array $parsedUrl ): void {
		if ( !array_key_exists( 'host', $parsedUrl ) ) {
			throw new \RuntimeException( 'url-missing-host' );
		}
	}

	/**
	 * Modernized version of https://www.php.net/manual/en/function.parse-url.php#106731
	 * @psalm-suppress MixedOperand
	 * @psalm-suppress MixedAssignment
	 */
	private function buildUrl( array $parsedUrl ): string {
		$scheme = isset( $parsedUrl['scheme'] ) ? $parsedUrl['scheme'] . '://' : '';
		$host = $parsedUrl['host'] ?? '';
		$port = isset( $parsedUrl['port'] ) ? ':' . $parsedUrl['port'] : '';
		$user = $parsedUrl['user'] ?? '';
		$pass = isset( $parsedUrl['pass'] ) ? ':' . $parsedUrl['pass'] : '';

		$pass = ( $user !== '' || $pass !== '' ) ? "$pass@" : '';
		
		$path = $parsedUrl['path'] ?? '';
		$query = isset( $parsedUrl['query'] ) ? '?' . $parsedUrl['query'] : '';
		$fragment = isset( $parsedUrl['fragment'] ) ? '#' . $parsedUrl['fragment'] : '';
		return "$scheme$user$pass$host$port$path$query$fragment";
	}

}
