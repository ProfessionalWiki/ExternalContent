<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\DataAccess;

use FileFetcher\FileFetcher;
use FileFetcher\FileFetchingException;
use MediaWiki\MediaWikiServices;

class MediaWikiFileFetcher implements FileFetcher {

	public function fetchFile( string $fileUrl ): string {
		$result = MediaWikiServices::getInstance()->getHttpRequestFactory()->get( $fileUrl );

		if ( is_string( $result ) ) {
			return $result;
		}

		throw new FileFetchingException( $fileUrl );
	}

}
