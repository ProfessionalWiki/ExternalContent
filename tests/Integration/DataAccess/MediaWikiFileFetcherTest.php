<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration\DataAccess;

use FileFetcher\FileFetchingException;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\DataAccess\MediaWikiFileFetcher;
use ProfessionalWiki\ExternalContent\Tests\TestEnvironment;

/**
 * @covers \ProfessionalWiki\ExternalContent\DataAccess\MediaWikiFileFetcher
 */
class MediaWikiFileFetcherTest extends TestCase {

	public function testUnreachableFileResultsInException(): void {
		$this->expectException( FileFetchingException::class );
		( new MediaWikiFileFetcher() )->fetchFile( '404' );
	}

	public function testCanGetLocalFile(): void {
		$specialVersionUrl = TestEnvironment::instance()->wikiUrl( 'Special:Version' );

		if ( $specialVersionUrl === null ) {
			$this->markTestSkipped( 'No reachable Special:Version' );
		}

		$this->assertStringContainsString(
			'https://professional.wiki/',
			( new MediaWikiFileFetcher() )->fetchFile( $specialVersionUrl )
		);
	}

}
