<?php

namespace AT_API\V1\Content\Parsers\Traits;

use DOMText;
use DOMNode;
use DOMComment;
use DOMNamedNodeMap;
use AT_API\V1\Content\Enum\Tag;
use AT_API\V1\Content\Enum\Property;
use AT_API\V1\Content\Components\Core\Listing;
use AT_API\V1\Content\Components\Elements\Text;
use AT_API\V1\Content\Components\Elements\Link;
use AT_API\V1\Content\Components\Base_Component;
use AT_API\V1\Content\Components\Elements\Image;
use AT_API\V1\Content\Components\Core\List_Item;
use AT_API\V1\Content\Components\Interfaces\Composite_Element;
use AT_API\V1\Content\Components\Interfaces\Composite_Component;

trait Parses_Element {
	use Sets_Link_Additional_Props;

	protected function parse_element( DOMNode $node, Base_Component $parent, ?array $props, &$skipCount, $private_props = array() ) {
		$component = $this->get_component_by_tag( $node->nodeName );

		if (
			! $component
			&& ! in_array( $node->nodeName, Tag::get_all(), true )
			&& ! str_contains( $node->nodeName, '#' )
		) {
			$parent->add_error( array(
				'tag' => $node->nodeName,
				'message' => 'No corresponding component.',
			) );
		}

		if ( ! $props ) {
			$props = array();
		}

		$prop = $this->get_prop_by_tag( $node->nodeName );
		$private_prop = $this->get_private_prop_by_tag( $node->nodeName );
		$attrs = $this->get_node_attrs( $node );

		if ( is_a( $component, Link::class ) && empty( $attrs ) ) {
			$parent->add_error( array(
				'tag' => $node->nodeName,
				'message' => 'Missing required attribute.',
			) );
			return;
		}

		if ( $prop ) {
			$props[ $prop['name'] ] = $prop['value'];
		}

		if ( $private_prop ) {
			$private_props[ $private_prop['name'] ] = $private_prop['value'];
		}

		if ( $attrs ) {
			foreach ( $attrs as $attr ) {
				$props[ $attr['name'] ] = $attr['value'];
			}
		}

		if ( is_a( $component, Text::class ) ) {
			$component->content = $node->textContent;

			if ( $props ) {
				foreach ( $props as $key => $prop ) {
					$component->add_prop( $key, $prop );
				}
			}

			if ( $private_props ) {
				foreach ( $private_props as $key => $prop ) {
					$component->add_private_prop( $key, $prop );
				}
			}
		}

		if ( is_a( $component, Base_Component::class ) ) {
			if ( $attrs ) {
				foreach ( $attrs as $attr ) {
					$component->add_prop( $attr['name'], $attr['value'] );
				}
			}

			if ( is_a( $parent, Composite_Component::class ) ) {
				$ac = $parent->get_allowed_children();

				if ( empty( $ac ) || in_array( get_class($component), $ac, true ) ) {
					$parent->add_content( $component );
				}
			} elseif ( is_a( $parent, Composite_Element::class ) ) {
				$parent->add_child( $component );
			}
		}

		if ( is_a( $component, Link::class ) ) {
			$component = $this->set_link_additional_props( $component );
		}

		if ( $node->hasChildNodes() ) {
			$i = 0;

			$composite_components = array(
				Link::class,
				Listing::class,
				List_Item::class,
			);

			while ( $child = $node->childNodes->item( $i++ ) ) {
				$obj = $parent;

				foreach ( $composite_components as $composite_component ) {
					if ( is_a( $component, $composite_component ) ) {
						$obj = $component;
					}
				}

				$this->parse_element( $child, $obj, $props, $skipCount, $private_props );

				if ( ! $child instanceof DOMText && ! $child instanceof DOMComment ) {
					$skipCount++;
				}
			}
		}
	}

	private function get_prop_by_tag( string $tag ): array {
		switch ( $tag ) {
			case Tag::STRONG:
			case Tag::B:
				return array(
					'name' => Text::PROP_FONT_WEIGHT,
					'value' => 700,
				);
			case Tag::EM:
				return array(
					'name' => Text::PROP_ITALIC,
					'value' => true,
				);
			case Tag::S:
				return array(
					'name' => Text::PROP_STRIKETHROUGH,
					'value' => true,
				);
			case Tag::SUB:
				return array(
					'name' => Text::PROP_SUBSCRIPT,
					'value' => true,
				);
			case Tag::SUP:
				return array(
					'name' => Text::PROP_SUPERSCRIPT,
					'value' => true,
				);
			case Tag::U:
				return array(
					'name' => Text::PROP_UNDERLINE,
					'value' => true,
				);
			default:
				return array();
		}
	}

