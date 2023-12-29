<?php

namespace AT_API\V1\Content\Components\Core;

use AT_API\V1\Content\Components\Base_Component;
use AT_API\V1\Content\Components\Traits\Adds_Content;
use AT_API\V1\Content\Components\Interfaces\Composite_Component;

class Heading extends Base_Component implements Composite_Component {
	use Adds_Content;

	public string $type = 'heading';
	public array $content = array();

	public function set_content( array $content ) {
		$this->content = $content;
	}
}