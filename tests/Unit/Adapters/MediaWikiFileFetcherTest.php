<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Adapters;

use FileFetcher\FileFetchingException;
use MediaWiki\Http\HttpRequestFactory;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Adapters\MediaWikiFileFetcher;

/**
 * @covers \ProfessionalWiki\ExternalContent\Adapters\MediaWikiFileFetcher
 */
class MediaWikiFileFetcherTest extends TestCase {

	public function testWhenRequestFactoryReturnsNull_exceptionIsThrown(): void {
		$requestFactory = $this->createMock( HttpRequestFactory::class );
		$requestFactory->method( 'get' )
			->with( $this->equalTo( 'https://example.com' ) )
			->willReturn( null );

		$this->expectException( FileFetchingException::class );
		( new MediaWikiFileFetcher( $requestFactory ) )->fetchFile( 'https://example.com' );
	}

	public function testWhenRequestFactoryReturnsString_itIsReturned(): void {
		$requestFactory = $this->createMock( HttpRequestFactory::class );
		$requestFactory->method( 'get' )
			->with( $this->equalTo( 'https://example.com' ) )
			->willReturn( '~=[,,_,,]:3' );

		$this->assertSame(
			'~=[,,_,,]:3',
			( new MediaWikiFileFetcher( $requestFactory ) )->fetchFile( 'https://example.com' )
		);
	}

}
