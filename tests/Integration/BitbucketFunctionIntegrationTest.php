<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration;

use FileFetcher\InMemoryFileFetcher;
use FileFetcher\StubFileFetcher;
use ProfessionalWiki\ExternalContent\Tests\TestEnvironment;

/**
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\BitbucketFunction
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\MediaWikiHooks
 * @covers \ProfessionalWiki\ExternalContent\Adapters\ParserFunctionEmbedPresenter
 * @covers \ProfessionalWiki\ExternalContent\EmbedExtensionFactory
 */
class BitbucketFunctionIntegrationTest extends ExternalContentIntegrationTestCase {

	public function testHappyPath(): void {
		$this->extensionFactory->setFileFetcher( new InMemoryFileFetcher( [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md' => 'I am **bold**'
		] ) );

		$this->assertStringContainsString(
			'<p>I am <strong>bold</strong></p>',
			TestEnvironment::instance()->parse( '{{#bitbucket:https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse}}' )
		);
	}

	public function testInvalidBitbucketUrl(): void {
		$this->extensionFactory->setFileFetcher( new StubFileFetcher( 'I am **bold**' ) );

		$this->assertStringContainsString(
			'Not a valid Bitbucket URL',
			TestEnvironment::instance()->parse( '{{#bitbucket:https://example.com/KITTENS.md}}' )
		);
	}

}
