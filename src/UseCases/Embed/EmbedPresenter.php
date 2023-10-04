<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\UseCases\Embed;

interface EmbedPresenter {

	public function showError( string $messageKey ): void;

	public function showContent( string $content ): void;

	public function showFetchingError(): void;

}
