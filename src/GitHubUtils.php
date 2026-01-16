<?php

declare(strict_types=1);

namespace ProfessionalWiki\ExternalContent;

use MediaWiki\MediaWikiServices;
use ProfessionalWiki\ExternalContent\Security\TokenEncryption;

class GitHubUtils
{
    public static function getAccessTokenForFileUrl(string $fileUrl): string
    {
        $services = MediaWikiServices::getInstance();
        $config = $services->getMainConfig()->get('ExternalContentBearerTokenCredentials');

        if ( !is_array( $config ) || empty( $config['github_app_id'] ) || empty( $config['github_private_key'] ) ) {
            return '';
        }

        if ( empty( $config['encryption_key'] ) ) {
            wfLogWarning( 'ExternalContent: encryption_key is not configured for GitHub tokens' );
            return '';
        }

        $api = new GitHubApi(
            $services->getHttpRequestFactory(),
            $config['github_app_id'],
            $config['github_private_key']
        );

        $tokenEncryption = new TokenEncryption( $config['encryption_key'] );
        $store = new GitHubStore( $services->getDBLoadBalancer(), $tokenEncryption );
        $tokenManager = new TokenManager($api, $store);
        return $tokenManager->getValidAccessToken($fileUrl);
    }
}
