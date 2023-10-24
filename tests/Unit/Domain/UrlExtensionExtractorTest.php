<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Domain\UrlExtensionExtractor;

/**
 * @covers \ProfessionalWiki\ExternalContent\Domain\UrlExtensionExtractor
 */
class UrlExtensionExtractorTest extends TestCase {

	public function testUrlWithExtension(): void {
		$this->assertSame(
			'md',
			( new UrlExtensionExtractor() )->extractExtension(
				'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md'
			)
		);
	}

	public function testUrlWithoutExtension(): void {
		$this->assertSame(
			'',
			( new UrlExtensionExtractor() )->extractExtension(
				'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README'
			)
		);
	}

	public function testUrlWithQuery(): void {
		$this->assertSame(
			'md',
			( new UrlExtensionExtractor() )->extractExtension(
				'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md?at=refs%2Fheads%2Fmaster'
			)
		);
	}

	public function testUrlWithHash(): void {
		$this->assertSame(
			'md',
			( new UrlExtensionExtractor() )->extractExtension(
				'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md#Installation'
			)
		);
	}

	public function testUrlWithDot(): void {
		$this->assertSame(
			'md',
			( new UrlExtensionExtractor() )->extractExtension(
				'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/foo.bar.baz.md#hash?a=b&c=d'
			)
		);
	}

	public function testUrlWithHashAndDot(): void {
		$this->assertSame(
			'md',
			( new UrlExtensionExtractor() )->extractExtension(
				'https://git.example.com/projects/KNOW/repos/fluffy-kittens/raw/README.md#an.example?a=b&c=d.e'
			)
		);
	}

}
