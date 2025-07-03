<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration;

use CommentStoreComment;
use FileFetcher\StubFileFetcher;
use MediaWiki\Title\Title;
use MediaWiki\User\User;
use WikiPage;

/**
 * @group Database
 *
 * @covers \ProfessionalWiki\ExternalContent\EmbedExtensionFactory
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\MediaWikiHooks
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\EmbedFunction
 */
class EmbedFunctionSystemTest extends ExternalContentIntegrationTestCase {

	private const PAGE_NAME = 'ExternalContent EmbedFunctionTest';

	protected function setUp(): void {
		parent::setUp();

		$this->extensionFactory->setFileFetcher( new StubFileFetcher( 'I am **bold**' ) );

		$this->createTestPage();
	}

	private function createTestPage(): void {
		$this->createPage( self::PAGE_NAME, '{{#embed:https://example.com/KITTENS.md}}' );
	}

	public function testSearchIndexText(): void {
		$this->assertSame(
			'{{#embed:https://example.com/KITTENS.md}}', // TODO
			$this->getWikiPage()->getContent()->getTextForSearchIndex()
		);
	}

	private function getWikiPage(): WikiPage {
		return ( new WikiPage( Title::newFromText( self::PAGE_NAME ) ) );
	}

	public function testTrackingCategories(): void {
		$this->assertSame(
			[ 'Category:Pages_with_external_content' => self::PAGE_NAME ],
			$this->getWikiPage()->getTitle()->getParentCategories()
		);
	}

	private function createPage( string $title, string $content ): WikiPage {
		$titleObject = Title::newFromText( $title );

		return $this->createPageWithContent(
			$title,
			\ContentHandler::makeContent( $content, $titleObject )
		);
	}

	private function createPageWithContent( string $title, \Content $content ): WikiPage {
		$titleObject = Title::newFromText( $title );
		$page = new WikiPage( $titleObject );

		$updater = $page->newPageUpdater( User::newSystemUser( 'TestUser' ) );
		$updater->setContent( 'main', $content );
		$updater->saveRevision( CommentStoreComment::newUnsavedComment( __CLASS__ . ' creating page ' . $title ) );

		return $page;
	}

}
