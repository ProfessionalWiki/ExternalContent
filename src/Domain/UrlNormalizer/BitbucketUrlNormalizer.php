<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;

use ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;

class BitbucketUrlNormalizer implements UrlNormalizer {

	public function normalize( string $url ): string {
		return ( new HostAndPathModifier() )->modifyPath(
			$url,
			fn( string $host, string $path ) => [ $host, $this->normalizePath( $path ) ]
		);
	}

	private function normalizePath( string $path ): string {
		// /projects/KNOW/repos/kittens/browse/Arbitrary.md
		$urlParts = explode( '/', $path );

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

}
