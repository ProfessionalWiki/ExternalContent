<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\EntryPoints;

use MediaWiki\MediaWikiServices;
use Parser;

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

}