<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\CodeRenderer;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\MarkdownRenderer;

class ContentRendererFactory {

	/**
	 * @param string[] $rendererConfig
	 */
	public function createContentRenderer( array $rendererConfig ): ContentRenderer {
		$options = $this->extractOptions( $rendererConfig );

		if ( $options === [] ) {
			return new MarkdownRenderer();
		}

		$language = $options['lang'] ?? false;
		if ( $language !== false ) {
			return new CodeRenderer(
				language: is_string( $language ) ? $language : '',
				showLineNumbers: ( $options['line'] ?? false ) !== false
			);
		}

		// TODO: use code renderer if extension is not .md, but no config was provided?

		return new MarkdownRenderer();
	}

	/**
	 * @param string[] $options
	 * @return array<string|boolean> $results
	 */
	private function extractOptions( array $options ): array {
		$results = [];

		foreach ( $options as $option ) {
			$pair = array_map( 'trim', explode( '=', $option, 2 ) );
			if ( count( $pair ) === 2 ) {
				$results[ $pair[0] ] = $pair[1];
			}
			if ( count( $pair ) === 1 ) {
				$results[ $pair[0] ] = true;
			}
		}

		return $results;
	}

}
