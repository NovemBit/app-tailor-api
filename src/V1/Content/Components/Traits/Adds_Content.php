<?php

namespace AT_API\V1\Content\Components\Traits;

use AT_API\V1\Content\Components\Base_Component;

trait Adds_Content
{
	public array $content = array();

	public function add_content( Base_Component $component ): void {
		$this->content[] = $component;
	}
}
