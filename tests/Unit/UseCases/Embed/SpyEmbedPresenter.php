<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\UseCases\Embed;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;
use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedPresenter;

class SpyEmbedPresenter implements EmbedPresenter {

	public array $errors = [];
	public ?string $content = null;
	public bool $requirementsAreLoaded = false;

	public function showError( string $messageKey ): void {
		$this->errors[] = $messageKey;
	}

	public function showContent( string $content ): void {
		$this->content = $content;
	}

	public function showFetchingError(): void {
		$this->errors[] = 'fetch-error';
	}

	public function loadRendererRequirements( ContentRenderer $renderer ): void {
		$this->requirementsAreLoaded = true;
	}

}
