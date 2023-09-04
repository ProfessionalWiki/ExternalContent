<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\UseCases\Embed;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

class NullContentRenderer implements ContentRenderer {

	public function render( string $content, string $contentUrl ): string {
		return $content;
	}

	public function getOutputModules(): array {
		return [];
	}

	public function getOutputModuleStyles(): array {
		return [];
	}

}
