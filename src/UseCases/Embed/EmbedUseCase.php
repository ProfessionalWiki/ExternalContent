<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\UseCases\Embed;

use FileFetcher\FileFetcher;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\RendererConfig;
use ProfessionalWiki\ExternalContent\Domain\ContentRendererFactory;
use ProfessionalWiki\ExternalContent\Domain\UrlExtensionExtractor;
use ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;
use ProfessionalWiki\ExternalContent\Domain\UrlValidator;

class EmbedUseCase {

	private EmbedPresenter $presenter;
	private UrlValidator $urlValidator;
	private UrlNormalizer $urlNormalizer;
	private FileFetcher $fileFetcher;
	private ContentRendererFactory $contentRendererFactory;
	private EmbedResourceLoader $resourceLoader;

	public function __construct(
		EmbedPresenter $presenter,
		UrlValidator $urlValidator,
		UrlNormalizer $urlNormalizer,
		FileFetcher $fileFetcher,
		ContentRendererFactory $contentRendererFactory,
		EmbedResourceLoader $resourceLoader
	) {
		$this->presenter = $presenter;
		$this->urlValidator = $urlValidator;
		$this->urlNormalizer = $urlNormalizer;
		$this->fileFetcher = $fileFetcher;
		$this->contentRendererFactory = $contentRendererFactory;
		$this->resourceLoader = $resourceLoader;
	}

	public function embed( EmbedRequest $request ): void {
		try {
			$normalizedUrl = $this->urlNormalizer->fullNormalize( $request->fileUrl );
		}
		catch ( \RuntimeException $exception ) {
			$this->presenter->showError( $exception->getMessage() );
			return;
		}

		$urlValidationError = $this->urlValidator->getErrorCode( $normalizedUrl );

		if ( $urlValidationError !== null ) {
			$this->presenter->showError( $urlValidationError );
			return;
		}

		try {
			$content = $this->fileFetcher->fetchFile( $normalizedUrl );
		}
		catch ( \Exception $exception ) {
			$this->presenter->showFetchingError();
			return;
		}

		$renderer = $this->contentRendererFactory->createContentRenderer( $this->createRendererConfig( $request ) );

		$this->presenter->showContent(
			$renderer->render(
				$content,
				$this->urlNormalizer->viewLevelNormalize( $request->fileUrl )
			)
		);

		$this->resourceLoader->loadRendererResources( $renderer );
	}

	private function createRendererConfig( EmbedRequest $request ): RendererConfig {
		return new RendererConfig(
			fileExtension: ( new UrlExtensionExtractor() )->extractExtension(
				$this->urlNormalizer->fullNormalize( $request->fileUrl )
			),
			language: $request->language ?? '',
			showLineNumbers: $request->showLineNumbers ?? false
		);
	}

}
