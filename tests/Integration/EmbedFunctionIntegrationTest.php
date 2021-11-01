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

}
