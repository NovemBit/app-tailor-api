<?php

namespace AT_API\V1\Content\Components\Interfaces;

use AT_API\V1\Content\Components\Base_Component;

interface Composite_Component
{
	public function add_content( Base_Component $component ): void;
}
