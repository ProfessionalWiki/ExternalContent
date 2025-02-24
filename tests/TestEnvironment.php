<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests;

use MediaWiki\MediaWikiServices;
use Title;

class TestEnvironment {

	public static function instance(): self {
		return new self();
	}

	public function wikiUrl( string $pageName ): ?string {
		if ( is_string( getenv( 'PHPUNIT_WIKI_URL' ) ) ) {
			return getenv( 'PHPUNIT_WIKI_URL' ) . $pageName;
		}

		return null;
	}

	public function parse( string $textToParse, ?Title $contextPage = null ): string {
		return MediaWikiServices::getInstance()->getParser()
			->parse(
				$textToParse,
				$contextPage ?? Title::newFromText( 'ContextPage' ),
				new \ParserOptions( \User::newSystemUser( 'TestUser' ) )
			)->getText();
	}

}
