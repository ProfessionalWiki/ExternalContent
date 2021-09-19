<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Presentation;

use MessageLocalizer;
use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedPresenter;

class ParserFunctionEmbedPresenter implements EmbedPresenter {

	private MessageLocalizer $localizer;

	private string $html = '';

	public function __construct( MessageLocalizer $localizer ) {
		$this->localizer = $localizer;
	}

	public function showError( string $messageKey ): void {
		$this->html = $this->buildErrorResponse( $messageKey );
	}

	private function buildErrorResponse( string $messageKey ): string {
		return '<div><span class="errorbox">'
			. $this->localizer->msg( 'external-content-' . $messageKey )->parse()
			. '</span></div><br /><br />';
	}

	public function showContent( string $content ): void {
		$this->html = $content;
	}

	// TODO: join into showError
	public function showFetchingError(): void {
		$this->html = $this->buildErrorResponse( 'fetch-failed' );
	}

	public function getParserFunctionReturnValue(): array {
		return [
			$this->html,
			'noparse' => true,
			'isHTML' => true,
		];
	}

}
