<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

class NullUrlNormalizer implements UrlNormalizer {

	public function normalize( string $url ): string {
		return $url;
	}

}
