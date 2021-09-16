<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

use Michelf\Markdown;

class MarkdownRenderer implements ContentRenderer {

	public function normalize( string $content ): string {
		$parser = new Markdown();

		return ( new ContentPurifier() )->purify(
			trim( $parser->transform( $content ) )
		);
	}

}
