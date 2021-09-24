<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\EntryPoints;

use Parser;

final class MediaWikiHooks {

	public static function onParserFirstCallInit( Parser $parser ): void {
		$parser->setFunctionHook(
			'embed',
			fn( Parser $parser, string ...$arguments )
				=> ( new EmbedFunction() )->handleParserFunctionCall( $parser, ...$arguments )
		);

		$parser->setFunctionHook(
			'bitbucket',
			fn( Parser $parser, string ...$arguments )
				=> ( new BitbucketFunction() )->handleParserFunctionCall( $parser, ...$arguments )
		);
	}

}
