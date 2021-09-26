<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\FileExtensionUrlValidator;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\FileExtensionUrlValidator
 */
class FileExtensionUrlValidatorTest extends TestCase {

	public function testReturnsNullForWhitelistedExtension(): void {
		$this->assertNull(
			( new FileExtensionUrlValidator(
				'md'
			) )->getErrorCode( 'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md' )
		);

		$this->assertNull(
			( new FileExtensionUrlValidator(
				'md',
				'pew',
				'hi'
			) )->getErrorCode( 'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.pew' )
		);
	}

	public function testReturnsErrorCodeForUnknownExtension(): void {
		$this->assertSame(
			'extension-not-allowed',
			( new FileExtensionUrlValidator(
				'md',
				'pew',
				'hi'
			) )->getErrorCode( 'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.hax' )
		);
	}

	public function testExtensionIsAllowedWhenWhitelistIsEmpty(): void {
		$this->assertNull(
			( new FileExtensionUrlValidator() )
				->getErrorCode( 'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.whatever' )
		);
	}

	public function testReturnsErrorCodeForNoExtension(): void {
		$this->assertSame(
			'extension-not-allowed',
			( new FileExtensionUrlValidator(
				'md'
			) )->getErrorCode( 'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README' )
		);
	}

	public function testAllowsEmptyExtensionInWhitelist(): void {
		$this->assertNull(
			( new FileExtensionUrlValidator(
				''
			) )->getErrorCode( 'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README' )
		);
	}

}
