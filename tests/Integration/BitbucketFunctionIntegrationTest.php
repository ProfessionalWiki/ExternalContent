<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration;

use MediaWiki\MediaWikiServices;
use PHPUnit\Framework\TestCase;
use User;

/**
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\BitbucketFunction
 */
class BitbucketFunctionIntegrationTest extends TestCase {

	private const PAGE_TITLE = 'ContextPageTitle';

	private function parse( string $textToParse ): string {
		return MediaWikiServices::getInstance()->getParser()
			->parse(
				$textToParse,
				\Title::newFromText( self::PAGE_TITLE ),
				new \ParserOptions( User::newSystemUser( 'TestUser' ) )
			)->getText();
	}

	public function testTodo(): void {
		$this->assertStringContainsString(
			'TODO',
			$this->parse( '{{#bitbucket:}}' )
		);
	}

}
