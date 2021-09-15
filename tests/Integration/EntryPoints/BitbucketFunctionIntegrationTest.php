<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Integration\EntryPoints;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\ExternalContent\Tests\TestEnvironment;

/**
 * @covers \ProfessionalWiki\ExternalContent\EntryPoints\BitbucketFunction
 */
class BitbucketFunctionIntegrationTest extends TestCase {

	public function testTodo(): void {
		$this->assertStringContainsString(
			'TODO',
			TestEnvironment::instance()->parse( '{{#bitbucket:}}' )
		);
	}

}
