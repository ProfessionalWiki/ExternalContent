<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration\EntryPoints;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\MarkdownRenderer;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\ContentRenderer\MarkdownRenderer
 */
class MarkdownRendererTest extends TestCase {

	public function testRendersSimpleMarkdown(): void {
		$this->assertSame(
			'<p>I am <strong>bold</strong></p>',
			( new MarkdownRenderer() )->render( 'I am **bold**', '' )
		);
	}

	public function testPurifiesContent(): void {
		$this->assertRendersAs(
			'<p>I am </p><br />',
			'I am <script>evil</script><div><br></div>'
		);
	}

	/**
	 * @dataProvider linkProvider
	 */
	public function testRelativeLink( string $link, string $contentPath, string $expectedUrl ): void {
		$this->assertSame(
			'<p><a href="' . $expectedUrl . '">My Link</a></p>',
			( new MarkdownRenderer() )->render( "[My Link]($link)", $contentPath )
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

	public function testRendersFencedCodeBlock(): void {
		$this->assertRendersAs(
			'<pre><code>Some codeMore code</code></pre>',
			"~~~\nSome code\nMore code\n~~~"
		);
	}

	private function assertRendersAs( string $expectedHtml, string $markdown ): void {
		$this->assertSame(
			$expectedHtml,
			preg_replace(
				'/\n+/', // Replace newlines since they get inserted pretty randomly between HTML tags
				'',
				( new MarkdownRenderer() )->render( $markdown, '' )
			)
		);
	}

}
