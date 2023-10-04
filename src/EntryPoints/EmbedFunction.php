<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\EntryPoints;

use Parser;
use ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter\CategoryUsageTracker;
use ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter\ParserFunctionEmbedPresenter;
use ProfessionalWiki\ExternalContent\EmbedExtensionFactory;
use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedRequestBuilder;

final class EmbedFunction {

	/**
	 * @param Parser $parser
	 * @param string ...$arguments
	 * @return array|string
	 */
	public function handleParserFunctionCall( Parser $parser, string ...$arguments ) {
		$presenter = new ParserFunctionEmbedPresenter(
			EmbedExtensionFactory::getInstance()->getMessageLocalizer(),
			new CategoryUsageTracker( $parser ),
			$parser->getOutput()
		);

		$useCase = EmbedExtensionFactory::getInstance()->newEmbedUseCaseForEmbedFunction( $presenter );

		$useCase->embed( EmbedRequestBuilder::argumentsToRequest( $arguments ) );

		return $presenter->getParserFunctionReturnValue();
	}

}
