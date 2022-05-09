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
		if ( $parser ) {
			if ( self::cacheIsDisabled() ) {
				$parser->getOutput()->updateCacheExpiry( 0 );
				// We only need to set the External Content cache expiry if it is lower than the sitewide expiry
			} elseif ( is_int( self::getCacheExpiry() ) && !self::hasReducedExpiry() ) {
				$parser->getOutput()->updateCacheExpiry( self::getCacheExpiry() );
			}
		}
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
	 * Compatibility shim for hasReducedExpiry() coming in 1.37
	 */
	private static function hasReducedExpiry(): bool {
		$parserCacheExpireTime = MediaWikiServices::getInstance()->getMainConfig()->get( 'ParserCacheExpireTime' );
		return self::getCacheExpiry() < $parserCacheExpireTime;
	}

	private static function cacheIsDisabled(): bool {
		return MediaWikiServices::getInstance()->getMainConfig()->get( 'ExternalContentDisableCache' );
	}

	private static function getCacheExpiry(): int {
		return MediaWikiServices::getInstance()->getMainConfig()->get( 'ExternalContentDefaultExpiry' );
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
