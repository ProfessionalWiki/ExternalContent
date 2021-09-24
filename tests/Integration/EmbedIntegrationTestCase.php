<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Tests\TestFactory;

class EmbedIntegrationTestCase extends TestCase {

	protected TestFactory $extensionFactory;

	protected function setUp(): void {
		$this->extensionFactory = TestFactory::newTestInstance();
	}

}
