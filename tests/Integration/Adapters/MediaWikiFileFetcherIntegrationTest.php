<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration\Adapters;

use FileFetcher\FileFetchingException;
use MediaWiki\MediaWikiServices;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Adapters\MediaWikiFileFetcher;
use ProfessionalWiki\ExternalContent\Tests\TestEnvironment;

/**
 * @covers \ProfessionalWiki\ExternalContent\Adapters\MediaWikiFileFetcher
 */
class MediaWikiFileFetcherIntegrationTest extends TestCase {

	public function testUnreachableFileResultsInException(): void {
		$this->expectException( FileFetchingException::class );
		( new MediaWikiFileFetcher( MediaWikiServices::getInstance()->getHttpRequestFactory() ) )->fetchFile( '404' );
	}

	public function testCanGetLocalFile(): void {
		$specialVersionUrl = TestEnvironment::instance()->wikiUrl( 'Special:Version' );

		if ( $specialVersionUrl === null ) {
			$this->markTestSkipped( 'No reachable Special:Version' );
		}

		$this->assertStringContainsString(
			'https://professional.wiki/',
			( new MediaWikiFileFetcher( MediaWikiServices::getInstance()->getHttpRequestFactory() ) )->fetchFile( $specialVersionUrl )
		);
	}

}