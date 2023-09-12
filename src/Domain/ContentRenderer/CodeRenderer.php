<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

class CodeRenderer implements ContentRenderer {

	/**
	 * @var array<string, string>
	 */
	// TODO: incomplete
	private const EXTENSION_MAP = [
		'html' => 'html',
		'js' => 'javascript',
		'json' => 'json',
		'ts' => 'typescript',
		'md' => 'markdown',
		'php' => 'php',
		'py' => 'python'
	];

	public function render( string $content, string $contentUrl ): string {
		return '<pre class="external-content line-numbers"><code class="language-' . $this->getLanguage( $contentUrl ) . '">' .
			htmlspecialchars( $content ) .
			'</code></pre>';
	}

	private function getLanguage( string $contentUrl ): string {
		// TODO: should the extension be injected via the parser function?
		return self::EXTENSION_MAP[$this->extractFileExtension( $contentUrl )] ?? 'UNKNOWN';
	}

	private function extractFileExtension( string $contentUrl ): string {
		return pathinfo( $contentUrl, PATHINFO_EXTENSION );
	}

}
