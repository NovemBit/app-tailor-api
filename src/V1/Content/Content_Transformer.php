<?php

namespace AT_API\V1\Content;

use AT_API\V1\Content\Parsers\Embed;
use AT_API\V1\Content\Parsers\Image;
use AT_API\V1\Content\Parsers\Quote;
use AT_API\V1\Content\Parsers\Buttons;
use AT_API\V1\Content\Parsers\Columns;
use AT_API\V1\Content\Parsers\Heading;
use AT_API\V1\Content\Parsers\Listing;
use AT_API\V1\Content\Parsers\Wysiwyg;
use AT_API\V1\Content\Parsers\Freeform;
use AT_API\V1\Content\Parsers\Paragraph;
use AT_API\V1\Content\Parsers\Pullquote;
use AT_API\V1\Content\Parsers\Separator;
use AT_API\V1\Content\Components\Core\Html;
use AT_API\V1\Content\Parsers\Abstract_Parser;

class Content_Transformer {
	public ?Abstract_Parser $parser = null;

	private function set_parser( ?string $block_name ): void {
        $block_name = $block_name ?: 'missing';
        $mapping = array(
            'core/paragraph' => Paragraph::get_instance(),
            'core/heading' => Heading::get_instance(),
            'core/separator' => Separator::get_instance(),
            'core/image' => Image::get_instance(),
            'core/columns' => Columns::get_instance(),
            'core/list' => Listing::get_instance(),
            'core/buttons' => Buttons::get_instance(),
            'core/quote' => Quote::get_instance(),
            'core/pullquote' => Pullquote::get_instance(),
            'core/embed' => Embed::get_instance(),
            'missing' => Freeform::get_instance(),
        );
        $mapping = apply_filters( 'app_tailor_content_parser', $mapping );

        $this->parser = $mapping[ $block_name ] ?? null;
	}

	public function transform( ?string $content, ?string $mode = null ) {
		$result = [];
		$is_block_editor = false;

        if ( ! $content ) {
            return $result;
        }

		if ( ( ! $mode || 'block-editor' === $mode ) && has_blocks( $content )  ) {
			$is_block_editor = true;
		}

		if ( $is_block_editor ) {
			$blocks = parse_blocks( $content );

			foreach ( $blocks as $block ) {
				if ( ! $block['blockName'] && empty( trim( $block['innerHTML'] ) ) ) {
					continue;
				}

				$this->set_parser( $block['blockName'] );

				if ( $this->parser ) {
					$block['innerHTML'] = str_replace( array( '<br>', '<br />' ), "\n", $block['innerHTML'] );
                    $parsed_content = $this->parser->parse( $block );

                    if ( $parsed_content ) {
                        $result[] = $parsed_content;
                        continue;
                    }
				}

                $html = new Html();
                $content = $this->get_block_content( $block );
                $html->set_content( $content );
                $result[] = $html;
			}
		} else {
            $wysiwyg_content = wpautop( $content );
			$wysiwyg_content = str_replace( array( '<br>', '<br />' ), "\n", $wysiwyg_content );
			$wysiwyg_content = mb_convert_encoding( $wysiwyg_content, 'HTML-ENTITIES', 'UTF-8' );
			$parser = Wysiwyg::get_instance();
            $parsed_content = $parser->parse( array( 'innerHTML' => $wysiwyg_content ) );
			$result = $parsed_content;
		}

		return $result;
	}

	public function analyze( ?string $content, ?string $mode = null ) {
		$errors = array();
		$is_block_editor = false;

		if ( ( null === $mode || 'block-editor' === $mode ) && has_blocks( $content )  ) {
			$is_block_editor = true;
		}

		if ( $is_block_editor ) {
			$blocks = parse_blocks( $content );

			foreach ( $blocks as $block ) {
				if ( ! $block['blockName'] ) {
					continue;
				}

				$this->set_parser( $block['blockName'] );

				if ( ! $this->parser ) {
					$errors[] = array(
						'block' => $block['blockName'],
						'message' => 'Block not supported',
					);
				}
			}
		} else {
			$wysiwyg_content = wpautop( $content );
			$wysiwyg_content = str_replace( array( '<br>', '<br />' ), "\n", $wysiwyg_content );
			$wysiwyg_content = mb_convert_encoding( $wysiwyg_content, 'HTML-ENTITIES', 'UTF-8' );
			$parser = Wysiwyg::get_instance();
			$errors = $parser->analyze( array( 'innerHTML' => $wysiwyg_content ) );
		}

		return $errors;
	}

	private function get_block_content( array $block ) {
		$content = isset( $block['innerContent'][0] ) ? $block['innerContent'][0] : '';

		if ( $block['innerBlocks'] ) {
			$blocks = $block['innerBlocks'];

			foreach ( $blocks as $item ) {
				$blockContent = $this->get_block_content( $item );
				$content .= $blockContent;
			}
		}

		foreach ( $block['innerContent'] as $key => $item ) {
			if ( $key === 0 ) {
				continue;
			}

			$content .= $item;
		}

		return $content;
	}
}
