<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\UseCases\Embed;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

interface EmbedPresenter {

	public function showError( string $messageKey ): void;

	public function showContent( string $content ): void;

	public function showFetchingError(): void;

	public function loadRendererRequirements( ContentRenderer $renderer ): void;

}
