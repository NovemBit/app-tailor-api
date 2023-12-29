<?php

namespace AT_API\V1\Content\Components\Traits;

use AT_API\V1\Content\Components\Base_Component;

trait Adds_Child
{
	public array $children = array();

	public function add_child( Base_Component $component ): void {
		$this->children[] = $component;
	}
}
