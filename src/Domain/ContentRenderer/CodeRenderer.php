<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

use Html;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

class CodeRenderer implements ContentRenderer {

	public function __construct(
		private string $language,
		private bool $showLineNumbers,
		private string $showSpecificLines,
		private bool $showEditButton
	) {
	}

	public function render( string $content, string $contentUrl ): string {
		return Html::rawElement(
			'pre',
			$this->getWrapperAttributes( $contentUrl ),
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
		return [ 'language-' . htmlspecialchars( $this->language ) ];
	}

	/**
	 * @return (string|string[])[]
	 */
	private function getWrapperAttributes( string $contentUrl ): array {
		$attributes = [];

		$attributes['class'] = $this->getWrapperClasses();

		if ( !empty( $this->showSpecificLines ) ) {
			$attributes['data-show-lines'] = $this->lineNormalizer( $this->showSpecificLines );
		}

		$attributes['data-toolbar-order'] = 'copy-to-clipboard';

		if ( $this->showEditButton ) {
			$attributes['data-toolbar-order'] = 'bitbucket-edit,' . $attributes['data-toolbar-order'];
			$attributes['data-src'] = $contentUrl;
		}

		return $attributes;
	}

	/**
	 * @return string
	 */
	private function lineNormalizer( string $lines ): string {
		$exploded = explode( ',', preg_replace( '/\s+/', '', $lines ) );

		$ranges = array_filter( $exploded, static function( $value ): bool {
			return ( preg_match( '/^\d+$/', $value ) || preg_match( '/^(\d+)-(\d+)$/', $value ) );
		} );
		
		return implode( ',', $ranges );
	}
}
