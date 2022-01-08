<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\UrlValidator;
use ProfessionalWiki\ExternalContent\Domain\UrlValidator\CompoundUrlValidator;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\UrlValidator\CompoundUrlValidator
 */
class CompoundUrlValidatorTest extends TestCase {

	public function testSucceedsWithNoValidators(): void {
		$this->assertNull(
			( new CompoundUrlValidator() )->getErrorCode( 'https://whatever.com' )
		);
	}

	public function testFailsWhenInnerValidatorFails(): void {
		$this->assertSame(
			'error-code',
			( new CompoundUrlValidator(
				$this->newStubValidator( null ),
				$this->newStubValidator( 'error-code' ),
				$this->newStubValidator( null )
			) )->getErrorCode( 'https://whatever.com' )
		);
	}

	private function newStubValidator( ?string $returnValue ): UrlValidator {
		return new class( $returnValue ) implements UrlValidator {
			private ?string $returnValue;

			public function __construct( ?string $returnValue ) {
				$this->returnValue = $returnValue;
			}

			public function getErrorCode( string $url ): ?string {
				return $this->returnValue;
			}
		};
	}

	public function testSucceedsWhenInnerValidatorsSucceed(): void {
		$this->assertNull(
			( new CompoundUrlValidator(
				$this->newStubValidator( null ),
				$this->newStubValidator( null ),
				$this->newStubValidator( null )
			) )->getErrorCode( 'https://whatever.com' )
		);
	}

}
