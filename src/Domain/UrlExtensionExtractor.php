<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

class UrlExtensionExtractor {

	public function extractExtension( string $url ): string {
		return pathinfo(
			parse_url( $url, PHP_URL_PATH ),
			PATHINFO_EXTENSION
		);
	}

}
