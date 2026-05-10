<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests;

use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

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
		$parserOptions = new ParserOptions( User::newSystemUser( 'TestUser' ) );
		return MediaWikiServices::getInstance()->getParser()
			->parse(
				$textToParse,
				$contextPage ?? Title::newFromText( 'ContextPage' ),
				$parserOptions
			)->runOutputPipeline( $parserOptions, [] )->getRawText();
	}

}
