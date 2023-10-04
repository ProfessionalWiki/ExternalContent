<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

interface ContentRenderer {

	public function render( string $content, string $contentUrl ): string;

}
