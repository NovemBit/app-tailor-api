<?php

namespace AT_API\V1\Content\Parsers;

use AT_API\V1\Content\Enum\Tag;
use AT_API\V1\Content\Enum\Property;
use AT_API\V1\Content\Parsers\Traits\Parses_Element;
use AT_API\V1\Content\Parsers\Traits\Sets_Align_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Color_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Italic_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Size_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Background_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Weight_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Transform_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Letter_Spacing_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Decoration_Prop;
use AT_API\V1\Content\Components\Core\Pullquote as Pullquote_Component;

class Pullquote extends Abstract_Parser
{
	use Parses_Element;
	use Sets_Align_Prop;
	use Sets_Font_Size_Prop;
	use Sets_Italic_Prop;
	use Sets_Font_Weight_Prop;
	use Sets_Text_Decoration_Prop;
	use Sets_Text_Transform_Prop;
	use Sets_Letter_Spacing_Prop;
	use Sets_Color_Prop;
	use Sets_Background_Prop;

	private static ?Pullquote $instance = null;

	protected function __construct() {
	}

	public static function get_instance(): Pullquote {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function parse(array $block)
	{
		$content = $block['innerHTML'];

		if ( empty( $content ) ) {
			return null;
		}

		$dom = self::get_dom_instance();
		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
		$nodes = $dom->getElementsByTagName('*');
		$i = 2;
		$skipCount = 0;
		$pullquote = new Pullquote_Component();
		$citation = array();

		while ( $node = $nodes->item( $i++ ) ) {
			$this->parse_element( $node, $pullquote, null, $skipCount );
			$i += $skipCount;
		}

		foreach ($pullquote->content as $key => $item) {
			$private_props = $item->get_private_props();

			if (
				isset( $private_props[ Property::TAG ] )
				&& $private_props[ Property::TAG ] === Tag::CITE
			) {
				$citation[] = $item;
				unset( $pullquote->content[ $key ] );
			}
		}

		$pullquote->set_prop( 'citation', $citation );

		$attrs = $block['attrs'] ?? null;
		$this->set_align_prop( $attrs, $pullquote );
		$this->set_font_size_prop( $attrs, $pullquote );
		$this->set_italic_prop( $attrs, $pullquote );
		$this->set_font_weight_prop( $attrs, $pullquote );
		$this->set_text_decoration_prop( $attrs, $pullquote );
		$this->set_text_transform_prop( $attrs, $pullquote );
		$this->set_font_size_prop_from_typography( $attrs, $pullquote );
		$this->set_letter_spacing_prop( $attrs, $pullquote );
		$this->set_color_prop_from_styles( $attrs, $pullquote );
		$this->set_background_prop_from_styles( $attrs, $pullquote );
		$this->set_color_prop( $attrs, $pullquote );
		$this->set_background_prop( $attrs, $pullquote );

		return $pullquote;
	}
}