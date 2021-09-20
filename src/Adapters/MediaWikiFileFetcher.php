<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters;

use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;
use MediaWiki\Http\HttpRequestFactory;

class MediaWikiFileFetcher implements FileFetcher {

	private HttpRequestFactory $requestFactory;

	public function __construct( HttpRequestFactory $requestFactory ) {
		$this->requestFactory = $requestFactory;
	}

	public function fetchFile( string $fileUrl ): string {
		$result = $this->requestFactory->get( $fileUrl );

		if ( is_string( $result ) ) {
			return $result;
		}

		throw new FileFetchingException( $fileUrl );
	}

}
