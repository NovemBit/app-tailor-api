<?php

namespace AT_API\V1\Content\Components\Elements;

use AT_API\V1\Content\Components\Base_Component;
use AT_API\V1\Content\Components\Traits\Adds_Child;
use AT_API\V1\Content\Components\Interfaces\Composite_Element;

class Link extends Base_Component implements Composite_Element {
	use Adds_Child;
	public const PROP_HREF = 'href';
	public const PROP_RESOURCE_TYPE = 'resourceType';
	public const PROP_RESOURCE_ID = 'resourceId';
	public const PROP_POST_TYPE = 'postType';
	public const PROP_TAXONOMY = 'taxonomy';

	public function __construct() {
		$this->type = 'link';
		$this->eligible_props = array(
			self::PROP_HREF,
			self::PROP_RESOURCE_TYPE,
			self::PROP_RESOURCE_ID,
			self::PROP_POST_TYPE,
			self::PROP_TAXONOMY,
		);
	}
}
