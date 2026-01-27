<?php

declare(strict_types = 1);

use MediaWiki\MediaWikiServices;
use ProfessionalWiki\ExternalContent\Adapters\FileFetcher\GitHub\GitHubApi;
use ProfessionalWiki\ExternalContent\Adapters\FileFetcher\GitHub\GitHubStore;
use ProfessionalWiki\ExternalContent\Adapters\FileFetcher\GitHub\GitHubTokenManager;
use ProfessionalWiki\ExternalContent\Security\TokenEncryption;

return [
	'ExternalContent.GitHubTokenManagerFactory' => static function ( MediaWikiServices $services ): callable {
		return static function ( string $domain ) use ( $services ): GitHubTokenManager {
			$config = $services->getMainConfig();

			// Get GitHub App configuration from bearer token credentials
			$bearerTokenCredentials = $config->get( 'ExternalContentBearerTokenCredentials' );

			// Extract domain-specific credentials
			$appId = '';
			$privateKey = '';
			$encryptionKey = '';

			if ( isset( $bearerTokenCredentials[$domain] ) ) {
				$domainConfig = $bearerTokenCredentials[$domain];
				$appId = $domainConfig['app_id'] ?? '';
				$privateKey = $domainConfig['private_key'] ?? '';
				$encryptionKey = $domainConfig['encryption_key'] ?? '';
			}
			// Create GitHubApi instance
			$api = new GitHubApi(
				$services->getHttpRequestFactory(),
				$appId,
				$privateKey
			);

			// Create TokenEncryption instance
			$tokenEncryption = new TokenEncryption( $encryptionKey );

			// Create GitHubStore instance
			$store = new GitHubStore(
				$services->getDBLoadBalancer(),
				$tokenEncryption
			);

			// Create and return GitHubTokenManager instance
			return new GitHubTokenManager( $api, $store );
		};
	},
];
