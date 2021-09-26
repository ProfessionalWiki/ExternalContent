<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

class CompoundUrlValidator implements UrlValidator {

	/**
	 * @var UrlValidator[]
	 */
	private array $validators;

	public function __construct( UrlValidator ...$validators ) {
		$this->validators = $validators;
	}

	public function getErrorCode( string $url ): ?string {
		foreach ( $this->validators as $validator ) {
			$errorCode = $validator->getErrorCode( $url );

			if ( $errorCode !== null ) {
				return $errorCode;
			}
		}

		return null;
	}

}
