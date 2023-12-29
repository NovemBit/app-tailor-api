<?php

namespace AT_API\V1\Content\Parsers;

use AT_API\V1\Content\Parsers\Traits\Parses_Element;
use AT_API\V1\Content\Components\Core\List_Item as List_Item_Component;

class List_Item extends Abstract_Parser {
	use Parses_Element;
	private static ?List_Item $instance = null;

	protected function __construct() {
	}

	public static function get_instance(): List_Item {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function parse( array $block ) {
		$list_item  = new List_Item_Component();
		$option = $block['innerHTML'];

		if ( empty( $option ) ) {
			return null;
		}

		$dom = self::get_dom_instance();
		$dom->loadHTML( mb_convert_encoding( $option, 'HTML-ENTITIES', 'UTF-8' ) );
		$nodes = $dom->getElementsByTagName('*');
		$i = 2;
		$skipCount = 0;

		while ( $node = $nodes->item( $i++ ) ) {
			$this->parse_element( $node, $list_item, null, $skipCount );
			$i += $skipCount;
		}

		/** @var List_Item_Component $list_item */
		$list_item = $list_item->content[0];

		if ( $block['innerBlocks'] ) {
			foreach ( $block['innerBlocks'] as $inner_block ) {
				$parser = Listing::get_instance();
				$content = $parser->parse( $inner_block );
				$list_item->add_content( $content );
			}
		}

		return $list_item;
	}
}
