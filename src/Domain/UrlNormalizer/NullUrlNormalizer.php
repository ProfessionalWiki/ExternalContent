<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;

use ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;

class NullUrlNormalizer implements UrlNormalizer {

	public function fullNormalize( string $url ): string {
		return $url;
	}

	public function viewLevelNormalize( string $url ): string {
		return $url;
	}

}
