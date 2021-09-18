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
		yield 'Raw paths should not be changed' => [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/Arbitrary.md',
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/Arbitrary.md'
		];

		yield 'Browse paths get turned into raw paths' => [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/Arbitrary.md',
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/Arbitrary.md'
		];

		yield 'Repository root defaults to README.md' => [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse',
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md'
		];

		yield 'Repository root defaults to README.md (tailing slash)' => [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/',
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md'
		];

		yield 'README.md file should remain unchanged' => [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/README.md',
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md'
		];

		yield 'Files without dot are handled' => [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse/LICENSE',
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/LICENSE'
		];

		yield 'Branch query should remain unchanged' => [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/browse?at=refs%2Fheads%2FMyBranch',
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md?at=refs%2Fheads%2FMyBranch'
		];

		yield 'Browse is appended when missing' => [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens',
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md'
		];

		yield 'Browse is appended when missing (tailing slash)' => [
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/',
			'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md'
		];

		yield 'Port is retained' => [
			'http://localhost:7990/projects/MYP/repos/fluffykittens/browse',
			'http://localhost:7990/projects/MYP/repos/fluffykittens/raw/README.md'
		];
	}

}
