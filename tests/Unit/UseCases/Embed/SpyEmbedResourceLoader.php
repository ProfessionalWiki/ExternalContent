<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Tests\Unit\UseCases\Embed;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;
use ProfessionalWiki\ExternalContent\UseCases\Embed\EmbedResourceLoader;

class SpyEmbedResourceLoader implements EmbedResourceLoader {

	public bool $resourcesAreLoaded = false;

	public function loadRendererResources( ContentRenderer $renderer ): void {
		$this->resourcesAreLoaded = true;
	}

}
