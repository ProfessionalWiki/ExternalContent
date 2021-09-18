<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent;

use Config;
use FileFetcher\FileFetcher;
use MediaWiki\MediaWikiServices;
use ProfessionalWiki\ExternalContent\DataAccess\MediaWikiFileFetcher;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;
use ProfessionalWiki\ExternalContent\Domain\MarkdownRenderer;
use ProfessionalWiki\ExternalContent\Domain\NullUrlNormalizer;
use ProfessionalWiki\ExternalContent\Domain\UrlValidator;
use ProfessionalWiki\ExternalContent\Domain\WhitelistedDomainUrlValidator;
use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedPresenter;
use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedUseCase;

final class EmbedExtensionFactory {

	private static ?self $instance;

	public static function getInstance(): self {
		self::$instance ??= new self();
		return self::$instance;
	}

	private ?FileFetcher $fileFetcher = null;

	public function newEmbedUseCaseForEmbedFunction( EmbedPresenter $presenter ): EmbedUseCase {
		return new EmbedUseCase(
			$presenter,
			$this->getUrlValidator(),
			new NullUrlNormalizer(),
			$this->getFileFetcher(),
			$this->getContentRender()
		);
	}

	private function getUrlValidator(): UrlValidator {
		/**
		 * @var array<int, string>
		 */
		$domains = $this->getConfig()->get( 'ExternalContentDomainWhitelist' );
		return new WhitelistedDomainUrlValidator( ...$domains );
	}

	private function getConfig(): Config {
		return MediaWikiServices::getInstance()->getMainConfig();
	}

	private function getFileFetcher(): FileFetcher {
		$this->fileFetcher ??= new MediaWikiFileFetcher( MediaWikiServices::getInstance()->getHttpRequestFactory() );
		return $this->fileFetcher;
	}

	private function getContentRender(): ContentRenderer {
		return new MarkdownRenderer();
	}

	public function setFileFetcher( FileFetcher $fileFetcher ): void {
		$this->fileFetcher = $fileFetcher;
	}

}
