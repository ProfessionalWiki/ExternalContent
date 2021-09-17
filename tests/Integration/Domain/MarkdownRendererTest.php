<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration\EntryPoints;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\MarkdownRenderer;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\MarkdownRenderer
 */
class MarkdownRendererTest extends TestCase {

	public function testRendersSimpleMarkdown(): void {
		$this->assertSame(
			'<p>I am <strong>bold</strong></p>',
			( new MarkdownRenderer() )->normalize( 'I am **bold**', '' )
		);
	}

	public function testPurifiesContent(): void {
		$this->assertSame(
			'<p>I am </p><br />',
			( new MarkdownRenderer() )->normalize( 'I am <script>evil</script><div><br></div>', '' )
		);
	}

	/**
	 * @dataProvider linkProvider
	 */
	public function testRelativeLink( string $link, string $contentPath, string $expectedUrl ): void {
		$this->assertSame(
			'<p><a href="' . $expectedUrl . '">My Link</a></p>',
			( new MarkdownRenderer() )->normalize( "[My Link]($link)", $contentPath )
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
	}

}
