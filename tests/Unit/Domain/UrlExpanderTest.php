<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\UrlExpander;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\UrlExpander
 */
class UrlExpanderTest extends TestCase {

	/**
	 * @dataProvider linkProvider
	 */
	public function testRelativeLink( string $link, string $baseUrl, string $expectedUrl ): void {
		$this->assertSame(
			$expectedUrl,
			( new UrlExpander() )->expand( $link, $baseUrl )
		);
	}

	public function linkProvider(): iterable {
		yield 'Full URL' => [
			'https://example.com/kittens.md',
			'https://professional.wiki/hi/example.md',
			'https://example.com/kittens.md'
		];

		yield 'Relative URL' => [
			'kittens.md',
			'https://professional.wiki/hi/example.md',
			'https://professional.wiki/hi/kittens.md'
		];

		yield 'Relative URL with path' => [
			'amazing/fluff/kittens.md',
			'https://professional.wiki/hi/example.md',
			'https://professional.wiki/hi/amazing/fluff/kittens.md',
		];

		yield 'Relative URL with preceding slash' => [
			'/kittens.md',
			'https://professional.wiki/hi/example.md',
			'https://professional.wiki/hi/kittens.md'
		];

		yield 'Relative URL followed by slash' => [
			'kittens.md/',
			'https://professional.wiki/hi/example.md',
			'https://professional.wiki/hi/kittens.md'
		];

		yield 'Empty relative URL' => [
			'',
			'https://professional.wiki/hi/example.md',
			'https://professional.wiki/hi/'
		];

		yield 'Relative URL with image' => [
			'kittens.gif',
			'https://professional.wiki/hi/example.md',
			'https://professional.wiki/hi/kittens.gif'
		];

		yield 'Relative URL with no slash content URL' => [
			'kittens.md',
			'https://professional.wiki',
			'https://professional.wiki/kittens.md'
		];
	}

}
