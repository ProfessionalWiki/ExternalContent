<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

use RuntimeException;

interface UrlNormalizer {

	/**
	 * @throws RuntimeException Error message should be valid as part of an i18n message key
	 */
	public function fullNormalize( string $url ): string;

	/**
	 * Normalizes without changing "view" URLs into "raw" URLs.
	 *
	 * @throws RuntimeException Error message should be valid as part of an i18n message key
	 */
	public function viewLevelNormalize( string $url ): string;

}
