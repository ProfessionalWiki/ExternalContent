<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent;

use FileFetcher\FileFetcher;
use MediaWiki\MediaWikiServices;
use Message;
use MessageLocalizer;
use ProfessionalWiki\ExternalContent\Adapters\FileFetcher\DomainCredentials;
use ProfessionalWiki\ExternalContent\Adapters\FileFetcher\MediaWikiFileFetcher;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\MarkdownRenderer;
use ProfessionalWiki\ExternalContent\Domain\UrlNormalizer\BitbucketUrlNormalizer;
use ProfessionalWiki\ExternalContent\Domain\UrlNormalizer\NullUrlNormalizer;
use ProfessionalWiki\ExternalContent\Domain\UrlValidator;
use ProfessionalWiki\ExternalContent\Domain\UrlValidator\CompoundUrlValidator;
use ProfessionalWiki\ExternalContent\Domain\UrlValidator\FileExtensionUrlValidator;
use ProfessionalWiki\ExternalContent\Domain\UrlValidator\WhitelistedDomainUrlValidator;
use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedPresenter;
use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedUseCase;

class EmbedExtensionFactory {

	public const DEFAULT_CONFIG = [
		'wgLanguageCode' => 'en',
		'ExternalContentDomainWhitelist' => [],
		'ExternalContentFileExtensionWhitelist' => [ 'md' ],
		'wgExternalContentEnableEmbedFunction' => true,
		'wgExternalContentEnableBitbucketFunction' => true,
	];

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
		return new CompoundUrlValidator(
			$this->getDomainValidator(),
			$this->getFileExtensionValidator(),
		);
	}

	private function getDomainValidator(): UrlValidator {
		/** @var array<int, string> */
		$domains = MediaWikiServices::getInstance()->getMainConfig()->get( 'ExternalContentDomainWhitelist' );

		$this->domainWhitelist ??= $domains;

		return new WhitelistedDomainUrlValidator( ...$this->domainWhitelist );
	}

	private function getFileExtensionValidator(): UrlValidator {
		/** @var array<int, string> */
		$extensions = MediaWikiServices::getInstance()->getMainConfig()->get( 'ExternalContentFileExtensionWhitelist' );
		return new FileExtensionUrlValidator( ...$extensions );
	}

	private function getFileFetcher(): FileFetcher {
		$this->fileFetcher ??= new MediaWikiFileFetcher(
			MediaWikiServices::getInstance()->getHttpRequestFactory(),
			$this->getDomainCredentials()
		);

		return $this->fileFetcher;
	}

	private function getDomainCredentials(): DomainCredentials {
		/** @var array<string, string[]> */
		$credentials = MediaWikiServices::getInstance()->getMainConfig()->get( 'ExternalContentBasicAuthCredentials' );
		return DomainCredentials::newFromArray( $credentials );
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
