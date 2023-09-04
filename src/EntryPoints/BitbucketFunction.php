<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\EntryPoints;

use Parser;
use ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter\CategoryUsageTracker;
use ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter\ParserFunctionEmbedPresenter;
use ProfessionalWiki\ExternalContent\EmbedExtensionFactory;

final class BitbucketFunction {

	/**
	 * @param Parser $parser
	 * @param string ...$arguments
	 * @return array|string
	 */
	public function handleParserFunctionCall( Parser $parser, string ...$arguments ) {
		// TODO: shouldn't be here - probably load it conditionally from JS
		$parser->getOutput()->addModules( [ 'ext.external-content.highlightjs' ] );

		$presenter = new ParserFunctionEmbedPresenter(
			EmbedExtensionFactory::getInstance()->getMessageLocalizer(),
			new CategoryUsageTracker( $parser )
		);

		$useCase = EmbedExtensionFactory::getInstance()->newEmbedUseCaseForBitbucketFunction( $presenter );

		$useCase->embed( $arguments[0] );

		return $presenter->getParserFunctionReturnValue();
	}

}
