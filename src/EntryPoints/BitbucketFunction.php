<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\EntryPoints;

use Parser;

final class BitbucketFunction {

	/**
	 * @param Parser $parser
	 * @param string ...$arguments
	 * @return array|string
	 */
	public function handleParserFunctionCall( Parser $parser, string ...$arguments ) {
		return 'TODO bitbucket';
	}

}
