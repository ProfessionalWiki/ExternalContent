<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\UseCases\Embed;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

interface EmbedResourceLoader {

	public function loadRendererResources( ContentRenderer $renderer ): void;

}
