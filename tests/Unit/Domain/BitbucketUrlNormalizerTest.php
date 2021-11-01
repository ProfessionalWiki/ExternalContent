<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\BitbucketUrlNormalizer;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\BitbucketUrlNormalizer
 * @covers \ProfessionalWiki\ExternalContent\Domain\UrlPathModifier
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

	/**
	 * @dataProvider invalidUrlProvider
	 */
	public function testInvalidUrlsCauseRuntimeException( string $invalidUrl, string $expectedMessage ): void {
		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( $expectedMessage );
		( new BitbucketUrlNormalizer() )->normalize( $invalidUrl );
	}

	public function invalidUrlProvider(): iterable {
		yield 'No host' => [ '/projects/KNOW/repos/fluffy-kittens/', 'url-missing-host' ];
		yield 'No repo' => [ 'https://git.example.com/projects/KNOW/repos', 'url-missing-repository' ];
		yield 'No repo (tailing slash)' => [ 'https://git.example.com/projects/KNOW/repos/', 'url-missing-repository' ];
		yield 'No path' => [ 'https://git.example.com', 'url-missing-path' ];
		yield 'No path (tailing slash)' => [ 'https://git.example.com/', 'url-not-bitbucket' ];
		yield 'Path does not start with projects' => [ 'https://git.example.com/wrong/KNOW/repos/fluffy-kittens/', 'url-not-bitbucket' ];
		yield 'Path does not have repos' => [ 'https://git.example.com/projects/KNOW/wrong/fluffy-kittens/', 'url-not-bitbucket' ];
		yield 'Path is entirely wrong' => [ 'https://git.example.com/foo/bar/baz', 'url-not-bitbucket' ];
		yield 'Empty' => [ '', 'url-missing-host' ];
		yield 'Slash' => [ '/', 'url-missing-host' ];
	}

}
