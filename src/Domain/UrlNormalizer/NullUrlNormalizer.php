<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;

use ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;

class NullUrlNormalizer implements UrlNormalizer {

	public function normalize( string $url ): string {
		return $url;
	}

}
