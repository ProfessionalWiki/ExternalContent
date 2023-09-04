<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

use Html;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

class CodeRenderer implements ContentRenderer {

	public function __construct(
		private string $language,
		private bool $showLineNumbers
	) {
	}

	public function render( string $content, string $contentUrl ): string {
		return Html::rawElement(
			'pre',
			[ 'class' => $this->getWrapperClasses() ],
			Html::element(
				'code',
				[ 'class' => $this->getLanguageClasses() ],
				$content
			)
		);
	}

	/**
	 * @return string[]
	 */
	private function getWrapperClasses(): array {
		$classes = [ 'external-content' ];

		if ( $this->showLineNumbers ) {
			$classes[] = 'line-numbers';
		}

		return $classes;
	}

	/**
	 * @return string[]
	 */
	private function getLanguageClasses(): array {
		if ( $this->language === '' ) {
			return [];
		}

		return [ 'language-' . htmlspecialchars( $this->language ) ];
	}

	public function getOutputModules(): array {
		return [ 'ext.external-content.code-renderer' ];
	}

	public function getOutputModuleStyles(): array {
		return [ 'ext.external-content.code-renderer.styles' ];
	}

}
