<?php

namespace AT_API\V1\Content\Parsers;

use AT_API\V1\Content\Parsers\Traits\Parses_Element;
use AT_API\V1\Content\Parsers\Traits\Sets_Width_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Color_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Italic_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Size_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Background_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Weight_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Transform_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Letter_Spacing_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Decoration_Prop;
use AT_API\V1\Content\Components\Core\Button as Button_Component;

class Button extends Abstract_Parser {
	use Parses_Element;
	use Sets_Width_Prop;
	use Sets_Font_Size_Prop;
	use Sets_Italic_Prop;
	use Sets_Font_Weight_Prop;
	use Sets_Text_Decoration_Prop;
	use Sets_Text_Transform_Prop;
	use Sets_Letter_Spacing_Prop;
	use Sets_Color_Prop;
	use Sets_Background_Prop;

	private static ?Button $instance = null;
	private function __construct() {
	}

	public static function get_instance(): Button {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function parse( array $block ) {
		$content = $block['innerHTML'];

		if ( empty( $content ) ) {
			return null;
		}

		$dom = self::get_dom_instance();
		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
		$nodes = $dom->getElementsByTagName('*');
		$i = 2;
		$skipCount = 0;
		$button = new Button_Component();

		while ( $node = $nodes->item( $i++ ) ) {
			$this->parse_element( $node, $button, null, $skipCount );
			$i += $skipCount;
		}

		foreach ( $button->content as $child ) {
			if ( $child->type === 'link' ) {
				foreach ( $child->props as $key => $prop ) {
					$button->set_link_prop( $key, $prop );
				}

				$button->content = array();

				if ( isset( $child->children ) ) {
					foreach ( $child->children as $link_child ) {
						$button->add_content( $link_child );
					}
				}

				break;
			}
		}

		$attrs = $block['attrs'] ?? null;
		$this->set_width_prop( $attrs, $button );
		$this->set_outline_prop( $attrs, $button );
		$this->set_font_size_prop_from_typography( $attrs, $button );
		$this->set_border_radius_prop( $attrs, $button );
		$this->set_italic_prop( $attrs, $button );
		$this->set_font_weight_prop( $attrs, $button );
		$this->set_text_decoration_prop( $attrs, $button );
		$this->set_text_transform_prop( $attrs, $button );
		$this->set_font_size_prop( $attrs, $button );
		$this->set_letter_spacing_prop( $attrs, $button );
		$this->set_color_prop_from_styles( $attrs, $button );
		$this->set_background_prop_from_styles( $attrs, $button );
		$this->set_color_prop( $attrs, $button );
		$this->set_background_prop( $attrs, $button );

		return $button;
	}

	private function set_outline_prop( ?array $attrs, Button_Component $component ) {
		$prop = 'outline';

		if ( isset( $attrs['className'] ) && 'is-style-outline' === $attrs['className'] ) {
			$component->set_prop( $prop, $attrs[ $prop ] );
		}
	}

	private function set_border_radius_prop( ?array $attrs, Button_Component $component ) {
		$prop = 'borderRadius';
		$attr = $attrs[ 'style' ] ?? null;

		if ( $attr && isset( $attr[ 'border' ][ 'radius' ] ) ) {
			$radius = $attr[ 'border' ][ 'radius' ];
			preg_match( '/^\d+/', $radius, $matches );

			if ( is_numeric( $matches[ 0 ] ) ) {
				$component->set_prop( $prop, ( int ) $matches[ 0 ] );
			}
		}
	}
}
