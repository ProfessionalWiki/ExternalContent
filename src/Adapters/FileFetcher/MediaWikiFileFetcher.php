<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters\FileFetcher;

use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;
use MediaWiki\Http\HttpRequestFactory;

class MediaWikiFileFetcher implements FileFetcher {

	private HttpRequestFactory $requestFactory;
	private DomainCredentials $credentials;

	public function __construct( HttpRequestFactory $requestFactory, ?DomainCredentials $credentials = null ) {
		$this->requestFactory = $requestFactory;
		$this->credentials = $credentials ?? new DomainCredentials();
	}

	public function fetchFile( string $fileUrl ): string {
		$domain = parse_url( $fileUrl, PHP_URL_HOST ) ?? '';
		$bearerToken = $this->credentials->getBearerTokenForDomain( $domain, $fileUrl );
		
		if ( $bearerToken !== null ) {
			$request = $this->requestFactory->create( $fileUrl );
			$request->setHeader( 'Authorization', 'Bearer ' . $bearerToken->getToken() );
			$status = $request->execute();
			if ( $status->isOK() ) {
				return $request->getContent();
			}
			throw new FileFetchingException( $fileUrl );
		}
		
		//existing code for basic auth and no auth
		$result = $this->requestFactory->get(
			$fileUrl,
			$this->newRequestOptions( $fileUrl )
		);

		if ( is_string( $result ) ) {
			return $result;
		}

		throw new FileFetchingException( $fileUrl );
	}

	private function newRequestOptions( string $fileUrl ): array {
		
		$domain = parse_url( $fileUrl, PHP_URL_HOST ) ?? '';		
		$bearerToken = $this->credentials->getBearerTokenForDomain( $domain, $fileUrl );
		
		if ( $bearerToken !== null ) {
			return [];
		}

		
		$basicAuth = $this->credentials->getBasicAuthForDomain( $domain );
		if ( $basicAuth !== null ) {
			return [
				'username' => $basicAuth->getUserName(),
				'password' => $basicAuth->getPassword(),
			];
		}

		return [];
	}

}
