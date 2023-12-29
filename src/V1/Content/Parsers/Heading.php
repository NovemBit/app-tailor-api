<?php

namespace AT_API\V1\Content\Parsers;

use AT_API\V1\Content\Parsers\Traits\Parses_Element;
use AT_API\V1\Content\Parsers\Traits\Sets_Align_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Color_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Italic_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Size_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Full_Width_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Background_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Weight_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Transform_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Letter_Spacing_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Decoration_Prop;
use AT_API\V1\Content\Components\Core\Heading as Heading_Component;

class Heading extends Abstract_Parser {
	use Parses_Element;
	use Sets_Full_Width_Prop;
	use Sets_Align_Prop;
	use Sets_Font_Size_Prop;
	use Sets_Italic_Prop;
	use Sets_Font_Weight_Prop;
	use Sets_Text_Decoration_Prop;
	use Sets_Text_Transform_Prop;
	use Sets_Letter_Spacing_Prop;
	use Sets_Color_Prop;
	use Sets_Background_Prop;

	private static ?Heading $instance = null;

	protected function __construct() {
	}

	public static function get_instance(): Heading {
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
		$heading = new Heading_Component();

		if ( ! $dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) ) ) {
			return null;
		}

		$nodes = $dom->getElementsByTagName('*');
		$i = 2;
		$skipCount = 0;

		while ( $node = $nodes->item( $i++ ) ) {
			$this->parse_element( $node, $heading, null, $skipCount );
			$i += $skipCount;
		}

		$attrs = $block['attrs'] ?? null;
		$this->set_full_width_prop( $attrs, $heading );
		$this->set_level_prop( $attrs, $heading );
		$this->set_align_prop( $attrs, $heading );
		$this->set_font_size_prop( $attrs, $heading );
		$this->set_italic_prop( $attrs, $heading );
		$this->set_font_weight_prop( $attrs, $heading );
		$this->set_text_decoration_prop( $attrs, $heading );
		$this->set_text_transform_prop( $attrs, $heading );
		$this->set_font_size_prop_from_typography( $attrs, $heading );
		$this->set_letter_spacing_prop( $attrs, $heading );
		$this->set_color_prop_from_styles( $attrs, $heading );
		$this->set_background_prop_from_styles( $attrs, $heading );
		$this->set_color_prop( $attrs, $heading );
		$this->set_background_prop( $attrs, $heading );

		if ( ! isset( $attrs['level'] ) ) {
			$heading->set_prop( 'level', 2 );
		}

		return $heading;
	}

	private function set_level_prop( ?array $attrs, Heading_Component $component ) {
		$prop = 'level';
		$attr = $attrs[ $prop ] ?? null;

		if ( $attr ) {
			$component->set_prop( $prop, $attr );
		}
	}
}
