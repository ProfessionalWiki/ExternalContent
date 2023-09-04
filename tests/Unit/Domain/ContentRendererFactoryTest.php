<?php

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\CodeRenderer;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\MarkdownRenderer;
use ProfessionalWiki\ExternalContent\Domain\ContentRendererFactory;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\ContentRendererFactory
 */
class ContentRendererFactoryTest extends TestCase {

	public function testNoConfigCreatesMarkdownRenderer(): void {
		$this->assertEquals(
			new MarkdownRenderer(),
			( new ContentRendererFactory() )->createContentRenderer( [] )
		);
	}

	public function testInvalidConfigCreatesMarkdownRenderer(): void {
		$this->assertEquals(
			new MarkdownRenderer(),
			( new ContentRendererFactory() )->createContentRenderer( [ 'foo=bar' ] )
		);
	}

	public function testConfigWithMissingLanguageCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: '', showLineNumbers: false ),
			( new ContentRendererFactory() )->createContentRenderer( [ 'lang' ] )
		);
	}

	public function testConfigWithEmptyLanguageCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: '', showLineNumbers: false ),
			( new ContentRendererFactory() )->createContentRenderer( [ 'lang=' ] )
		);
	}

	public function testConfigWithLanguageCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: 'php', showLineNumbers: false ),
			( new ContentRendererFactory() )->createContentRenderer( [ 'lang=php' ] )
		);
	}

	public function testConfigWithLanguageAndLineNumbersCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: 'php', showLineNumbers: true ),
			( new ContentRendererFactory() )->createContentRenderer( [ 'lang=php', 'line' ] )
		);
	}

	public function testConfigWithLanguageAndEmptyLineNumbersCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: 'php', showLineNumbers: true ),
			( new ContentRendererFactory() )->createContentRenderer( [ 'lang=php', 'line=' ] )
		);
	}

	public function testConfigWithLanguageAndInvalidLineNumbersCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: 'php', showLineNumbers: true ),
			( new ContentRendererFactory() )->createContentRenderer( [ 'lang=php', 'line=foo' ] )
		);
	}

}
