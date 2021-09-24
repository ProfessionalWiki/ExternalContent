<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

class BitbucketUrlNormalizer implements UrlNormalizer {

	// TODO: inject default branch

	public function normalize( string $url ): string {
		$parsedUrl = parse_url( $url );

		$this->assertRequiredUrlPartsArePresent( $parsedUrl );

		/** @psalm-suppress PossiblyUndefinedArrayOffset */
		$parsedUrl['path'] = $this->normalizePath( $parsedUrl['path'] );

		return $this->buildUrl( $parsedUrl );
	}

	private function assertRequiredUrlPartsArePresent( array $parsedUrl ): void {
		if ( !array_key_exists( 'host', $parsedUrl ) ) {
			throw new \RuntimeException( 'url-missing-host' );
		}

		if ( !array_key_exists( 'path', $parsedUrl ) ) {
			throw new \RuntimeException( 'url-missing-path' );
		}
	}

	private function normalizePath( string $url ): string {
		// /projects/KNOW/repos/kittens/browse/Arbitrary.md
		$urlParts = explode( '/', $url );

		$this->assertIsBitbucketUrl( $urlParts );

		if ( in_array( $urlParts[5] ?? '', [ 'browse', '' ] ) ) {
			$urlParts[5] = 'raw';
		}

		$urlParts[6] = ( $urlParts[6] ?? '' ) === '' ? 'README.md' : $urlParts[6];

		return implode( '/', $urlParts );
	}

	private function assertIsBitbucketUrl( array $urlParts ): void {
		if ( ( $urlParts[1] ?? '' ) !== 'projects' ) {
			throw new \RuntimeException( 'url-not-bitbucket' );
		}

		if ( ( $urlParts[3] ?? '' ) !== 'repos' ) {
			throw new \RuntimeException( 'url-not-bitbucket' );
		}

		if ( ( $urlParts[4] ?? '' ) === '' ) {
			throw new \RuntimeException( 'url-missing-repository' );
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
		$pass = ( $user || $pass ) ? "$pass@" : '';
		$path = $parsedUrl['path'] ?? '';
		$query = isset( $parsedUrl['query'] ) ? '?' . $parsedUrl['query'] : '';
		$fragment = isset( $parsedUrl['fragment'] ) ? '#' . $parsedUrl['fragment'] : '';
		return "$scheme$user$pass$host$port$path$query$fragment";
	}

}
