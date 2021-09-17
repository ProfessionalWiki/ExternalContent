<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

use Michelf\Markdown;

class MarkdownRenderer implements ContentRenderer {

	public function normalize( string $content, string $contentUrl ): string {
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
