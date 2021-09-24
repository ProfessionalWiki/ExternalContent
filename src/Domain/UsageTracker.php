<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

interface UsageTracker {

	public function trackUsage(): void;

	public function trackBrokenUsage(): void;

}
