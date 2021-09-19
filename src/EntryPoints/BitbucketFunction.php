<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\EntryPoints;

use Parser;
use ProfessionalWiki\ExternalContent\EmbedExtensionFactory;
use ProfessionalWiki\ExternalContent\Presentation\ParserFunctionEmbedPresenter;

final class BitbucketFunction {

	/**
	 * @param Parser $parser
	 * @param string ...$arguments
	 * @return array|string
	 */
	public function handleParserFunctionCall( Parser $parser, string ...$arguments ) {
		$presenter = new ParserFunctionEmbedPresenter( EmbedExtensionFactory::getInstance()->getMessageLocalizer() );

		$useCase = EmbedExtensionFactory::getInstance()->newEmbedUseCaseForBitbucketFunction( $presenter );

		$useCase->embed( $arguments[0] );

		return $presenter->getParserFunctionReturnValue();
	}

}
