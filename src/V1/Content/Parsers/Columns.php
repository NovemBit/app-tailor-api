<?php

namespace AT_API\V1\Content\Parsers;

use AT_API\V1\Content\Parsers\Traits\Parses_Element;
use AT_API\V1\Content\Parsers\Traits\Sets_Color_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Italic_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Size_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Full_Width_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Background_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Weight_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Transform_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Letter_Spacing_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Decoration_Prop;
use AT_API\V1\Content\Components\Core\Columns as Columns_Component;

class Columns extends Abstract_Parser {
	use Parses_Element;
	use Sets_Full_Width_Prop;
	use Sets_Font_Size_Prop;
	use Sets_Italic_Prop;
	use Sets_Font_Weight_Prop;
	use Sets_Text_Decoration_Prop;
	use Sets_Text_Transform_Prop;
	use Sets_Letter_Spacing_Prop;
	use Sets_Color_Prop;
	use Sets_Background_Prop;

	private static ?Columns $instance = null;
	private function __construct() {
	}

	public static function get_instance(): Columns {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function parse( array $block ) {
		$columns = new Columns_Component();

		if ( $block['innerBlocks'] ) {
			$column_parser = Column::get_instance();
			$count = count( $block['innerBlocks'] );

			foreach ( $block['innerBlocks'] as $inner_block ) {
				if ( ! $inner_block['attrs']['width'] ) {
					$inner_block['attrs']['width'] = round( 100 / $count, 2 ) . "%";
				}

				$content = $column_parser->parse( $inner_block );
				$columns->add_content( $content );
			}
		}

		$attrs = $block['attrs'] ?? null;
		$this->set_full_width_prop( $attrs, $columns );
		$this->set_is_stacked_on_mobile_prop( $attrs, $columns );
		$this->set_font_size_prop( $attrs, $columns );
		$this->set_italic_prop( $attrs, $columns );
		$this->set_font_weight_prop( $attrs, $columns );
		$this->set_text_decoration_prop( $attrs, $columns );
		$this->set_text_transform_prop( $attrs, $columns );
		$this->set_font_size_prop_from_typography( $attrs, $columns );
		$this->set_letter_spacing_prop( $attrs, $columns );
		$this->set_color_prop_from_styles( $attrs, $columns );
		$this->set_background_prop_from_styles( $attrs, $columns );
		$this->set_color_prop( $attrs, $columns );
		$this->set_background_prop( $attrs, $columns );

		return $columns;
	}

	private function set_is_stacked_on_mobile_prop( ?array $attrs, Columns_Component $component ) {
		$prop = 'isStackedOnMobile';
		$attr = $attrs[ $prop ] ?? null;

		if ( $attr ) {
			$component->set_prop( $prop, $attr );
		}
	}
}
