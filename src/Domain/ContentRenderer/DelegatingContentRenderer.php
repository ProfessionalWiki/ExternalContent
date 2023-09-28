<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

class DelegatingContentRenderer implements ContentRenderer {

	private ?ContentRenderer $renderer = null;

	/**
	 * @param string[] $arguments
	 */
	public function __construct(
		private array $arguments
	) {
	}

	public function render( string $content, string $contentUrl ): string {
		$this->renderer = $this->createRenderer( $contentUrl );
		return $this->renderer->render( $content, $contentUrl );
	}

	private function createRenderer( string $contentUrl ): ContentRenderer {
		$options = $this->extractOptions( $this->arguments );
		$extension = $this->extractFileExtension( $contentUrl );
		$language = $options['lang'] ?? false;

		if ( $options === [] || !is_string( $language ) || $language === '' ) {
			return $this->createRendererFromExtension( $extension, $options );
		}

		return new CodeRenderer(
			language: $language,
			showLineNumbers: ( $options['line'] ?? false ) !== false
		);
	}

	/**
	 * @param array<string,string|boolean> $options
	 */
	private function createRendererFromExtension( string $extension, array $options ): ContentRenderer {
		if ( $extension === 'md' ) {
			return new MarkdownRenderer();
		}

		return new CodeRenderer(
			language: $extension, // TODO: Use an extension-to-language map, although common extensions already work.
			showLineNumbers: ( $options['line'] ?? false ) !== false
		);
	}

	private function extractFileExtension( string $contentUrl ): string {
		return pathinfo( $contentUrl, PATHINFO_EXTENSION );
	}

	/**
	 * @param string[] $options
	 * @return array<string,string|boolean> $results
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

	public function getOutputModules(): array {
		if ( $this->renderer === null ) {
			return [];
		}

		return $this->renderer->getOutputModules();
	}

	public function getOutputModuleStyles(): array {
		if ( $this->renderer === null ) {
			return [];
		}

		return $this->renderer->getOutputModuleStyles();
	}

}
