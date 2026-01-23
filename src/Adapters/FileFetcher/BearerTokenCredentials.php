<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters\FileFetcher;

class BearerTokenCredentials {

	private string $app_id;

	private string $private_key;

	private string $encryption_key;

	private string $token;

	public function __construct( string $app_id, string $private_key, string $encryption_key ) {
		$this->app_id = $app_id;
		$this->private_key = $private_key;
		$this->encryption_key = $encryption_key;
	}

	public function getAppId(): string {
		return $this->app_id;
	}

	public function getPrivateKey(): string {
		return $this->private_key;
	}

	public function getEncryptionKey(): string {
		return $this->encryption_key;
	}
	
	public function getToken(): string {
		return $this->token;
	}

	public function setToken( string $token ): void {
		$this->token = $token;
	}
}
