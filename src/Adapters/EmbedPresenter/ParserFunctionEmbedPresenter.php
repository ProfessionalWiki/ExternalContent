<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter;

use Html;
use MessageLocalizer;
use ParserOutput;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;
use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedPresenter;

class ParserFunctionEmbedPresenter implements EmbedPresenter {

	private MessageLocalizer $localizer;

	private string $html = '';
	private UsageTracker $usageTracker;
	private ParserOutput $output;

	public function __construct( MessageLocalizer $localizer, UsageTracker $usageTracker, ParserOutput $output ) {
		$this->localizer = $localizer;
		$this->usageTracker = $usageTracker;
		$this->output = $output;
	}

	public function showError( string $messageKey ): void {
		$this->html = $this->buildErrorResponse( $messageKey );
		$this->usageTracker->trackBrokenUsage();
	}

	private function buildErrorResponse( string $messageKey ): string {
		return Html::errorBox(
			$this->localizer->msg( 'external-content-' . $messageKey )->parse()
		);
	}

	public function showContent( string $content ): void {
		$this->html = $content;
		$this->usageTracker->trackUsage();
	}

	// TODO: join into showError
	public function showFetchingError(): void {
		$this->html = $this->buildErrorResponse( 'fetch-failed' );
		$this->usageTracker->trackBrokenUsage();
	}

	public function getParserFunctionReturnValue(): array {
		return [
			$this->html,
			'noparse' => true,
			'isHTML' => true,
		];
	}

	public function loadRendererRequirements( ContentRenderer $renderer ): void {
		$this->output->addModules( $renderer->getOutputModules() );
		$this->output->addModuleStyles( $renderer->getOutputModuleStyles() );
	}

}
