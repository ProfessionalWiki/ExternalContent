<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

class BitbucketUrlNormalizer implements UrlNormalizer {

	// TODO: inject default branch

	public function normalize( string $url ): string {
		$urlParts = explode( '/', $url );
		$urlParts[] = $this->defaultLastPart( array_pop( $urlParts ) );
		return implode( '/', $urlParts );
	}

	private function defaultLastPart( string $urlPart ): string {
		if ( $urlPart === '' ) {
			return 'README.md';
		}

		if ( $this->isDirectory( $urlPart ) ) {
			 return $urlPart . '/README.md';
		}

		return $urlPart;
	}

	private function isDirectory( string $urlPart ): bool {
		return !str_contains( $urlPart, '.' );
	}

}
