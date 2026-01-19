<?php

declare(strict_types = 1);

namespace ProfessionalWiki\ExternalContent;

use MediaWiki\Http\HttpRequestFactory;
use Firebase\JWT\JWT;

class GitHubApi {
	private $httpFactory;
	private $appId;
	private $privateKey;

	public function __construct( HttpRequestFactory $httpFactory, string $appId, string $privateKey ) {
		$this->httpFactory = $httpFactory;
		$this->appId = $appId;
		$this->privateKey = $privateKey;
	}

	public function getJwtToken(): string {
		$payload = [
			'iat' => time(),
			'exp' => time() + ( 10 * 60 ), // 10 minutes
			'iss' => $this->appId,
		];
		return JWT::encode( $payload, $this->privateKey, 'RS256' );
	}

	public function getInstallationIds( string $jwtToken ): array {
		try {
			$request = $this->httpFactory->create( "https://api.github.com/app/installations" );
			$request->setHeader('Authorization', "Bearer $jwtToken");
			$request->setHeader('Accept', "application/vnd.github+json");
			$request->setHeader('User-Agent', "MediaWiki-ExternalContent");
			$status = $request->execute();

			if ( $status->isOK() ) {
				$data = json_decode( $request->getContent(), true );
				if ( json_last_error() !== JSON_ERROR_NONE ) {
					wfLogWarning( 'Failed to decode GitHub API response: ' . json_last_error_msg() );
					return [];
				}
				return $data;
			}
			return [];
		} catch ( \Exception $e ) {
			return [];
		}
	}

	public function fetchAccessToken( string $installationId, string $jwtToken ): string {
        
		try{
			$options = [
				'method' => 'POST'
			];
			$url = "https://api.github.com/app/installations/$installationId/access_tokens";
			$request = $this->httpFactory->create( $url, $options );
			
			$request->setHeader('Authorization' , "Bearer $jwtToken");
			$request->setHeader('Accept', 'application/vnd.github+json');
			$request->setHeader('User-Agent', 'MediaWiki-ExternalContent');
			$status = $request->execute();
			if ( $status->isOK() ) {
				$data = json_decode( $request->getContent(), true );
				if ( json_last_error() !== JSON_ERROR_NONE ) {
					wfLogWarning( 'Failed to decode GitHub API response: ' . json_last_error_msg() );
					return '';
				}
				return $data['token'] ?? '';
			}
			return '';
		}catch(\Exception $e){
			return '';
		}
        
	}
}
