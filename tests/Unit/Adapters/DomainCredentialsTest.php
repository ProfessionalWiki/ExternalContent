<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Adapters;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Adapters\FileFetcher\BasicAuthCredentials;
use ProfessionalWiki\ExternalContent\Adapters\FileFetcher\DomainCredentials;

/**
 * @covers \ProfessionalWiki\ExternalContent\Adapters\FileFetcher\DomainCredentials
 */
class DomainCredentialsTest extends TestCase {

	public function testNewFromEmptyArray(): void {
		$this->assertEquals(
			new DomainCredentials(),
			DomainCredentials::newFromArray( [] )
		);
	}

	public function testNewFromArrayWithMultipleEntries(): void {
		$credentials = new DomainCredentials();
		$credentials->add( 'git.example.com', new BasicAuthCredentials( 'User', 'Pass' ) );
		$credentials->add( 'foo.example.com', new BasicAuthCredentials( 'Bar', 'Baz' ) );

		$this->assertEquals(
			$credentials,
			DomainCredentials::newFromArray( [
				'git.example.com' => [ 'User', 'Pass' ],
				'foo.example.com' => [ 'Bar', 'Baz' ],
			] )
		);
	}

}
