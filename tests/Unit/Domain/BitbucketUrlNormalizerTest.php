<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\BitbucketUrlNormalizer;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\BitbucketUrlNormalizer
 */
class BitbucketUrlNormalizerTest extends TestCase {

	/**
	 * @dataProvider normalizationProvider
	 */
	public function testNormalization( string $input, string $expectedOutput ): void {
		$this->assertSame(
			$expectedOutput,
			( new BitbucketUrlNormalizer() )->normalize( $input )
		);
	}

	public function normalizationProvider(): iterable {
		yield 'Directory should get README.md appended' => [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse',
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/README.md'
		];

		// TODO: what about path/LICENSE?

		yield 'Directory with slash should get README.md appended' => [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/',
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/README.md'
		];

		yield 'README.md file should remain unchanged' => [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/README.md',
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/README.md'
		];

		yield 'Arbitrary.md file should remain unchanged' => [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/Arbitrary.md',
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/Arbitrary.md'
		];

		// TODO: https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md?at=refs%2Fheads%2Fmain
	}

}
