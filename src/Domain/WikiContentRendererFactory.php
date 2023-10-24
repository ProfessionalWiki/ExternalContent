<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\CodeRenderer;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\MarkdownRenderer;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\RendererConfig;

class WikiContentRendererFactory implements ContentRendererFactory {

	public function createContentRenderer( RendererConfig $config ): ContentRenderer {
		if ( $config->render ) {
			return new MarkdownRenderer();
		}
		else {
			return new CodeRenderer(
				language: ( $config->language !== '' ) ? $config->language : $config->fileExtension, // TODO: Use an extension-to-language map, although common extensions already work.
				showLineNumbers: $config->showLineNumbers,
				showEditButton: $config->showEditButton
			);
		}
	}

}
