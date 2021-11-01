<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\UrlValidator\WhitelistedDomainUrlValidator;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\UrlValidator\WhitelistedDomainUrlValidator
 */
class WhitelistedDomainUrlValidatorTest extends TestCase {

	public function testReturnsNullForWhitelistedDomain(): void {
		$this->assertNull(
			( new WhitelistedDomainUrlValidator(
				'foo.bar.com',
				'git.example.com',
				'pew.pew.pew'
			) )->getErrorCode( 'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md' )
		);
	}

	public function testReturnsErrorCodeForUnknownDomain(): void {
		$this->assertSame(
			'domain-not-allowed',
			( new WhitelistedDomainUrlValidator(
				'git.example.xyz',
				'xyz.example.com',
				'git.xyz.com'
			) )->getErrorCode( 'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md' )
		);
	}

	public function testDomainIsAllowedWhenWhitelistIsEmpty(): void {
		$this->assertNull(
			( new WhitelistedDomainUrlValidator() )
				->getErrorCode( 'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md' )
		);
	}

}
