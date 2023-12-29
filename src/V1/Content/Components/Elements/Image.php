<?php

namespace AT_API\V1\Content\Components\Elements;

use AT_API\V1\Content\Components\Base_Component;

class Image extends Base_Component {
	public const PROP_SRC = 'src';
	public const PROP_WIDTH = 'width';
	public const PROP_HEIGHT = 'height';

	public function __construct() {
		$this->type = 'img';
		$this->eligible_props = array(
			self::PROP_SRC,
			self::PROP_WIDTH,
			self::PROP_HEIGHT,
		);
	}
}
