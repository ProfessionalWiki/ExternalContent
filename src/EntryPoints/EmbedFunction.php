<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\EntryPoints;

use Parser;
use ProfessionalWiki\ExternalContent\Adapters\CategoryUsageTracker;
use ProfessionalWiki\ExternalContent\EmbedExtensionFactory;
use ProfessionalWiki\ExternalContent\Adapters\ParserFunctionEmbedPresenter;

final class EmbedFunction {

	/**
	 * @param Parser $parser
	 * @param string ...$arguments
	 * @return array|string
	 */
	public function handleParserFunctionCall( Parser $parser, string ...$arguments ) {
		$presenter = new ParserFunctionEmbedPresenter(
			EmbedExtensionFactory::getInstance()->getMessageLocalizer(),
			new CategoryUsageTracker( $parser )
		);

		$useCase = EmbedExtensionFactory::getInstance()->newEmbedUseCaseForEmbedFunction( $presenter );

		$useCase->embed( $arguments[0] );

		return $presenter->getParserFunctionReturnValue();
	}

}
