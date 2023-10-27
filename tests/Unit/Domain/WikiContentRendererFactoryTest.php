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
					showSpecificLines: '',
					render: true,
					showEditButton: false
				)
			)
		);
	}

	public function testConfigWithoutLanguageCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: 'php', showLineNumbers: false, showSpecificLines: '', showEditButton: false ),
			( new WikiContentRendererFactory() )->createContentRenderer(
				new RendererConfig(
					fileExtension: 'php',
					language: '',
					showLineNumbers: false,
					showSpecificLines: '',
					render: false,
					showEditButton: false
				)
			)
		);
	}

	public function testConfigWithLanguageCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: 'php', showLineNumbers: false, showSpecificLines: '', showEditButton: false ),
			( new WikiContentRendererFactory() )->createContentRenderer(
				new RendererConfig(
					fileExtension: 'php',
					language: 'php',
					showLineNumbers: false,
					showSpecificLines: '',
					render: false,
					showEditButton: false
				)
			)
		);
	}

	public function testConfigWithLanguageAndLineNumbersCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: 'php', showLineNumbers: true, showSpecificLines: '', showEditButton: false ),
			( new WikiContentRendererFactory() )->createContentRenderer(
				new RendererConfig(
					fileExtension: 'php',
					language: 'php',
					showLineNumbers: true,
					showSpecificLines: '',
					render: false,
					showEditButton: false
				)
			)
		);
	}

	public function testConfigWithMarkdownLanguageCreatesCodeRenderer(): void {
		$this->assertEquals(
			new CodeRenderer( language: 'md', showLineNumbers: false, showSpecificLines: '', showEditButton: false ),
			( new WikiContentRendererFactory() )->createContentRenderer(
				new RendererConfig(
					fileExtension: 'md',
					language: 'md',
					showLineNumbers: false,
					showSpecificLines: '',
					render: false,
					showEditButton: false
				)
			)
		);
	}

}
