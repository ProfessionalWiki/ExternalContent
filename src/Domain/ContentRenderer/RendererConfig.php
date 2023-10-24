<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Domain\ContentRenderer;

class RendererConfig {

	public function __construct(
		public string $fileExtension,
		public string $language,
		public bool $showLineNumbers,
		public bool $showEditButton = false,
		public bool $render
	) {
	}

}
