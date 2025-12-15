<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests;

use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Title\Title;
use MediaWiki\User\User;
use PHPUnit\Framework\TestCase;

class TestEnvironment extends TestCase {

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
		$contextPage = $contextPage ?? Title::newFromText( 'ContextPage' );
		$parserOutput = MediaWikiServices::getInstance()->getParser()
			->parse(
				$textToParse,
				$contextPage,
				$parserOptions
			);

		// getContentHolderText() is available in MediaWiki 1.43+
		if ( method_exists( $parserOutput, 'getContentHolderText' ) ) {
			return $parserOutput->runOutputPipeline( $parserOptions )
			->getContentHolderText();
		}
		// MediaWiki 1.44+ removed getText() in favor of getContentHolderText()
		return $parserOutput->getText();
	}

}
