<?php

declare(strict_types=1);

namespace ProfessionalWiki\ExternalContent\Adapters\FileFetcher\GitHub;

use MediaWiki\MediaWikiServices;
use ProfessionalWiki\ExternalContent\Security\TokenEncryption;
use ProfessionalWiki\ExternalContent\Adapters\FileFetcher\GitHub\GitHubApi;
use ProfessionalWiki\ExternalContent\Adapters\FileFetcher\GitHub\GitHubTokenManager;

class GitHubUtils
{
    public static function getAccessTokenForFileUrl( $config , string $fileUrl): string
    {  
        $appId = $config->getAppId() ?: '';
        $privateKey = $config->getPrivateKey() ?: '';
        $encryptionKey = $config->getEncryptionKey() ?: '';

        if ( !is_object( $config ) || empty( $appId ) || empty( $privateKey ) || empty( $encryptionKey ) ) {
            return '';
        }

        $services = MediaWikiServices::getInstance();
        $api = new GitHubApi(
            $services->getHttpRequestFactory(),
            $appId,
            $privateKey
        );

        $tokenEncryption = new TokenEncryption( $encryptionKey );
        $store = new GitHubStore( $services->getDBLoadBalancer(), $tokenEncryption );
        
        $objTokenManager = new GitHubTokenManager($api, $store);        
        return $objTokenManager->getValidAccessToken($fileUrl);
    }
}
