<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\EntryPoints;

use ContentHandler;
use MediaWiki\MediaWikiServices;
use Parser;
use ParserOutput;
use ProfessionalWiki\ExternalContent\EmbedExtensionFactory;
use SearchEngine;
use WikiPage;

final class MediaWikiHooks {

	public static function onParserFirstCallInit( Parser $parser ): void {
		if ( self::embedFunctionIsEnabled() ) {
			$parser->setFunctionHook(
				'embed',
				fn( Parser $parser, string ...$arguments )
					=> ( new EmbedFunction() )->handleParserFunctionCall( $parser, ...$arguments )
			);
		}

		if ( self::bitbucketFunctionIsEnabled() ) {
			$parser->setFunctionHook(
				'bitbucket',
				fn( Parser $parser, string ...$arguments )
					=> ( new BitbucketFunction() )->handleParserFunctionCall( $parser, ...$arguments )
			);
		}
	}

	/**
	 * @psalm-suppress MixedInferredReturnType
	 * @psalm-suppress MixedReturnStatement
	 */
	private static function embedFunctionIsEnabled(): bool {
		return MediaWikiServices::getInstance()->getMainConfig()->get( 'ExternalContentEnableEmbedFunction' );
	}

	/**
	 * @psalm-suppress MixedInferredReturnType
	 * @psalm-suppress MixedReturnStatement
	 */
	private static function bitbucketFunctionIsEnabled(): bool {
		return MediaWikiServices::getInstance()->getMainConfig()->get( 'ExternalContentEnableBitbucketFunction' );
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function onParserTestGlobals( array &$globals ): void {
		foreach ( EmbedExtensionFactory::DEFAULT_CONFIG as $key => $value ) {
			// The globals we get here do not include values from LocalSettings.php yet
			if ( !array_key_exists( $key, $globals ) ) {
				$globals[$key] = $value;
			}
		}
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function onSearchDataForIndex( array &$fields, ContentHandler $handler, WikiPage $page, ParserOutput $output, SearchEngine $engine ): void {
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function onSearchIndexFields( array &$fields, SearchEngine $engine ): void {
	}

}
