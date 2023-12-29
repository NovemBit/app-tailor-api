<?php

namespace AT_API\V1\Content\Parsers;

use AT_API\V1\Content\Components\Core\Html;
use AT_API\V1\Content\Parsers\Traits\Parses_Element;
use AT_API\V1\Content\Parsers\Traits\Sets_Color_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Italic_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Size_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Background_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Weight_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Transform_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Letter_Spacing_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Decoration_Prop;
use AT_API\V1\Content\Components\Core\Column as Column_Component;

class Column extends Abstract_Parser {
	use Parses_Element;
	use Sets_Font_Size_Prop;
	use Sets_Italic_Prop;
	use Sets_Font_Weight_Prop;
	use Sets_Text_Decoration_Prop;
	use Sets_Text_Transform_Prop;
	use Sets_Letter_Spacing_Prop;
	use Sets_Color_Prop;
	use Sets_Background_Prop;

	private static ?Column $instance = null;

	private function __construct() {
	}

	public static function get_instance(): Column {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function parse( array $block ) {
		$column  = new Column_Component();

		if ( $block['innerBlocks'] ) {
			foreach ( $block['innerBlocks'] as $inner_block ) {
				$parser = $this->get_parser_by_block_name( $inner_block['blockName'] );

				if ( $parser ) {
					$content = $parser->parse( $inner_block );
				} else {
					$content = new Html();
					$content->set_content( $block['innerHTML'] );
				}

				$column->add_content( $content );
			}
		}

		$attrs = $block['attrs'] ?? null;
		$this->set_vertical_alignment_prop( $attrs, $column );
		$this->set_column_width_prop( $attrs, $column );
		$this->set_font_size_prop( $attrs, $column );
		$this->set_italic_prop( $attrs, $column );
		$this->set_font_weight_prop( $attrs, $column );
		$this->set_text_decoration_prop( $attrs, $column );
		$this->set_text_transform_prop( $attrs, $column );
		$this->set_font_size_prop_from_typography( $attrs, $column );
		$this->set_letter_spacing_prop( $attrs, $column );
		$this->set_color_prop_from_styles( $attrs, $column );
		$this->set_background_prop_from_styles( $attrs, $column );
		$this->set_color_prop( $attrs, $column );
		$this->set_background_prop( $attrs, $column );

		return $column;
	}

	private function get_parser_by_block_name( string $block_name ) {
		switch ( $block_name ) {
			case 'core/paragraph':
				return Paragraph::get_instance();
			case 'core/heading':
				return Heading::get_instance();
			case 'core/separator':
				return Separator::get_instance();
			case 'core/image':
				return Image::get_instance();
			case 'core/columns':
				return Columns::get_instance();
			case 'core/list':
				return Listing::get_instance();
			case 'core/buttons':
				return Buttons::get_instance();
			case 'core/quote':
				return Quote::get_instance();
			case 'core/embed':
				return Embed::get_instance();
			default:
				return null;
		}
	}

	private function set_column_width_prop( ?array $attrs, Column_Component $component ) {
		$prop = 'width';
		$attr = $attrs[ $prop ] ?? null;

		if ( $attr ) {
			$width = (float) substr( $attr, 0, -1 );
			$component->set_prop( $prop, $width );
		}
	}

	private function set_vertical_alignment_prop( ?array $attrs, Column_Component $component ) {
		$prop = 'verticalAlignment';
		$attr = $attrs[ $prop ] ?? null;

		if ( $attr ) {
			$component->set_prop( $prop, $attr );
		}
	}
}
