<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests;

use FileFetcher\FileFetcher;
use FileFetcher\NullFileFetcher;
use ProfessionalWiki\ExternalContent\EmbedExtensionFactory;

class TestFactory extends EmbedExtensionFactory {

	public static function newTestInstance(): self {
		self::$instance = new static();
		self::$instance->setFileFetcher( new NullFileFetcher() );
		self::$instance->setDomainWhitelist();
		return self::$instance;
	}

	public function setFileFetcher( FileFetcher $fileFetcher ): void {
		$this->fileFetcher = $fileFetcher;
	}

	public function setDomainWhitelist( string ...$allowedDomains ): void {
		$this->domainWhitelist = $allowedDomains;
	}

}
