<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration;

use FileFetcher\InMemoryFileFetcher;
use FileFetcher\StubFileFetcher;
use MediaWiki\MediaWikiServices;
use ProfessionalWiki\ExternalContent\Tests\TestEnvironment;

/**
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\EmbedFunction
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\MediaWikiHooks
 * @covers \ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter\ParserFunctionEmbedPresenter
 * @covers \ProfessionalWiki\ExternalContent\EmbedExtensionFactory
 * @covers \ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter\CategoryUsageTracker
 */
class EmbedFunctionIntegrationTest extends ExternalContentIntegrationTestCase {

	public function testHappyPath(): void {
		$this->extensionFactory->setFileFetcher( new StubFileFetcher( 'I am **bold**' ) );

		$this->assertStringContainsString(
			'<p>I am <strong>bold</strong></p>',
			TestEnvironment::instance()->parse( '{{#embed:https://example.com/KITTENS.md}}' )
		);
	}

	public function testForbiddenDomain(): void {
		$this->extensionFactory->setFileFetcher( new StubFileFetcher( 'I am **bold**' ) );
		$this->extensionFactory->setDomainWhitelist( 'www.professional.wiki' );

		$this->assertStringContainsString(
			'Embedding files from this domain is not allowed',
			TestEnvironment::instance()->parse( '{{#embed:https://example.com/KITTENS.md}}' )
		);
	}

	public function testFileNotFound(): void {
		$this->extensionFactory->setFileFetcher( new InMemoryFileFetcher( [] ) );

		$this->assertStringContainsString(
			'Could not retrieve file',
			TestEnvironment::instance()->parse( '{{#embed:https://example.com/KITTENS.md}}' )
		);
	}

	public function testUsageIsTracked(): void {
		$this->extensionFactory->setFileFetcher( new InMemoryFileFetcher( [] ) );

		$parser = MediaWikiServices::getInstance()->getParser();

		$parser->parse(
			'{{#embed:https://example.com/KITTENS.md}}',
			\Title::newFromText( 'EmbedFunctionIntegrationTest' ),
			new \ParserOptions( \User::newSystemUser( 'TestUser' ) )
		)->getText();

		// Since the category name depends on the wiki language, we need to skip this test when it is not English.
		if ( MediaWikiServices::getInstance()->getContentLanguage()->getCode() === 'en' ) {
			$this->assertSame(
				[ 'Pages_with_external_content' => '', 'Pages_with_broken_external_content' => '' ],
				$parser->getOutput()->getCategories()
			);
		}

		$this->assertCount( 2, $parser->getOutput()->getCategories() );
	}

	public function testGitHubNormalization(): void {
		$this->extensionFactory->setFileFetcher( new InMemoryFileFetcher( [
			'https://raw.githubusercontent.com/ProfessionalWiki/ExternalContent/master/README.md' => 'How convenient'
		] ) );

		$this->assertStringContainsString(
			'How convenient',
			TestEnvironment::instance()->parse( '{{#embed:https://github.com/ProfessionalWiki/ExternalContent}}' )
		);
	}

	public function testRelativeLink(): void {
		$this->extensionFactory->setFileFetcher(
			new InMemoryFileFetcher( [
				'https://example.com/foo.md' => '[bar link](bar.md)'
			] )
		);

		$this->assertStringContainsString(
			'<a href="https://example.com/bar.md">bar link</a>',
			TestEnvironment::instance()->parse(
				'{{#embed:https://example.com/foo.md}}'
			)
		);
	}

	public function testRelativeLinkOnNormalizedUrl(): void {
		$this->extensionFactory->setFileFetcher(
			new InMemoryFileFetcher( [
				'https://raw.githubusercontent.com/ProfessionalWiki/ExternalContent/master/foo.md' => '[bar link](bar.md)'
			] )
		);

		$this->assertStringContainsString(
			'<a href="https://github.com/ProfessionalWiki/ExternalContent/blob/master/bar.md">bar link</a>',
			TestEnvironment::instance()->parse(
				'{{#embed:https://github.com/ProfessionalWiki/ExternalContent/blob/master/foo.md}}'
			)
		);
	}

	public function testRelativeLinkOnShortGitHubUrl(): void {
		$this->extensionFactory->setFileFetcher(
			new InMemoryFileFetcher( [
				'https://raw.githubusercontent.com/ProfessionalWiki/ExternalContent/master/README.md' => '[bar link](bar.md)'
			] )
		);

		$this->assertStringContainsString(
			'<a href="https://github.com/ProfessionalWiki/ExternalContent/blob/master/bar.md">bar link</a>',
			TestEnvironment::instance()->parse(
				'{{#embed:https://github.com/ProfessionalWiki/ExternalContent}}'
			)
		);
	}

}
