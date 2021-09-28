<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Adapters;

use FileFetcher\FileFetchingException;
use MediaWiki\Http\HttpRequestFactory;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Adapters\BasicAuthCredentials;
use ProfessionalWiki\ExternalContent\Adapters\DomainCredentials;
use ProfessionalWiki\ExternalContent\Adapters\MediaWikiFileFetcher;

/**
 * @covers \ProfessionalWiki\ExternalContent\Adapters\MediaWikiFileFetcher
 * @covers \ProfessionalWiki\ExternalContent\Adapters\DomainCredentials
 * @covers \ProfessionalWiki\ExternalContent\Adapters\BasicAuthCredentials
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

	public function testRightCredentialsArePassed(): void {
		$requestFactory = $this->createMock( HttpRequestFactory::class );
		$requestFactory->expects( $this->once() )
			->method( 'get' )
			->with(
				$this->equalTo( 'https://example.com' ),
				$this->equalTo( [
					'username' => 'FooUser',
					'password' => 'BarPassword'
				] )
			)->willReturn( '~=[,,_,,]:3' );

		$domainCredentials = new DomainCredentials();
		$domainCredentials->add( 'another-domain.com', new BasicAuthCredentials( 'Wrong', 'Wrong' ) );
		$domainCredentials->add( 'example.com', new BasicAuthCredentials( 'FooUser', 'BarPassword' ) );
		$domainCredentials->add( 'example.hax', new BasicAuthCredentials( 'Wrong', 'Wrong' ) );

		( new MediaWikiFileFetcher( $requestFactory, $domainCredentials ) )->fetchFile( 'https://example.com' );
	}

	public function testNoCredentialsArePassedWhenDomainNotInCredentialList(): void {
		$requestFactory = $this->createMock( HttpRequestFactory::class );
		$requestFactory->expects( $this->once() )
			->method( 'get' )
			->with(
				$this->equalTo( 'https://example.com' ),
				$this->equalTo( [] )
			)->willReturn( '~=[,,_,,]:3' );

		$domainCredentials = new DomainCredentials();
		$domainCredentials->add( 'example.hax', new BasicAuthCredentials( 'Wrong', 'Wrong' ) );

		( new MediaWikiFileFetcher( $requestFactory, $domainCredentials ) )->fetchFile( 'https://example.com' );
	}

}
