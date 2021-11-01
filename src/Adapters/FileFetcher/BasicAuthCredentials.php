<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters\FileFetcher;

class BasicAuthCredentials {

	private string $userName;
	private string $password;

	public function __construct( string $userName, string $password ) {
		$this->userName = $userName;
		$this->password = $password;
	}

	public function getUserName(): string {
		return $this->userName;
	}

	public function getPassword(): string {
		return $this->password;
	}

}