	private function get_private_prop_by_tag( string $tag ): array {
		switch ( $tag ) {
			case Tag::CITE:
				return array(
					'name' => Property::TAG,
					'value' => Tag::CITE,
				);
			default:
				return array();
		}
	}

	private function get_component_by_tag( $tag ): ?Base_Component {
		switch ( $tag ) {
			case Tag::TEXT:
				return new Text();
			case Tag::A:
				return new Link();
			case Tag::IMG:
				return new Image();
			case Tag::UL:
			case Tag::OL:
				return new Listing();
			case Tag::LI:
				return new List_Item();
			default:
				return null;
		}
	}

	private function get_node_attrs( DOMNode $node ): array {
		$attrs = [];

		/** @var DOMNamedNodeMap $attributes */
		$attributes = $node->attributes;

		switch ( $node->nodeName ) {
			case 'a':
				$href = $attributes->getNamedItem( 'href' );
				$value = null;

				if ( $href ) {
					$value = $href->nodeValue;

					if ( '/' === substr( $value, 0, 1 ) ) {
						$value = get_site_url() . $value;
					}
				}

				if ( ! empty( $value ) ) {
					$attrs[] = array(
						'name' => 'href',
						'value' => $value,
					);
				}
				break;
			case 'p':
			case 'mark':
				$class = $attributes->getNamedItem( 'class' );
				$style = $attributes->getNamedItem( 'style' );

				if ( $class ) {
					$colors = array(
						'black' => '#000000',
						'white' => '#ffffff',
						'light-gray' => '#666666',
						'primary' => '#db6720',
						'secondary' => '#b2d13f',
					);

					foreach ( $colors as $key => $color ) {
						if ( str_contains( $class->nodeValue, 'has-' . $key . '-color'  ) ) {
							list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
							$attrs[] = array(
								'name' => Text::PROP_COLOR,
								'value' => array(
									'r' => $r,
									'g' => $g,
									'b' => $b,
									'a' => 1,
								),
							);
						}
					}
				}

				if ( $style ) {
					$styles = explode( ';', $style->nodeValue );

					foreach ( $styles as $item ) {
						if ( preg_match( '/^background-color:(.+)/', $item, $matches ) ) {
							if ( $matches[1] !== 'rgba(0, 0, 0, 0)' ) {
								list($r, $g, $b) = sscanf($matches[1], "#%02x%02x%02x");
								$attrs[] = array(
									'name' => Text::PROP_BACKGROUND,
									'value' => array(
										'r' => $r,
										'g' => $g,
										'b' => $b,
										'a' => 1,
									),
								);
							}
						}

						if ( preg_match( '/^color:(.+)/', $item, $matches ) ) {
							if ( $matches[1] !== 'rgba(0, 0, 0, 0)' ) {
								list($r, $g, $b) = sscanf($matches[1], "#%02x%02x%02x");
								$attrs[] = array(
									'name' => Text::PROP_COLOR,
									'value' => array(
										'r' => $r,
										'g' => $g,
										'b' => $b,
										'a' => 1,
									),
								);
							}
						}
					}
				}
				break;
			case 'img':
				$src_attr = $attributes->getNamedItem( 'src' );
				$width_attr = $attributes->getNamedItem( 'width' );
				$height_attr = $attributes->getNamedItem( 'height' );
				$src = $src_attr->nodeValue ?? null;
				$width = isset( $width_attr->nodeValue ) ? (int) $width_attr->nodeValue : null;
				$height = isset( $height_attr->nodeValue ) ? (int) $height_attr->nodeValue : null;
				$style = $attributes->getNamedItem( 'style' );

				if ( $style && $src ) {
					if ( preg_match( '/width: (\d+)px/', $style->nodeValue, $matches ) ) {
						$width = (int) $matches[1];
						$attachment_id = attachment_url_to_postid( $src->nodeValue );

						if ( $attachment_id ) {
							$img_info = wp_get_attachment_image_src( $attachment_id, 'full' );

							if ( $img_info ) {
								$full_width = $img_info[1];
								$full_height = $img_info[2];
								$height = round( $width * $full_height / $full_width, 2 );
							}
						}
					}
				}

				if ( ( ! $width || ! $height ) && $src_attr->nodeValue ) {
					$img_info = getimagesize( $src_attr->nodeValue );
					$width = $img_info[0] ?? null;
					$height = $img_info[1] ?? null;
				}

				$attrs[] = array(
					'name' => Image::PROP_SRC,
					'value' => $src,
				);

				$attrs[] = array(
					'name' => Image::PROP_WIDTH,
					'value' => $width,
				);

				$attrs[] = array(
					'name' => Image::PROP_HEIGHT,
					'value' => $height,
				);

				break;
			default:
				break;
		}

		return $attrs;
	}
}
