<?php

namespace AT_API\V1\Content\Components\Interfaces;

use AT_API\V1\Content\Components\Base_Component;

interface Composite_Element
{
	public function add_child( Base_Component $component ): void;
}
