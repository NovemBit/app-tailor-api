<?php

namespace AT_API\V1\Content\Components\Core;

use AT_API\V1\Content\Components\Base_Component;
use AT_API\V1\Content\Components\Traits\Adds_Content;
use AT_API\V1\Content\Components\Interfaces\Composite_Component;

class Listing extends Base_Component implements Composite_Component {
	use Adds_Content;

	public string $type = 'list';
	protected array $allowed_children = array(
		List_Item::class,
		Listing::class,
	);
}
