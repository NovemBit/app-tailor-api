<?php

namespace AT_API\V1\Content\Components\Elements;

use AT_API\V1\Content\Components\Base_Component;
use AT_API\V1\Content\Components\Traits\Sets_Content;
use AT_API\V1\Content\Components\Interfaces\Raw_Element;

class Text extends Base_Component implements Raw_Element {
	use Sets_Content;

	public const PROP_COLOR = 'color';
	public const PROP_BACKGROUND = 'background';
	public const PROP_FONT_WEIGHT = 'fontWeight';
	public const PROP_ITALIC = 'italic';
	public const PROP_STRIKETHROUGH = 'strikethrough';
	public const PROP_SUBSCRIPT = 'subscript';
	public const PROP_SUPERSCRIPT = 'superscript';
	public const PROP_UNDERLINE = 'underline';

	public function __construct() {
		$this->type = 'text';
		$this->eligible_props = array(
			self::PROP_FONT_WEIGHT,
			self::PROP_ITALIC,
			self::PROP_COLOR,
			self::PROP_BACKGROUND,
			self::PROP_STRIKETHROUGH,
			self::PROP_SUBSCRIPT,
			self::PROP_SUPERSCRIPT,
			self::PROP_UNDERLINE,
		);
	}
}
