<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

interface UrlValidator {

	/**
	 * @return string|null Null for valid URLs
	 */
	public function getErrorCode( string $url ): ?string;

}
