<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Adapters\EmbedPresenter;

use Parser;

class CategoryUsageTracker implements UsageTracker {

	private Parser $parser;

	public function __construct( Parser $parser ) {
		$this->parser = $parser;
	}

	public function trackUsage(): void {
		$this->parser->addTrackingCategory( 'external-content-tracking-category' );
	}

	public function trackBrokenUsage(): void {
		$this->trackUsage();
		$this->parser->addTrackingCategory( 'external-content-broken-tracking-category' );
	}

}
