<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\UseCases\Embed;

class EmbedRequestBuilder {

	/**
	 * @param string[] $arguments
	 */
	public static function argumentsToRequest( array $arguments, bool $showEditButton = false ): EmbedRequest {
		$normalizedArguments = self::normalizeArguments( array_slice( $arguments, 1 ) );

		$language = $normalizedArguments['lang'] ?? null;
		$lineNumbers = $normalizedArguments['lineNumbers'] ?? null;
		$specificLines = $normalizedArguments['showLines'] ?? null;
		$render = $normalizedArguments['render'] ?? null;

		return new EmbedRequest(
			fileUrl: $arguments[0],
			language: is_string( $language ) ? $language : null,
			showLineNumbers: is_bool( $lineNumbers ) ? $lineNumbers : null,
			showSpecificLines: is_string( $specificLines ) ? $specificLines : null,
			render: ( is_bool( $render ) ? $render : null ),
			showEditButton: $showEditButton
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
