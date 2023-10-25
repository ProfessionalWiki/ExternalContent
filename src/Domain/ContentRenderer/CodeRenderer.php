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
		private bool $showEditButton = false
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

		if ( $this->showEditButton == true ) {
			$attributes['data-toolbar-order'] = 'bitbucket-edit,' . $attributes['data-toolbar-order'];
			$attributes['data-src'] = $contentUrl;
		}

		return $attributes;
	}

	/**
	 * @return string
	 */
	private function lineNormalizer( string $lines ) {
		$exploded = explode( ',', preg_replace( '/\s+/', '', $lines ) );

		$ranges = array_filter( $exploded, function( $value ) {
			if ( preg_match( '/^\d+$/', $value ) || preg_match( '/^(\d+)-(\d+)$/', $value ) ) {
				return true;
			}
			else {
				return false;
			}
		});
		
		return implode( ',', $ranges );
	}
}
