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
			( new MarkdownRenderer() )->normalize( 'I am **bold**' )
		);
	}

	public function testPurifiesContent(): void {
		$this->assertSame(
			'<p>I am </p><br />',
			( new MarkdownRenderer() )->normalize( 'I am <script>evil</script><div><br></div>' )
		);
	}

	public function testRelativeLink(): void {
		// FIXME: need source context to crate correct URL
		$this->assertSame(
			'<p><a href="path">Relative link</a></p>',
			( new MarkdownRenderer() )->normalize( '[Relative link](path)' )
		);
	}

}
