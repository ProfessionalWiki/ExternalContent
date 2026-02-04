<?php

declare(strict_types=1);

namespace ProfessionalWiki\ExternalContent\Adapters\FileFetcher\GitHub;

use MediaWiki\MediaWikiServices;

class GitHubUtils
{
    public static function getAccessTokenForFileUrl( string $domain, string $fileUrl ): string
    {
        $services = MediaWikiServices::getInstance();

        /** @var callable $factory */
        $factory = $services->get( 'ExternalContent.GitHubTokenManagerFactory' );

        /** @var GitHubTokenManager $tokenManager */
        $tokenManager = $factory( $domain );

        return $tokenManager->getValidAccessToken( $fileUrl );
    }
}

