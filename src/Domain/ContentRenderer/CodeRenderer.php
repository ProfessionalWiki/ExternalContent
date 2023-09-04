<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

class CodeRenderer implements ContentRenderer {

	public function render( string $content, string $contentUrl ): string {
		return '<pre class="external-content"><code>' .
			htmlspecialchars( $content ) .
			'</code></pre>';
	}

}
