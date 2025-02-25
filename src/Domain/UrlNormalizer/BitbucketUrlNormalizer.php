<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;

use ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;

class BitbucketUrlNormalizer implements UrlNormalizer {

	public function fullNormalize( string $url ): string {
		return ( new HostAndPathModifier() )->modifyPath(
			$url,
			fn ( string $host, string $path ) => [ $host, $this->normalizePath( $path, true ) ]
		);
	}

	private function normalizePath( string $path, bool $toRawUrl ): string {
		// /projects/KNOW/repos/kittens/browse/Arbitrary.md
		$urlParts = explode( '/', $path );

		$this->assertIsBitbucketUrl( $urlParts );

		if ( ( $urlParts[5] ?? '' ) === '' ) {
			$urlParts[5] = 'browse';
		}

		if ( $toRawUrl ) {
			$urlParts[5] = 'raw';
		}

		if ( !isset( $urlParts[6] ) || $urlParts[6] === '' ) {
			$urlParts[6] = 'README.md';
		}

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

	public function viewLevelNormalize( string $url ): string {
		return ( new HostAndPathModifier() )->modifyPath(
			$url,
			fn ( string $host, string $path ) => [ $host, $this->normalizePath( $path, false ) ]
		);
	}

}
