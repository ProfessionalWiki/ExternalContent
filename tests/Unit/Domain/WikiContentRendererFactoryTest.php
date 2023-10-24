<?php

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\CodeRenderer;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\MarkdownRenderer;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\RendererConfig;
use ProfessionalWiki\ExternalContent\Domain\WikiContentRendererFactory;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\WikiContentRendererFactory
 */
class WikiContentRendererFactoryTest extends TestCase {

	public function testMarkdownFileAndConfigWithoutLanguageCreatesMarkdownRenderer(): void {
		$this->assertEquals(
			new MarkdownRenderer(),
			( new WikiContentRendererFactory() )->createContentRenderer(
				new RendererConfig(
					fileExtension: 'md',
					language: '',
					showLineNumbers: false,
					render: true
				)
			)
		);
	}

	public function testConfigWithoutLanguageCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: 'php', showLineNumbers: false ),
			( new WikiContentRendererFactory() )->createContentRenderer(
				new RendererConfig(
					fileExtension: 'php',
					language: '',
					showLineNumbers: false,
					render: false
				)
			)
		);
	}

	public function testConfigWithLanguageCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: 'php', showLineNumbers: false ),
			( new WikiContentRendererFactory() )->createContentRenderer(
				new RendererConfig(
					fileExtension: 'php',
					language: 'php',
					showLineNumbers: false,
					render: false
				)
			)
		);
	}

	public function testConfigWithLanguageAndLineNumbersCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: 'php', showLineNumbers: true ),
			( new WikiContentRendererFactory() )->createContentRenderer(
				new RendererConfig(
					fileExtension: 'php',
					language: 'php',
					showLineNumbers: true,
					render: false
				)
			)
		);
	}

	public function testConfigWithMarkdownLanguageCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: 'md', showLineNumbers: false ),
			( new WikiContentRendererFactory() )->createContentRenderer(
				new RendererConfig(
					fileExtension: 'md',
					language: 'md',
					showLineNumbers: false,
					render: false
				)
			)
		);
	}

}
