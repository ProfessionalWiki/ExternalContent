<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration\EntryPoints;

use FileFetcher\StubFileFetcher;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\EmbedExtensionFactory;
use ProfessionalWiki\ExternalContent\Tests\TestEnvironment;

/**
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\EmbedFunction
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\MediaWikiHooks
 * @covers \ProfessionalWiki\ExternalContent\Presentation\ParserFunctionEmbedPresenter
 */
class EmbedFunctionIntegrationTest extends TestCase {

	// TODO: ensure used extension config is correct

	public function testHappyPath(): void {
		EmbedExtensionFactory::getInstance()->setFileFetcher( new StubFileFetcher( 'I am **bold**' ) );

		$this->assertStringContainsString(
			'<p>I am <strong>bold</strong></p>',
			TestEnvironment::instance()->parse( '{{#embed:https://example.com/KITTENS.md}}' )
		);
	}

}
