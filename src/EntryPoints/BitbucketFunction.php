<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\EntryPoints;

use Parser;
use ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter\CategoryUsageTracker;
use ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter\ParserFunctionEmbedPresenter;
use ProfessionalWiki\ExternalContent\Domain\ContentRendererFactory;
use ProfessionalWiki\ExternalContent\EmbedExtensionFactory;

final class BitbucketFunction {

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

		$renderer = ( new ContentRendererFactory() )->createContentRenderer( array_slice( $arguments, 1 ) );
		$parser->getOutput()->addModules( $renderer->getOutputModules() );
		$parser->getOutput()->addModuleStyles( $renderer->getOutputModuleStyles() );

		$useCase = EmbedExtensionFactory::getInstance()->newEmbedUseCaseForBitbucketFunction( $presenter, $renderer );

		$useCase->embed( $arguments[0] );

		return $presenter->getParserFunctionReturnValue();
	}

}
