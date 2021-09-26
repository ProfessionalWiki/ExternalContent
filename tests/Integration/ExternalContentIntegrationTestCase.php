<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration;

use MediaWikiIntegrationTestCase;
use ProfessionalWiki\ExternalContent\EmbedExtensionFactory;
use ProfessionalWiki\ExternalContent\Tests\TestFactory;

class ExternalContentIntegrationTestCase extends MediaWikiIntegrationTestCase {

	protected TestFactory $extensionFactory;

	protected function setUp(): void {
		parent::setUp();

		$this->setMwGlobals( EmbedExtensionFactory::DEFAULT_CONFIG );

		$this->extensionFactory = TestFactory::newTestInstance();
	}

}
