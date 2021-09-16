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

		$parser->url_filter_func = function( string $url ) use ( $contentUrl ): string {
			if ( str_contains( $url, '://' ) ) {
				return $url;
			}

			return $this->expandRelativeUrl( $url, $contentUrl );
		};

		return $parser;
	}

	private function expandRelativeUrl( string $url, string $baseUrl ): string {
		$afterDoubleSlash = (int)strpos( $baseUrl, '://' ) + 3;
		$lastSingleSlash = strrpos( substr( $baseUrl, $afterDoubleSlash ), '/' );

		if ( $lastSingleSlash === false ) {
			return $baseUrl . '/' . trim( $url, '/' );
		}

		return substr_replace(
			$baseUrl,
			trim( $url, '/' ),
			$lastSingleSlash + $afterDoubleSlash + 1
		);
	}

}
