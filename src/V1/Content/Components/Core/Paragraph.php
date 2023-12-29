<?php

namespace AT_API\V1\Content\Components\Core;

use AT_API\V1\Content\Components\Base_Component;
use AT_API\V1\Content\Components\Traits\Adds_Content;
use AT_API\V1\Content\Components\Interfaces\Composite_Component;

class Paragraph extends Base_Component implements Composite_Component {
	use Adds_Content;

	public string $type = 'paragraph';
}
