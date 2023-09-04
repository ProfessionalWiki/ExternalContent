<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\UseCases\Embed;

use FileFetcher\InMemoryFileFetcher;
use FileFetcher\SpyingFileFetcher;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;
use ProfessionalWiki\ExternalContent\Domain\UrlNormalizer;
use ProfessionalWiki\ExternalContent\Domain\UrlNormalizer\NullUrlNormalizer;
use ProfessionalWiki\ExternalContent\Domain\UrlValidator;
use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedUseCase;

/**
 * @covers \ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedUseCase
 * @covers \ProfessionalWiki\ExternalContent\Domain\UrlNormalizer\NullUrlNormalizer
 */
class EmbedUseCaseTest extends TestCase {

	private SpyEmbedPresenter $presenter;
	private UrlValidator $urlValidator;
	private UrlNormalizer $urlNormalizer;
	private SpyingFileFetcher $fileFetcher;
	private ContentRenderer $contentRenderer;

	private const KNOWN_FILE_URL = 'https://example.com/Fluff.md';
	private const KNOWN_FILE_CONTENT = '~=[,,_,,]:3';

	protected function setUp(): void {
		$this->presenter = new SpyEmbedPresenter();
		$this->urlValidator = new StubUrlValidator( null );
		$this->urlNormalizer = new NullUrlNormalizer();
		$this->fileFetcher = new SpyingFileFetcher( new InMemoryFileFetcher( [
			self::KNOWN_FILE_URL => self::KNOWN_FILE_CONTENT
		] ) );
		$this->contentRenderer = new NullContentRenderer();
	}

	private function newUseCase(): EmbedUseCase {
		return new EmbedUseCase(
			$this->presenter,
			$this->urlValidator,
			$this->urlNormalizer,
			$this->fileFetcher,
			$this->contentRenderer
		);
	}

	public function testInvalidUrlResultsInPresentedError(): void {
		$this->urlValidator = new StubUrlValidator( 'not-fluff-enough' );

		$this->newUseCase()->embed( self::KNOWN_FILE_URL );

		$this->assertSame( [ 'not-fluff-enough' ], $this->presenter->errors );
		$this->assertNull( $this->presenter->content );
	}

	public function testValidUrlResultsInPresenterContent(): void {
		$this->newUseCase()->embed( self::KNOWN_FILE_URL );

		$this->assertSame( self::KNOWN_FILE_CONTENT, $this->presenter->content );
		$this->assertSame( [], $this->presenter->errors );
	}

	public function testFileFetchingErrorResultsInPresentedError(): void {
		$this->newUseCase()->embed( 'https://example.com/NotFluff.md' );

		$this->assertSame( [ 'fetch-error' ], $this->presenter->errors );
		$this->assertNull( $this->presenter->content );
	}

	public function testFetchesNormalizedUrl(): void {
		$this->urlNormalizer = new class() implements UrlNormalizer {
			public function fullNormalize( string $url ): string {
				return $url . 'README.md';
			}

			public function viewLevelNormalize( string $url ): string {
				return $url . 'README.md';
			}
		};

		$this->newUseCase()->embed( 'https://example.com/path/' );

		$this->assertSame(
			[ 'https://example.com/path/README.md' ],
			$this->fileFetcher->getFetchedUrls()
		);
	}

	public function testPresentsRenderedContent(): void {
		$this->contentRenderer = new class() implements ContentRenderer {
			public function render( string $content, string $contentUrl ): string {
				return $content . ' from ' . $contentUrl;
			}

			public function getOutputModules(): array {
				return [];
			}

			public function getOutputModuleStyles(): array {
				return [];
			}
		};

		$this->newUseCase()->embed( self::KNOWN_FILE_URL );

		$this->assertSame(
			self::KNOWN_FILE_CONTENT . ' from ' . self::KNOWN_FILE_URL,
			$this->presenter->content
		);
		$this->assertSame( [], $this->presenter->errors );
	}

	public function testPresentsErrorOnUrlNormalizerException(): void {
		$this->urlNormalizer = new class() implements UrlNormalizer {
			public function fullNormalize( string $url ): string {
				throw new \RuntimeException( 'url-not-fluff-enough' );
			}

			public function viewLevelNormalize( string $url ): string {
				throw new \RuntimeException( 'url-not-fluff-enough' );
			}
		};

		$this->newUseCase()->embed( self::KNOWN_FILE_URL );

		$this->assertSame( [ 'url-not-fluff-enough' ], $this->presenter->errors );
		$this->assertNull( $this->presenter->content );
	}

}
