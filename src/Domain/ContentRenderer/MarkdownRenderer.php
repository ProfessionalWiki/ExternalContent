<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

use Michelf\Markdown;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

class MarkdownRenderer implements ContentRenderer {

	public function render( string $content, string $contentUrl ): string {
		return ( new ContentPurifier() )->purify(
			trim( $this->newMarkdownParser( $contentUrl )->transform( $content ) )
		);
	}

	private function newMarkdownParser( string $contentUrl ): Markdown {
		$parser = new Markdown();
		$urlExpander = new UrlExpander();

		$parser->url_filter_func = fn( string $url ) => $urlExpander->expand( $url, $contentUrl );

		return $parser;
	}

}
