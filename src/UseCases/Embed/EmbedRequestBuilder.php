<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\UseCases\Embed;

class EmbedRequestBuilder {

	/**
	 * @param string[] $arguments
	 */
	public static function argumentsToRequest( array $arguments ): EmbedRequest {
		$normalizedArguments = self::normalizeArguments( array_slice( $arguments, 1 ) );

		$language = $normalizedArguments['lang'] ?? null;
		$line = $normalizedArguments['line'] ?? null;

		return new EmbedRequest(
			fileUrl: $arguments[0],
			language: is_string( $language ) ? $language : null,
			showLineNumbers: is_bool( $line ) ? $line : null
		);
	}

	/**
	 * @param string[] $arguments
	 * @return array<string,string|boolean> $results
	 */
	private static function normalizeArguments( array $arguments ): array {
		$results = [];

		foreach ( $arguments as $argument ) {
			$pair = array_map( 'trim', explode( '=', $argument, 2 ) );
			if ( count( $pair ) === 2 ) {
				$results[ $pair[0] ] = $pair[1];
			}
			if ( count( $pair ) === 1 ) {
				$results[ $pair[0] ] = true;
			}
		}

		return $results;
	}

}
