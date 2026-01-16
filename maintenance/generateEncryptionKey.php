<?php

namespace ProfessionalWiki\ExternalContent\Maintenance;

use Maintenance;
use ProfessionalWiki\ExternalContent\Security\TokenEncryption;

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

/**
 * Generates a new encryption key for GitHub access tokens
 */
class GenerateEncryptionKey extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Generates a new encryption key for GitHub access tokens' );
	}

	public function execute() {
		$key = TokenEncryption::generateKey();

		$this->output( "Generated encryption key:\n\n" );
		$this->output( $key . "\n\n" );
		$this->output( "Add this to your LocalSettings.php:\n\n" );
		$this->output( "\$wgExternalContentBearerTokenCredentials = [\n" );
		$this->output( "    'github_app_id' => 'YOUR_GITHUB_APP_ID',\n" );
		$this->output( "    'github_private_key' => 'YOUR_PRIVATE_KEY',\n" );
		$this->output( "    'encryption_key' => '$key',\n" );
		$this->output( "    'domains' => [\n" );
		$this->output( "        'raw.githubusercontent.com',\n" );
		$this->output( "    ],\n" );
		$this->output( "];\n\n" );
		$this->output( "IMPORTANT: Keep this key secure! If lost, you won't be able to decrypt stored tokens.\n" );
		$this->output( "Store it in a secure location (e.g., environment variable or secret management system).\n" );
	}
}

$maintClass = GenerateEncryptionKey::class;
require_once RUN_MAINTENANCE_IF_MAIN;
