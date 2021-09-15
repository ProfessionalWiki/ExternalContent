<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

use RuntimeException;

interface UrlNormalizer {

	/**
	 * @throws RuntimeException
	 */
	public function normalize( string $url ): string;

}
