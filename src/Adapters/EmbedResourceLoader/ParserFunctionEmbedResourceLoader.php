<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters\EmbedResourceLoader;

use ParserOutput;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\CodeRenderer;
use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedResourceLoader;

class ParserFunctionEmbedResourceLoader implements EmbedResourceLoader {

	public function __construct(
		private ParserOutput $output
	) {
	}

	public function loadRendererResources( ContentRenderer $renderer ): void {
		if ( $renderer instanceof CodeRenderer ) {
			$this->output->addModules( [ 'ext.external-content.code-renderer' ] );
			$this->output->addModuleStyles( [ 'ext.external-content.code-renderer.styles' ] );
		}
	}

}
