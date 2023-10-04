<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\UseCases\Embed;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\RendererConfig;
use ProfessionalWiki\ExternalContent\Domain\ContentRendererFactory;

class StubContentRendererFactory implements ContentRendererFactory {

	public function __construct(
		private ContentRenderer $renderer
	) {
	}

	public function createContentRenderer( RendererConfig $config ): ContentRenderer {
		return $this->renderer;
	}

}
