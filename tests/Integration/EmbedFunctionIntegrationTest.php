<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration;

use FileFetcher\InMemoryFileFetcher;
use FileFetcher\StubFileFetcher;
use ProfessionalWiki\ExternalContent\Tests\TestEnvironment;

/**
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\EmbedFunction
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\MediaWikiHooks
 * @covers \ProfessionalWiki\ExternalContent\Presentation\ParserFunctionEmbedPresenter
 * @covers \ProfessionalWiki\ExternalContent\EmbedExtensionFactory
 */
class EmbedFunctionIntegrationTest extends EmbedIntegrationTestCase {

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

		$this->assertStringNotContainsString(
			'I am <strong>bold</strong>', // TODO: check for error instead
			TestEnvironment::instance()->parse( '{{#embed:https://example.com/KITTENS.md}}' )
		);
	}

	public function testFileNotFound(): void {
		$this->extensionFactory->setFileFetcher( new InMemoryFileFetcher( [] ) );

		$this->assertStringNotContainsString(
			'I am <strong>bold</strong>', // TODO: check for error instead
			TestEnvironment::instance()->parse( '{{#embed:https://example.com/KITTENS.md}}' )
		);
	}

}
