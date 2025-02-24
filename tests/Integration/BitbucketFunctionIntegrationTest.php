<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration;

use FileFetcher\InMemoryFileFetcher;
use FileFetcher\StubFileFetcher;
use ProfessionalWiki\ExternalContent\Tests\TestEnvironment;

/**
 * @group Database
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\BitbucketFunction
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\MediaWikiHooks
 * @covers \ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter\ParserFunctionEmbedPresenter
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

	public function testRelativeLink(): void {
		$this->extensionFactory->setFileFetcher(
			new InMemoryFileFetcher( [
				'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/foo.md' => '[bar link](bar.md)'
			] )
		);

		$this->assertStringContainsString(
			'<a href="https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/bar.md">bar link</a>',
			TestEnvironment::instance()->parse(
				'{{#bitbucket:https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/foo.md}}'
			)
		);
	}

	public function testRelativeLinkOnNormalizedUrl(): void {
		$this->extensionFactory->setFileFetcher(
			new InMemoryFileFetcher( [
				'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/foo.md' => '[bar link](bar.md)'
			] )
		);

		$this->assertStringContainsString(
			'<a href="https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/bar.md">bar link</a>',
			TestEnvironment::instance()->parse(
				'{{#bitbucket:https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/foo.md}}'
			)
		);
	}

	public function testRelativeLinkOnAutoReadmeUrl(): void {
		$this->extensionFactory->setFileFetcher(
			new InMemoryFileFetcher( [
				'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md' => '[bar link](bar.md)'
			] )
		);

		$this->assertStringContainsString(
			'<a href="https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/bar.md">bar link</a>',
			TestEnvironment::instance()->parse(
				'{{#bitbucket:https://git.example.com/projects/KNOW/repos/fluffy-kittens}}'
			)
		);
	}

}
