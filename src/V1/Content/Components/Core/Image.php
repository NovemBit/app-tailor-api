<?php

namespace AT_API\V1\Content\Components\Core;

use AT_API\V1\Content\Components\Base_Component;
use AT_API\V1\Content\Components\Traits\Adds_Content;
use AT_API\V1\Content\Components\Interfaces\Composite_Component;

class Image extends Base_Component implements Composite_Component {
	use Adds_Content;

	public string $type = 'image';
	public ?array $link = null;

	public function get_prop( string $key ) {
		return $this->props[ $key ];
	}

	public function set_link_prop( string $key, $value ) {
		$this->link[ $key ] = $value;
	}
}
