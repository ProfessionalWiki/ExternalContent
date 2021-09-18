<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Presentation;

use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedPresenter;

class ParserFunctionEmbedPresenter implements EmbedPresenter {

	/**
	 * @var mixed[]|string
	 */
	private $parserFunctionReturnValue = '';

	public function showError( string $messageKey ): void {
		$this->parserFunctionReturnValue = $this->buildErrorResponse( $messageKey );
	}

	private function buildErrorResponse( string $messageKey ): string {
		return $messageKey; // TODO
	}

	public function showContent( string $content ): void {
		$this->parserFunctionReturnValue = $content; // TODO
	}

	// TODO: join into showError
	public function showFetchingError(): void {
		$this->parserFunctionReturnValue = $this->buildErrorResponse( 'fetch-failed' );
	}

	/**
	 * @return mixed[]|string
	 */
	public function getParserFunctionReturnValue() {
		return $this->parserFunctionReturnValue;
	}

}
