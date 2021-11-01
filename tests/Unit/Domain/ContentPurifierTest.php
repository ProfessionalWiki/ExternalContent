<?php

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\ContentPurifier;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\ContentRenderer\ContentPurifier
 */
class ContentPurifierTest extends TestCase {

	public function testReturnsAllAllowedTags(): void {
		$this->assertSame(
			'<h1>my <u>test</u> <em>site</em></h1>
<p>lorem</p>
<ul><li>item <strong>1</strong></li></ul><img src="/logo.png" alt="wikimedia" />
some<br />
thing<br /><hr />
new
<table class="bobby"><tr><td>1</td></tr></table>
dolor
<a href="http://wikipedia.org" target="_blank" rel="noreferrer noopener">opening in new window, rel added by HtmlPurifier</a>
amet
<a href="http://wikimedia.de">ordinary link</a>',
			$this->newPurifier()->purify(
				'<h1>my <u>test</u> <em>site</em></h1>
<p>lorem</p>
<ul><li>item <strong>1</strong></li></ul><img src="/logo.png" alt="wikimedia" />
some<br>
thing<br/><hr />
new
<table class="bobby"><tr><td>1</td></tr></table>
dolor
<a href="http://wikipedia.org" target="_blank">opening in new window, rel added by HtmlPurifier</a>
amet
<a href="http://wikimedia.de">ordinary link</a>'
			)
		);
	}

	private function newPurifier(): ContentPurifier {
		return new ContentPurifier();
	}

	public function testStripsDisallowedTags(): void {
		$this->assertSame( 'invalid div', $this->newPurifier()->purify( '<div>invalid div</div>' ) );
	}

	public function testRepairsDamagedTags(): void {
		$this->assertSame( '<p>dangling paragraph</p>', $this->newPurifier()->purify( '<p>dangling paragraph' ) );
	}

	public function testRemovesInvalidAttributes(): void {
		$this->assertSame( '<p>BIG</p>', $this->newPurifier()->purify( '<p style="font-size:100000px">BIG</p>' ) );
	}
}
