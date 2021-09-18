<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\UseCases\Embed;

use FileFetcher\FileFetcher;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;
use ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;
use ProfessionalWiki\ExternalContent\Domain\UrlValidator;

class EmbedUseCase {

	private EmbedPresenter $presenter;
	private UrlValidator $urlValidator;
	private UrlNormalizer $urlNormalizer;
	private FileFetcher $fileFetcher;
	private ContentRenderer $contentRenderer;

	public function __construct(
		EmbedPresenter $presenter,
		UrlValidator $urlValidator,
		UrlNormalizer $urlNormalizer,
		FileFetcher $fileFetcher,
		ContentRenderer $contentRenderer
	) {
		$this->presenter = $presenter;
		$this->urlValidator = $urlValidator;
		$this->urlNormalizer = $urlNormalizer;
		$this->fileFetcher = $fileFetcher;
		$this->contentRenderer = $contentRenderer;
	}

	public function embed( string $fileUrl ): void {
		$urlValidationError = $this->urlValidator->getErrorCode( $fileUrl );

		if ( $urlValidationError !== null ) {
			$this->presenter->showError( $urlValidationError );
			return;
		}

		$content = $this->fileFetcher->fetchFile( $fileUrl );

		$this->presenter->showContent( $content );
	}

}
