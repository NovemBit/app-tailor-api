<?php

namespace AT_API\V1\Content\Parsers;

use AT_API\V1\Content\Components\Core\Html;

class Freeform extends Abstract_Parser
{
	private static ?Freeform $instance = null;

	protected function __construct() {
	}

	public static function get_instance(): Freeform {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function parse( array $block ) {
		$content = trim( $block['innerHTML'] );

		if ( empty( $content ) ) {
			return null;
		}

		$html = new Html();
		$html->set_content( $block['innerHTML'] );

		return $html;
	}
}
