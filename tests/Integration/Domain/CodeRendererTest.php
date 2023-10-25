<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration\Domain;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\CodeRenderer;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\ContentRenderer\CodeRenderer
 */
class CodeRendererTest extends TestCase {

	public function testRendersSimplePhpWithoutLineNumbers(): void {
		$this->assertSame(
			'<pre class="external-content" data-toolbar-order="copy-to-clipboard"><code class="language-php">print( "Hello world" );</code></pre>',
			( new CodeRenderer( language: 'php', showLineNumbers: false, showEditButton: false ) )->render( 'print( "Hello world" );', '' )
		);
	}

	public function testRendersSimpleTypescriptWithLineNumbers(): void {
		$this->assertSame(
			'<pre class="external-content line-numbers" data-toolbar-order="copy-to-clipboard"><code class="language-typescript">console.log( "Hello world" as string );</code></pre>',
			( new CodeRenderer( language: 'typescript', showLineNumbers: true, showEditButton: false ) )->render( 'console.log( "Hello world" as string );', '' )
		);
	}

	public function testRendersEscapedHtml(): void {
		$this->assertSame(
			'<pre class="external-content" data-toolbar-order="copy-to-clipboard"><code class="language-html">&lt;script>alert( "HAX" );&lt;/script></code></pre>',
			( new CodeRenderer( language: 'html', showLineNumbers: false, showEditButton: false ) )->render( '<script>alert( "HAX" );</script>', '' )
		);
	}

	public function testRendersEscapedHtmlWithWrongLanguage(): void {
		$this->assertSame(
			'<pre class="external-content" data-toolbar-order="copy-to-clipboard"><code class="language-javascript">&lt;script>alert( "HAX" );&lt;/script></code></pre>',
			( new CodeRenderer( language: 'javascript', showLineNumbers: false, showEditButton: false ) )->render( '<script>alert( "HAX" );</script>', '' )
		);
	}

	public function testRendersEscapedPartialHtml(): void {
		$this->assertSame(
			'<pre class="external-content" data-toolbar-order="copy-to-clipboard"><code class="language-html">&lt;/code>&lt;/pre>FOO</code></pre>',
			( new CodeRenderer( language: 'html', showLineNumbers: false, showEditButton: false ) )->render( '</code></pre>FOO', '' )
		);
	}

	public function testRendersShowEditButton(): void {
		$this->assertSame(
			'<pre class="external-content" data-toolbar-order="bitbucket-edit,copy-to-clipboard" data-src="http://bitbucket:7990/projects/TEST/repos/test/browse/test.php"><code class="language-php">&lt;/code>&lt;/pre>FOO</code></pre>',
			( new CodeRenderer( language: 'php', showLineNumbers: false, showEditButton: true ) )->render( '</code></pre>FOO', 'http://bitbucket:7990/projects/TEST/repos/test/browse/test.php' )
		);
	}

}
