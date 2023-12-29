<?php

namespace AT_API\V1\Content\Components\Traits;

trait Sets_Content
{
	public string $content = '';

	public function set_content( string $content ): void {
		$this->content = $content;
	}
}
