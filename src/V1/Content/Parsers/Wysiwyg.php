<?php

namespace AT_API\V1\Content\Parsers;

use AT_API\V1\Content\Enum\Tag;
use AT_API\V1\Content\Components\Base_Component;
use AT_API\V1\Content\Parsers\Traits\Parses_Element;
use AT_API\V1\Content\Components\Interfaces\Composite_Component;
use AT_API\V1\Content\Components\Core\Listing as Listing_Component;
use AT_API\V1\Content\Components\Core\Heading as Heading_Component;
use AT_API\V1\Content\Components\Core\List_Item as List_Item_Component;
use AT_API\V1\Content\Components\Core\Paragraph as Paragraph_Component;

class Wysiwyg extends Abstract_Parser
{
	use Parses_Element;

	private static ?Wysiwyg $instance = null;

	protected function __construct() {
	}

	public static function get_instance(): Wysiwyg {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function parse( array $block ) {
		$content = $block['innerHTML'];

		if ( ! $content ) {
			return null;
		}

		$content = trim( $content );
		$dom = self::get_dom_instance();
		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
		$nodes = $dom->getElementsByTagName('*');
		$i = 2;
		$components = array();

		while ( $node = $nodes->item( $i++ ) ) {
			$skipCount = 0;
			$component = $this->get_core_component_by_tag( $node->nodeName );

			if ( ! $component ) {
				continue;
			}

			$this->parse_element( $node, $component, null, $skipCount );
			$i += $skipCount;

			if ( empty( $component->content ) ) {
				continue;
			}

			if ( is_a( $component, Listing_Component::class ) ) {
				$component = $component->content[0];
				$component->content = array_values( array_filter( $component->content, function( $item ) {
					return $item instanceof List_Item_Component;
				} ) );
			}

			$this->set_component_props( $component, $node->nodeName );
			$components[] = $component;
		}

		return $components;
	}

	public function analyze( array $block ) {
		$content = $block['innerHTML'];
		$errors = array();

		if ( ! $content ) {
			return $errors;
		}

		$content = trim( $content );
		$dom = self::get_dom_instance();
		$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
		$nodes = $dom->getElementsByTagName('*');
		$i = 2;

		while ( $node = $nodes->item( $i++ ) ) {
			$skipCount = 0;
			$component = $this->get_core_component_by_tag( $node->nodeName );

			if ( ! $component ) {
				$errors[] = array(
					'tag' => $node->nodeName,
					'message' => 'No corresponding component.',
				);
				continue;
			}

			$this->parse_element( $node, $component, null, $skipCount );
			$i += $skipCount;
			$component_errors = $component->get_errors();

			if ( $component_errors ) {
				$errors = array_merge( $errors, $component_errors );
			} elseif ( empty( $component->content ) ) {
				$errors[] = array(
					'tag' => $node->nodeName,
					'message' => 'No content.',
				);
			}
		}

		return $errors;
	}

	private function get_core_component_by_tag( ?string $tag ): ?Base_Component {
		switch ( $tag ) {
			case Tag::UL:
			case Tag::OL:
				return new Listing_Component();
			case Tag::P:
			case Tag::DIV:
				return new Paragraph_Component();
			case Tag::H1:
			case Tag::H2:
			case Tag::H3:
			case Tag::H4:
			case Tag::H5:
			case Tag::H6:
				return new Heading_Component();
			default:
				return null;
		}
	}

	private function set_component_props( Composite_Component $component, ?string $tag ) {
		switch ( $tag ) {
			case Tag::OL:
				$component->set_prop( 'ordered', true );
				break;
			case Tag::H1:
				$component->set_prop( 'level', 1 );
				break;
			case Tag::H2:
				$component->set_prop( 'level', 2 );
				break;
			case Tag::H3:
				$component->set_prop( 'level', 3 );
				break;
			case Tag::H4:
				$component->set_prop( 'level', 4 );
				break;
			case Tag::H5:
				$component->set_prop( 'level', 5 );
				break;
			case Tag::H6:
				$component->set_prop( 'level', 6 );
				break;
		}
	}
}
