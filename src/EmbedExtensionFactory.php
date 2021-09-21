<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent;

use FileFetcher\FileFetcher;
use MediaWiki\MediaWikiServices;
use Message;
use MessageLocalizer;
use ProfessionalWiki\ExternalContent\Adapters\MediaWikiFileFetcher;
use ProfessionalWiki\ExternalContent\Domain\BitbucketUrlNormalizer;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;
use ProfessionalWiki\ExternalContent\Domain\MarkdownRenderer;
use ProfessionalWiki\ExternalContent\Domain\NullUrlNormalizer;
use ProfessionalWiki\ExternalContent\Domain\UrlValidator;
use ProfessionalWiki\ExternalContent\Domain\WhitelistedDomainUrlValidator;
use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedPresenter;
use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedUseCase;

class EmbedExtensionFactory {

	protected static ?self $instance;

	public static function getInstance(): self {
		self::$instance ??= new self();
		return self::$instance;
	}

	protected ?FileFetcher $fileFetcher = null;
	protected ?MessageLocalizer $localizer = null;

	/**
	 * @var null|array<int, string>
	 */
	protected ?array $domainWhitelist = null;

	final protected function __construct() {
	}

	public function newEmbedUseCaseForEmbedFunction( EmbedPresenter $presenter ): EmbedUseCase {
		return new EmbedUseCase(
			$presenter,
			$this->getUrlValidator(),
			new NullUrlNormalizer(),
			$this->getFileFetcher(),
			$this->getContentRender()
		);
	}

	public function newEmbedUseCaseForBitbucketFunction( EmbedPresenter $presenter ): EmbedUseCase {
		return new EmbedUseCase(
			$presenter,
			$this->getUrlValidator(),
			new BitbucketUrlNormalizer(),
			$this->getFileFetcher(),
			$this->getContentRender()
		);
	}

	private function getUrlValidator(): UrlValidator {
		/** @var array<int, string> */
		$domains = MediaWikiServices::getInstance()->getMainConfig()->get( 'ExternalContentDomainWhitelist' );

		$this->domainWhitelist ??= $domains;

		return new WhitelistedDomainUrlValidator( ...$this->domainWhitelist );
	}

	private function getFileFetcher(): FileFetcher {
		$this->fileFetcher ??= new MediaWikiFileFetcher( MediaWikiServices::getInstance()->getHttpRequestFactory() );
		return $this->fileFetcher;
	}

	private function getContentRender(): ContentRenderer {
		return new MarkdownRenderer();
	}

	public function getMessageLocalizer(): MessageLocalizer {
		$this->localizer ??= $this->newMessageLocalizer();
		return $this->localizer;
	}

	/**
	 * @psalm-suppress MixedInferredReturnType
	 * @psalm-suppress UndefinedFunction
	 * @psalm-suppress MixedReturnStatement
	 * @psalm-suppress UndefinedClass
	 */
	private function newMessageLocalizer(): MessageLocalizer {
		return new class() implements MessageLocalizer {
			public function msg( $key, ...$params ): Message {
				return wfMessage( $key, ...$params );
			}
		};
	}

}
