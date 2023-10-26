<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\UseCases\Embed;

class EmbedRequest {

	public function __construct(
		public string $fileUrl,
		public ?string $language,
		public ?bool $showLineNumbers,
		public ?string $showSpecificLines,
		public bool $showEditButton
	) {
	}

}
