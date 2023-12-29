<?php

namespace AT_API\V1\Content\Parsers;

use AT_API\V1\Content\Components\Core\Html;
use AT_API\V1\Content\Parsers\Traits\Parses_Element;
use AT_API\V1\Content\Parsers\Traits\Sets_Color_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Align_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Italic_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Size_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Background_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Font_Weight_Prop;
use AT_API\V1\Content\Components\Core\Quote as Quote_Component;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Transform_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Letter_Spacing_Prop;
use AT_API\V1\Content\Parsers\Traits\Sets_Text_Decoration_Prop;
use AT_API\V1\Content\Components\Core\Paragraph as Paragraph_Component;

class Quote extends Abstract_Parser {
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

	private static ?Quote $instance = null;

	protected function __construct() {
	}

	public static function get_instance(): Quote {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function parse( array $block ) {
		$citation = $block['innerHTML'];

		if ( empty( $citation ) ) {
			return null;
		}

		$dom = self::get_dom_instance();
		$dom->loadHTML( mb_convert_encoding( $citation, 'HTML-ENTITIES', 'UTF-8' ) );
		$nodes = $dom->getElementsByTagName('*');
		$i = 2;
		$skipCount = 0;
		$paragraph = new Paragraph_Component();
		$quote = new Quote_Component();

		while ( $node = $nodes->item( $i++ ) ) {
			$this->parse_element( $node, $paragraph, null, $skipCount );
			$i += $skipCount;
		}

		$quote->set_prop( 'citation', $paragraph->content );

		if ( $block['innerBlocks'] ) {
			foreach ( $block['innerBlocks'] as $inner_block ) {
				$parser = $this->get_parser_by_block_name( $inner_block['blockName'] );

				if ( $parser ) {
					$content = $parser->parse( $inner_block );
				} else {
					$html = new Html();
					$html->set_content( $block['innerHTML'] );
					$content = $html;
				}

				$quote->add_content( $content );
			}
		}

		$attrs = $block['attrs'] ?? null;
		$this->set_style_prop( $attrs, $quote );
		$this->set_align_prop( $attrs, $quote );
		$this->set_font_size_prop( $attrs, $quote );
		$this->set_italic_prop( $attrs, $quote );
		$this->set_font_weight_prop( $attrs, $quote );
		$this->set_text_decoration_prop( $attrs, $quote );
		$this->set_text_transform_prop( $attrs, $quote );
		$this->set_font_size_prop_from_typography( $attrs, $quote );
		$this->set_letter_spacing_prop( $attrs, $quote );
		$this->set_color_prop_from_styles( $attrs, $quote );
		$this->set_background_prop_from_styles( $attrs, $quote );
		$this->set_color_prop( $attrs, $quote );
		$this->set_background_prop( $attrs, $quote );

		return $quote;
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
			default:
				return null;
		}
	}

	private function set_style_prop( ?array $attrs, Quote_Component $component ) {
		$prop = 'style';
		$attr = $attrs[ 'className' ] ?? null;

		if ( $attr ) {
			$mapping = array(
				'is-style-default' => 'default',
				'is-style-plain' => 'plain',
			);

			if ( isset( $mapping[ $attr ] ) ) {
				$component->set_prop( $prop, $mapping[ $attr ] );
			}
		}
	}
}
