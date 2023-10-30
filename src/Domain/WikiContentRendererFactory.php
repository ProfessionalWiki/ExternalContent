<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\CodeRenderer;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\MarkdownRenderer;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\RendererConfig;

class WikiContentRendererFactory implements ContentRendererFactory {

	public function createContentRenderer( RendererConfig $config ): ContentRenderer {
		if ( $config->language !== '' ) {
			return new CodeRenderer(
				language: $config->language,
				showLineNumbers: $config->showLineNumbers,
				showSpecificLines: $config->showSpecificLines,
				showEditButton: $config->showEditButton
			);
		}

		if ( $config->render && $config->fileExtension === 'md' ) {
			return new MarkdownRenderer();
		}

		return new CodeRenderer(
			language: $config->fileExtension, // TODO: Use an extension-to-language map, although common extensions already work.
			showLineNumbers: $config->showLineNumbers,
			showSpecificLines: $config->showSpecificLines,
			showEditButton: $config->showEditButton
		);
	}

}
