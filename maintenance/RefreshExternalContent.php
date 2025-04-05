<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Maintenance;

use Maintenance;
use MediaWiki\MediaWikiServices;
use Wikimedia\Rdbms\IDatabase;

$basePath = getenv( 'MW_INSTALL_PATH' ) !== false ? getenv( 'MW_INSTALL_PATH' ) : __DIR__ . '/../../..';
require_once $basePath . '/maintenance/Maintenance.php';

class RefreshExternalContent extends Maintenance {

	public function __construct() {
		parent::__construct();

		$this->requireExtension( 'External Content' );
		$this->addDescription( 'Invalidates the cache of all pages that embed external content via the ExternalContent extension' );
	}

	public function execute() {
		$this->invalidateCacheOfPagesInCategory(
			'Pages_with_external_content', // TODO
			MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection( DB_PRIMARY )
		);
	}

	private function invalidateCacheOfPagesInCategory( string $categoryName, IDatabase $db ): void {
		// TODO: optimize
		// TODO: automated test
		// TODO: extract?

		$pageIds = $db->selectFieldValues(
			'categorylinks',
			'cl_from',
			[
				'cl_to' => $categoryName
			]
		);

		$db->update(
			'page',
			[
				'page_touched' => $db->timestamp()
			],
			[
				'page_id' => $pageIds
			]
		);
	}

}

$maintClass = RefreshExternalContent::class;
require_once RUN_MAINTENANCE_IF_MAIN;
