<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\UseCases\Embed;

use ProfessionalWiki\ExternalContent\Domain\UrlValidator;

class StubUrlValidator implements UrlValidator {

	private ?string $errorCode;

	public function __construct( ?string $errorCode ) {
		$this->errorCode = $errorCode;
	}

	public function getErrorCode( string $url ): ?string {
		return $this->errorCode;
	}

}
