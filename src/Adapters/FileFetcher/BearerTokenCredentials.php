<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters\FileFetcher;

class BearerTokenCredentials {

	private string $userName;
	private string $token;

	public function __construct( string $userName,string $token ){
		$this->userName = $userName;
		$this->token = $token;
	}
	public function getUserName(): string {
		return $this->userName;
	}

	public function getToken(): string {
		return $this->token;
	}

}
