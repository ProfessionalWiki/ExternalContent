<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\UrlNormalizer\GitHubUrlNormalizer;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\UrlNormalizer\GitHubUrlNormalizer
 * @covers \ProfessionalWiki\ExternalContent\Domain\UrlNormalizer\HostAndPathModifier
 */
class GitHubUrlNormalizerTest extends TestCase {

	/**
	 * @dataProvider normalizationProvider
	 */
	public function testNormalization( string $input, string $expectedOutput ): void {
		$this->assertSame(
			$expectedOutput,
			( new GitHubUrlNormalizer() )->fullNormalize( $input )
		);
	}

	public function normalizationProvider(): iterable {
		yield 'Normal paths get turned into raw paths' => [
			'https://github.com/ProfessionalWiki/ExternalContent/blob/master/README.md',
			'https://raw.githubusercontent.com/ProfessionalWiki/ExternalContent/master/README.md'
		];

		yield 'Branches are handled' => [
			'https://github.com/ProfessionalWiki/ExternalContent/blob/1.0.0/README.md',
			'https://raw.githubusercontent.com/ProfessionalWiki/ExternalContent/1.0.0/README.md'
		];

		yield 'README.md is default file in repo root' => [
			'https://github.com/ProfessionalWiki/ExternalContent',
			'https://raw.githubusercontent.com/ProfessionalWiki/ExternalContent/master/README.md'
		];

		yield 'README.md is default file in repo root (tailing slash)' => [
			'https://github.com/ProfessionalWiki/ExternalContent/',
			'https://raw.githubusercontent.com/ProfessionalWiki/ExternalContent/master/README.md'
		];

		yield 'README.md is default file in repo root (branch)' => [
			'https://github.com/ProfessionalWiki/ExternalContent/tree/1.0.0',
			'https://raw.githubusercontent.com/ProfessionalWiki/ExternalContent/1.0.0/README.md'
		];

		yield 'README.md is default file in repo root (branch and tailing slash)' => [
			'https://github.com/ProfessionalWiki/ExternalContent/tree/1.0.0/',
			'https://raw.githubusercontent.com/ProfessionalWiki/ExternalContent/1.0.0/README.md'
		];

		yield 'README.md is default file in directories' => [
			'https://github.com/ProfessionalWiki/ExternalContent/tree/1.0.0/tests',
			'https://raw.githubusercontent.com/ProfessionalWiki/ExternalContent/1.0.0/tests/README.md'
		];
	}

	/**
	 * @dataProvider unchangedNormalizationProvider
	 */
	public function testUnchangedNormalization( string $input ): void {
		$this->assertSame(
			$input,
			( new GitHubUrlNormalizer() )->fullNormalize( $input )
		);
	}

	public function unchangedNormalizationProvider(): iterable {
		yield 'Raw path' => [
			'https://raw.githubusercontent.com/ProfessionalWiki/ExternalContent/master/README.md'
		];

		yield 'Path without blob' => [
			'https://github.com/ProfessionalWiki/ExternalContent/not-blob/master/README.md'
		];

		yield 'Path that is too short' => [
			'https://github.com/ProfessionalWiki/'
		];

		yield 'Path that is way too short' => [
			'https://github.com'
		];
	}

	/**
	 * @dataProvider viewLevelNormalizationProvider
	 */
	public function testViewLevelNormalization( string $input, string $expectedOutput ): void {
		$this->assertSame(
			$expectedOutput,
			( new GitHubUrlNormalizer() )->viewLevelNormalize( $input )
		);
	}

	public function viewLevelNormalizationProvider(): iterable {
		yield 'Normal paths are NOT turned into raw paths' => [
			'https://github.com/ProfessionalWiki/ExternalContent/blob/master/README.md',
			'https://github.com/ProfessionalWiki/ExternalContent/blob/master/README.md'
		];

		yield 'Raw paths are left as they are' => [
			'https://raw.githubusercontent.com/ProfessionalWiki/ExternalContent/master/README.md',
			'https://raw.githubusercontent.com/ProfessionalWiki/ExternalContent/master/README.md'
		];

		yield 'Repo root defaults to non-raw README.md on master' => [
			'https://github.com/ProfessionalWiki/ExternalContent',
			'https://github.com/ProfessionalWiki/ExternalContent/blob/master/README.md'
		];
	}

}
