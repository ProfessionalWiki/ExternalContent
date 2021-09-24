<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

use RuntimeException;

interface UrlNormalizer {

	/**
	 * @throws RuntimeException Error message should be valid as part of an i18n message key
	 */
	public function normalize( string $url ): string;

}
