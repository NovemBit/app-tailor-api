<?php

namespace AT_API\V1\Content\Parsers;

use AT_API\V1\Content\Parsers\Traits\Parses_Element;
use AT_API\V1\Content\Parsers\Traits\Sets_Color_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Italic_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Size_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Background_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Weight_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Transform_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Letter_Spacing_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Decoration_Prop;
use AT_API\V1\Content\Components\Core\Listing as Listing_Component;

class Listing extends Abstract_Parser {
	use Parses_Element;
	use Sets_Font_Size_Prop;
	use Sets_Italic_Prop;
	use Sets_Font_Weight_Prop;
	use Sets_Text_Decoration_Prop;
	use Sets_Text_Transform_Prop;
	use Sets_Letter_Spacing_Prop;
	use Sets_Color_Prop;
	use Sets_Background_Prop;
	private static ?Listing $instance = null;

	protected function __construct() {
	}

	public static function get_instance(): Listing {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function parse( array $block ) {
		$list = new Listing_Component();

		if ( $block['innerBlocks'] ) {
			$list_parser = List_Item::get_instance();

			foreach ( $block['innerBlocks'] as $inner_block ) {
				$content = $list_parser->parse( $inner_block );

				if ( $content ) {
					$list->add_content( $content );
				}
			}
		}

		$attrs = $block['attrs'] ?? null;
		$this->set_ordered_prop( $attrs, $list );
		$this->set_reserved_prop( $attrs, $list );
		$this->set_start_value_prop( $attrs, $list );
		$this->set_font_size_prop( $attrs, $list );
		$this->set_italic_prop( $attrs, $list );
		$this->set_font_weight_prop( $attrs, $list );
		$this->set_text_decoration_prop( $attrs, $list );
		$this->set_text_transform_prop( $attrs, $list );
		$this->set_font_size_prop_from_typography( $attrs, $list );
		$this->set_letter_spacing_prop( $attrs, $list );
		$this->set_color_prop_from_styles( $attrs, $list );
		$this->set_background_prop_from_styles( $attrs, $list );
		$this->set_color_prop( $attrs, $list );
		$this->set_background_prop( $attrs, $list );

		return $list;
	}

	private function set_ordered_prop( ?array $attrs, Listing_Component $component ) {
		$prop = 'ordered';
		$attr = $attrs[ $prop ] ?? null;

		if ( $attr ) {
			$component->set_prop( $prop, $attr );
		}
	}

	private function set_reserved_prop( ?array $attrs, Listing_Component $component ) {
		$prop = 'reserved';
		$attr = $attrs[ $prop ] ?? null;

		if ( $attr ) {
			$component->set_prop( $prop, $attr );
		}
	}

	private function set_start_value_prop( ?array $attrs, Listing_Component $component ) {
		$prop = 'startValue';
		$attr = $attrs[ 'start' ] ?? null;

		if ( $attr ) {
			$component->set_prop( $prop, $attr );
		}
	}
}
