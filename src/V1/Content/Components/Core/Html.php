<?php

namespace AT_API\V1\Content\Components\Core;

use AT_API\V1\Content\Components\Base_Component;
use AT_API\V1\Content\Components\Traits\Sets_Content;
use AT_API\V1\Content\Components\Interfaces\Raw_Component;

class Html extends Base_Component implements Raw_Component {
	use Sets_Content;

	public string $type = 'html';
}
