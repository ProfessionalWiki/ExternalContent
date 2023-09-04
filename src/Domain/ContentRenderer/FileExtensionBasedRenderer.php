<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

class FileExtensionBasedRenderer implements ContentRenderer {

	public function render( string $content, string $contentUrl ): string {
		$extension = $this->extractFileExtension( $contentUrl );

		// TODO: what if I want to show syntax highlighted Markdown?
		if ( $extension == 'md' ) {
			return ( new MarkdownRenderer() )->render( $content, $contentUrl );
		}

		return ( new CodeRenderer() )->render( $content, $contentUrl );
	}

	private function extractFileExtension( string $contentUrl ): string {
		return pathinfo( $contentUrl, PATHINFO_EXTENSION );
	}

}
