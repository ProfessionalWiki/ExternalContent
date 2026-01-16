<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters\FileFetcher;

class BearerTokenCredentials {

	private string $token;

	public function __construct( string $token = '' ) {
		$this->token = $token;
	}
	public function getToken(): string {
		return $this->token;
	}
}
