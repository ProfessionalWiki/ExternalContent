<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests;

use FileFetcher\FileFetcher;
use FileFetcher\NullFileFetcher;
use Message;
use MessageLocalizer;
use ProfessionalWiki\ExternalContent\EmbedExtensionFactory;

class TestFactory extends EmbedExtensionFactory {

	public static function newTestInstance(): self {
		self::$instance = new static();
		self::$instance->setFileFetcher( new NullFileFetcher() );
		return self::$instance;
	}

	public function setFileFetcher( FileFetcher $fileFetcher ): void {
		$this->fileFetcher = $fileFetcher;
	}

	public function setDomainWhitelist( string ...$allowedDomains ): void {
		$this->domainWhitelist = $allowedDomains;
	}

	public function newTestMessageLocalizer(): MessageLocalizer {
		return new class() implements MessageLocalizer {
			public function msg( $key, ...$params ): Message {
				return wfMessage( 'test-' . $key, ...$params );
			}
		};
	}

}
