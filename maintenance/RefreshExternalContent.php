<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Maintenance;

use MediaWiki\Category\Category;
use MediaWiki\Maintenance\Maintenance;

$basePath = getenv( 'MW_INSTALL_PATH' ) !== false ? getenv( 'MW_INSTALL_PATH' ) : __DIR__ . '/../../..';
require_once $basePath . '/maintenance/Maintenance.php';

class RefreshExternalContent extends Maintenance {

	public function __construct() {
		parent::__construct();

		$this->requireExtension( 'External Content' );
		$this->addDescription( 'Invalidates the cache of all pages that embed external content via the ExternalContent extension' );
	}

	public function execute() {
		$trackingCategories = $this->getServiceContainer()->getTrackingCategories()->getTrackingCategories();

		foreach ( $trackingCategories['external-content-tracking-category']['cats'] as $category ) {
			$this->invalidateCacheOfPagesInCategory( $category->getDBkey() );
		}
	}

	private function invalidateCacheOfPagesInCategory( string $categoryName ): void {
		foreach ( Category::newFromName( $categoryName )->getMembers() as $page ) {
			$page->invalidateCache();
		}
	}

}

$maintClass = RefreshExternalContent::class;
require_once RUN_MAINTENANCE_IF_MAIN;
