<?php

namespace AT_API\V1\Content\Parsers;

use AT_API\V1\Content\Parsers\Traits\Parses_Element;
use AT_API\V1\Content\Parsers\Traits\Sets_Width_Prop;
use AT_API\V1\Content\Components\Core\Image as Image_Component;

class Image extends Abstract_Parser {
	use Parses_Element;

	private static ?Image $instance = null;
	private function __construct() {
	}

	public static function get_instance(): Image {
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
		$dom->loadHTML( $content );
		$nodes = $dom->getElementsByTagName('*');
		$i = 2;
		$skipCount = 0;
		$image = new Image_Component();

		while ( $node = $nodes->item( $i++ ) ) {
			$this->parse_element( $node, $image, null, $skipCount );
			$i += $skipCount;
		}

		$caption_content = [];

		foreach ( $image->content as $child ) {
			if ( $child->type === 'img' ) {
				$image->set_prop( 'src', $child->props['src'] );
			} elseif (
				$child->type === 'link'
				&& isset( $child->children[0]->type )
				&& $child->children[0]->type === 'img'
			) {
				$img = $child->children[0];
				$image->set_prop( 'src', $img->props['src'] );

				foreach ( $child->props as $key => $prop ) {
					$image->set_link_prop( $key, $prop );
				}
			} else {
				$caption_content[] = $child;
			}
		}

		unset( $image->content );

		if ( $caption_content ) {
			$image->set_prop( 'caption', $caption_content );
		}

		$attrs = $block['attrs'] ?? null;
		$this->set_image_dimensions( $attrs, $image );
		$this->set_alignment( $attrs, $image );

		if ( ! $image->get_prop( 'width' ) || ! $image->get_prop( 'height' ) ) {
			$src = $image->get_prop( 'src' );
			$img_info = getimagesize( $src );
			$width = $img_info[0] ?? null;
			$height = $img_info[1] ?? null;
			$image->set_prop( 'width', $width );
			$image->set_prop( 'height', $height );
		}

		return $image;
	}

	private function set_image_dimensions( ?array $attrs, Image_Component $component ) {
		$size = $attrs[ 'sizeSlug' ] ?? 'full';
		$width = $this->get_size_val( $attrs[ 'width' ] );
		$height = $this->get_size_val( $attrs[ 'height' ] );
		$id = $attrs['id'];
		$img_info = wp_get_attachment_image_src( $id, $size );

		if ( $width ) {
			$height = $height ?: $this->calculate_height( $width, $img_info );
		} elseif ( $height ) {
			$width = $this->calculate_width( $height, $img_info );
		} elseif ( $img_info ) {
				$width = $img_info[1];
				$height = $img_info[2];
		}

		$component->set_prop( 'width', $width );
		$component->set_prop( 'height', $height );
	}

	private function get_size_val( ?string $size_attr ): ?int {
		$val = null;

		if ( preg_match( '/(\d+)px/', $size_attr, $matches ) ) {
			$val = (int) $matches[1];
		}

		return $val;
	}

	private function calculate_height( int $width, array $img_info ): ?float {
		$height = null;

		if ( $img_info ) {
			$full_width = $img_info[1];
			$full_height = $img_info[2];
			$height = round( $width * $full_height / $full_width, 2 );
		}

		return $height;
	}
	private function calculate_width( int $height, array $img_info ): ?float {
		$width = null;

		if ( $img_info ) {
			$full_width = $img_info[1];
			$full_height = $img_info[2];
			$width = round( $height * $full_width / $full_height, 2 );
		}

		return $width;
	}

	private function set_alignment( ?array $attrs, Image_Component $component ) {
		$attr = $attrs[ 'align' ] ?? null;

		if ( in_array( $attr, array( 'left', 'center', 'right' ), true ) ) {
			$component->set_prop( 'align', $attr );
		} elseif ( $attr === 'full' ) {
			$component->set_prop( 'fullWidth', true );
		}
	}
}
