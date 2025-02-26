<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;

use ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;

/**
 * Normalizes URLs if they are valid GitHub URLs. Otherwise leaves them unchanged.
 *
 * Gotcha: repo root README.md defaulting will always use `master`, which is not correct for repos with another default branch.
 */
class GitHubUrlNormalizer implements UrlNormalizer {

	public function fullNormalize( string $url ): string {
		return ( new HostAndPathModifier() )->modifyPath(
			$url,
			function( string $host, string $path ) {
				if ( $host !== 'github.com' || !$this->isGitHubPath( $path ) ) {
					return [ $host, $path ];
				}

				return [ 'raw.githubusercontent.com', $this->normalizePath( $path, true ) ];
			}
		);
	}

	private function normalizePath( string $path, bool $removeBlobSegment ): string {
		// /ProfessionalWiki/ExternalContent/blob/master/README.md => ProfessionalWiki/ExternalContent/master/README.md
		$urlParts = explode( '/', $path );

		$urlParts[3] ??= '';

		if ( $urlParts[3] === '' ) {
			$urlParts[4] = 'master';
			$urlParts[5] = 'README.md';
		}

		if ( $urlParts[3] === 'tree' ) {
			$lastPartIndex = count( $urlParts ) - 1;

			if ( $urlParts[$lastPartIndex] === '' ) {
				unset( $urlParts[$lastPartIndex] );
			}

			$urlParts[] = 'README.md';
		}

		if ( $removeBlobSegment ) {
			unset( $urlParts[3] );
		} elseif ( $urlParts[3] === '' ) {
			$urlParts[3] = 'blob';
		}

		return implode( '/', $urlParts );
	}

	private function isGitHubPath( string $path ): bool {
		$urlParts = explode( '/', $path );

		if ( count( $urlParts ) < 3 ) {
			return false;
		}

		return $urlParts[2] !== ''
			&& in_array( $urlParts[3] ?? '', [ 'blob', 'tree', '' ] );
	}

	public function viewLevelNormalize( string $url ): string {
		return ( new HostAndPathModifier() )->modifyPath(
			$url,
			function( string $host, string $path ) {
				if ( $host !== 'github.com' || !$this->isGitHubPath( $path ) ) {
					return [ $host, $path ];
				}

				return [ $host, $this->normalizePath( $path, false ) ];
			}
		);
	}

}
