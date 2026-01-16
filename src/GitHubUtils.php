<?php

declare(strict_types=1);

namespace ProfessionalWiki\ExternalContent;

use MediaWiki\MediaWikiServices;

class GitHubUtils
{
    public static function getAccessTokenForFileUrl(string $fileUrl): string
    {
        $services = MediaWikiServices::getInstance();
        $config = $services->getMainConfig()->get('ExternalContentBearerTokenCredentials');
        
        if ( !is_array( $config ) || empty( $config['github_app_id'] ) || empty( $config['github_private_key'] ) ) {
            return '';
        }

        $api = new GitHubApi(
            $services->getHttpRequestFactory(),
            $config['github_app_id'],
            $config['github_private_key']
        );

        $store = new GitHubStore($services->getDBLoadBalancer());
        $tokenManager = new TokenManager($api, $store);        return $tokenManager->getValidAccessToken($fileUrl);
    }
}
