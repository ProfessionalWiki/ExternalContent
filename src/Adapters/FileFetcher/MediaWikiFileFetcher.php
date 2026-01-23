<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters\FileFetcher;

use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;
use MediaWiki\Http\HttpRequestFactory;
use MediaWiki\MediaWikiServices;

class MediaWikiFileFetcher implements FileFetcher {

	private HttpRequestFactory $requestFactory;
	private DomainCredentials $credentials;

	public function __construct( HttpRequestFactory $requestFactory, ?DomainCredentials $credentials = null ) {
		$this->requestFactory = $requestFactory;
		$this->credentials = $credentials ?? new DomainCredentials();
	}

	public function fetchFile( string $fileUrl ): string {
		$request = $this->createRequest( $fileUrl );
		$status = $request->execute();
		if ( $status->isOk() ) {
			return $request->getContent();
		}

		throw new FileFetchingException( $fileUrl );
	}

	protected function createRequest( string $fileUrl ) {
		$domain = parse_url( $fileUrl, PHP_URL_HOST ) ?? '';
		$authHeader = $this->credentials->getAuthorizationHeader( $domain, $fileUrl );
		$request = $this->requestFactory->create( $fileUrl );
		if ( !empty( $authHeader ) ) {
			$request->setHeader( 'Authorization', $authHeader );
		}
		return $request;
	}
}
