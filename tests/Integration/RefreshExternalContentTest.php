<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration;

use MediaWiki\MainConfigNames;
use MediaWiki\Tests\Maintenance\MaintenanceBaseTestCase;
use ProfessionalWiki\ExternalContent\Maintenance\RefreshExternalContent;
use ProfessionalWiki\ExternalContent\Tests\TestFactory;
use Wikimedia\Timestamp\ConvertibleTimestamp;

require_once __DIR__ . '/../../maintenance/RefreshExternalContent.php';

/**
 * Needs the database: the script finds pages through their categories and invalidates them via their touched timestamp.
 *
 * @covers \ProfessionalWiki\ExternalContent\Maintenance\RefreshExternalContent
 * @group Database
 */
class RefreshExternalContentTest extends MaintenanceBaseTestCase {

	private const CREATION_TIME = '20250101000000';

	protected function getMaintenanceClass(): string {
		return RefreshExternalContent::class;
	}

	protected function setUp(): void {
		parent::setUp();

		TestFactory::newTestInstance();
	}

	public function testInvalidatesOnlyPagesThatEmbedExternalContent(): void {
		$embeddingPage = $this->createPage( 'Embedding page', '{{#embed:https://example.com/README.md}}' );
		$otherEmbeddingPage = $this->createPage( 'Other embedding page', '{{#embed:https://example.com/README.md}}' );
		$plainPage = $this->createPage( 'Plain page', 'No external content' );

		$this->maintenance->execute();

		$this->assertGreaterThan( self::CREATION_TIME, $this->getTouched( $embeddingPage ) );
		$this->assertGreaterThan( self::CREATION_TIME, $this->getTouched( $otherEmbeddingPage ) );
		$this->assertSame( self::CREATION_TIME, $this->getTouched( $plainPage ) );
	}

	private function createPage( string $title, string $wikitext ): int {
		ConvertibleTimestamp::setFakeTime( self::CREATION_TIME );
		$pageId = $this->insertPage( $title, $wikitext )['id'];
		ConvertibleTimestamp::setFakeTime( false );

		return $pageId;
	}

	private function getTouched( int $pageId ): string {
		return $this->getServiceContainer()->getWikiPageFactory()->newFromID( $pageId )->getTouched();
	}

	public function testInvalidatesPagesThatEmbedExternalContentOnNonEnglishWiki(): void {
		$this->overrideConfigValue( MainConfigNames::LanguageCode, 'de' );
		$embeddingPage = $this->createPage( 'Embedding page', '{{#embed:https://example.com/README.md}}' );

		$this->maintenance->execute();

		$this->assertGreaterThan( self::CREATION_TIME, $this->getTouched( $embeddingPage ) );
	}

	public function testLeavesPagesUntouchedWhenNoPageEmbedsExternalContent(): void {
		$plainPage = $this->createPage( 'Plain page', 'No external content' );

		$this->maintenance->execute();

		$this->assertSame( self::CREATION_TIME, $this->getTouched( $plainPage ) );
	}

}
