<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

interface ContentRenderer {

	public function normalize( string $content, string $contentUrl ): string;

}
