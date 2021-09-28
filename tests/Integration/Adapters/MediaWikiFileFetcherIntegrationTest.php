<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration\Adapters;

use FileFetcher\FileFetchingException;
use MediaWiki\MediaWikiServices;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Adapters\BasicAuthCredentials;
use ProfessionalWiki\ExternalContent\Adapters\DomainCredentials;
use ProfessionalWiki\ExternalContent\Adapters\MediaWikiFileFetcher;
use ProfessionalWiki\ExternalContent\Tests\TestEnvironment;

/**
 * @covers \ProfessionalWiki\ExternalContent\Adapters\MediaWikiFileFetcher
 * @covers \ProfessionalWiki\ExternalContent\Adapters\DomainCredentials
 * @covers \ProfessionalWiki\ExternalContent\Adapters\BasicAuthCredentials
 */
class MediaWikiFileFetcherIntegrationTest extends TestCase {

	public function testUnreachableFileResultsInException(): void {
		$this->expectException( FileFetchingException::class );
		( new MediaWikiFileFetcher( MediaWikiServices::getInstance()->getHttpRequestFactory() ) )->fetchFile( '404' );
	}

	/**
	 * To get this test to run, set the PHPUNIT_WIKI_URL ENV var. Example: http://localhost/index.php/
	 */
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

	/**
	 * To get this test to run, set these ENV vars:
	 * * BITBUCKET_URL example: http://bitbucket.svc:7990/projects/MYP/repos/fluffykittens/raw/README.md
	 * * BITBUCKET_USER example: AdminUser
	 * * BITBUCKET_ACCESS_TOKEN example: mQk5OYTzMDIzOTM4OvMj9wLxDiKulWL3nxG8Xr7KbBZz
	 * The file needs to contain the string `professional-wiki` and can thus be the README of this repo.
	 */
	public function testAuthenticateWithBasicAuth(): void {
		$url = getenv( 'BITBUCKET_URL' );
		$user = getenv( 'BITBUCKET_USER' );
		$password = getenv( 'BITBUCKET_ACCESS_TOKEN' );

		if ( !is_string( $url ) || !is_string( $user ) || !is_string( $password ) ) {
			$this->markTestSkipped( 'Bitbucket ENV vars not set' );
		}

		$credentials = new DomainCredentials();
		$credentials->add(
			parse_url( $url, PHP_URL_HOST ),
			new BasicAuthCredentials( $user, $password )
		);

		$fileFetcher = new MediaWikiFileFetcher(
			MediaWikiServices::getInstance()->getHttpRequestFactory(),
			$credentials
		);

		$this->assertStringContainsString(
			'professional-wiki',
			$fileFetcher->fetchFile( $url )
		);
	}

}
