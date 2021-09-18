<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\UseCases\Embed;

use ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;

class NullUrlNormalizer implements UrlNormalizer {

	public function normalize( string $url ): string {
		return $url;
	}

}
