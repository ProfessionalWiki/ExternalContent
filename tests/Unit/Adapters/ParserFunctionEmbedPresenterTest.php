<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Adapters;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter\ParserFunctionEmbedPresenter;
use ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter\UsageTracker;
use ProfessionalWiki\ExternalContent\Tests\TestFactory;

/**
 * @covers \ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter\ParserFunctionEmbedPresenter
 */
class ParserFunctionEmbedPresenterTest extends TestCase {

	public function testShowErrorBuildsErrorMessageKey(): void {
		$presenter = new ParserFunctionEmbedPresenter(
			TestFactory::newTestInstance()->newTestMessageLocalizer(),
			$this->createMock( UsageTracker::class )
		);

		$presenter->showError( 'my-error' );

		$this->assertStringContainsString(
			'test-external-content-my-error',
			$presenter->getParserFunctionReturnValue()[0]
		);
	}

	public function testShowErrorBuildsErrorHtml(): void {
		$presenter = new ParserFunctionEmbedPresenter(
			TestFactory::newTestInstance()->newTestMessageLocalizer(),
			$this->createMock( UsageTracker::class )
		);

		$presenter->showError( 'my-error' );
		$parserFunctionReturnValue = $presenter->getParserFunctionReturnValue();

		$this->assertThat(
			$parserFunctionReturnValue[0],
			$this->logicalOr(
				$this->stringContains( '<div class="errorbox">' ),
				$this->stringContains( 'mw-message-box-error' ),
				$this->stringContains( 'cdx-message--error' )
			)
		);

		$this->assertTrue( $parserFunctionReturnValue['noparse'] );
		$this->assertTrue( $parserFunctionReturnValue['isHTML'] );
	}

	public function assertStringContainsEither( string $expected1, string $expected2, string $actual ): void {
		$this->assertTrue(
			str_contains( $actual, $expected1 ) || str_contains( $actual, $expected2 ),
			"Failed asserting that string contains either '{$expected1}' or '{$expected2}'"
		);
	}

	public function testContentIsAccessibleAsHtml(): void {
		$presenter = new ParserFunctionEmbedPresenter(
			TestFactory::newTestInstance()->newTestMessageLocalizer(),
			$this->createMock( UsageTracker::class )
		);

		$presenter->showContent( '<strong>Well hello there!</strong>' );
		$parserFunctionReturnValue = $presenter->getParserFunctionReturnValue();

		$this->assertStringContainsString(
			'<strong>Well hello there!</strong>',
			$parserFunctionReturnValue[0]
		);

		$this->assertTrue( $parserFunctionReturnValue['noparse'] );
		$this->assertTrue( $parserFunctionReturnValue['isHTML'] );
	}

}
