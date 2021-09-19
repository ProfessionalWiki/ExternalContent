<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration;

use ProfessionalWiki\ExternalContent\Tests\TestEnvironment;

/**
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\BitbucketFunction
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\MediaWikiHooks
 * @covers \ProfessionalWiki\ExternalContent\Presentation\ParserFunctionEmbedPresenter
 * @covers \ProfessionalWiki\ExternalContent\EmbedExtensionFactory
 */
class BitbucketFunctionIntegrationTest extends EmbedIntegrationTestCase {

	public function testTodo(): void {
		$this->assertStringContainsString(
			'TODO bitbucket',
			TestEnvironment::instance()->parse( '{{#bitbucket:}}' )
		);
	}

}
