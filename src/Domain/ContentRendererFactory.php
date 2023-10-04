<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain;

use ProfessionalWiki\ExternalContent\Domain\ContentRenderer\RendererConfig;

interface ContentRendererFactory {

	public function createContentRenderer( RendererConfig $config ): ContentRenderer;

}
