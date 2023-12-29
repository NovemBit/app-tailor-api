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
use AT_API\V1\Content\Components\Core\Buttons as Buttons_Component;

class Buttons extends Abstract_Parser {
	use Parses_Element;
	use Sets_Font_Size_Prop;
	use Sets_Italic_Prop;
	use Sets_Font_Weight_Prop;
	use Sets_Text_Decoration_Prop;
	use Sets_Text_Transform_Prop;
	use Sets_Letter_Spacing_Prop;
	use Sets_Color_Prop;
	use Sets_Background_Prop;

	private static ?Buttons $instance = null;
	private function __construct() {
	}

	public static function get_instance(): Buttons {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function parse( array $block ) {
		$buttons = new Buttons_Component();

		if ( $block['innerBlocks'] ) {
			$button_parser = Button::get_instance();

			foreach ( $block['innerBlocks'] as $inner_block ) {
				$content = $button_parser->parse( $inner_block );

				if ( $content ) {
					$buttons->add_content( $content );
				}
			}
		}

		if ( empty( $buttons->content ) ) {
			return null;
		}

		$attrs = $block['attrs'] ?? null;
		$this->set_justify_content_prop( $attrs, $buttons );
		$this->set_orientation_prop( $attrs, $buttons );
		$this->set_wrap_prop( $attrs, $buttons );
		$this->set_font_size_prop( $attrs, $buttons );
		$this->set_italic_prop( $attrs, $buttons );
		$this->set_font_weight_prop( $attrs, $buttons );
		$this->set_text_decoration_prop( $attrs, $buttons );
		$this->set_text_transform_prop( $attrs, $buttons );
		$this->set_font_size_prop_from_typography( $attrs, $buttons );
		$this->set_letter_spacing_prop( $attrs, $buttons );
		$this->set_color_prop_from_styles( $attrs, $buttons );
		$this->set_background_prop_from_styles( $attrs, $buttons );
		$this->set_color_prop( $attrs, $buttons );
		$this->set_background_prop( $attrs, $buttons );

		return $buttons;
	}

	private function set_justify_content_prop( ?array $attrs, Buttons_Component $component ) {
		$prop = 'justifyContent';
		$attr = $attrs[ 'layout' ] ?? null;

		if ( $attr && isset( $attr['justifyContent'] ) ) {
			$component->set_prop( $prop, $attr['justifyContent'] );
		}
	}

	private function set_orientation_prop( ?array $attrs, Buttons_Component $component ) {
		$prop = 'orientation';
		$attr = $attrs[ 'layout' ] ?? null;

		if ( $attr && isset( $attr['orientation'] ) ) {
			$component->set_prop( $prop, $attr['orientation'] );
		}
	}

	private function set_wrap_prop( ?array $attrs, Buttons_Component $component ) {
		$prop = 'wrap';
		$attr = $attrs[ 'layout' ] ?? null;

		if ( $attr && isset( $attr['flexWrap'] ) ) {
			if ( 'wrap' === $attr['flexWrap'] ) {
				$component->set_prop( $prop, true );
			} elseif ( 'nowrap' === $attr['flexWrap'] ) {
				$component->set_prop( $prop, false );
			}
		}
	}
}
