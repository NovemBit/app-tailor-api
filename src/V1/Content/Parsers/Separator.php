<?php

namespace AT_API\V1\Content\Parsers;

use AT_API\V1\Content\Parsers\Traits\Sets_Background_Prop;
use AT_API\V1\Content\Components\Core\Separator as Separator_Component;

class Separator extends Abstract_Parser {
	use Sets_Background_Prop;

	private static ?Separator $instance = null;
	private function __construct() {
	}
	public static function get_instance(): Separator {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function parse( array $block ) {
		$separator = new Separator_Component();
		$attrs = $block['attrs'] ?? null;
		$this->set_background_prop( $attrs, $separator );
		$this->set_style_prop( $attrs, $separator );

		return $separator;
	}

	private function set_style_prop( ?array $attrs, Separator_Component $component ) {
		$prop = 'style';
		$attr = $attrs[ 'className' ] ?? null;

		if ( $attr ) {
			$mapping = array(
				'is-style-default' => 'default',
				'is-style-wide' => 'wide',
				'is-style-dots' => 'dots',
			);

			if ( isset( $mapping[ $attr ] ) ) {
				$component->set_prop( $prop, $mapping[ $attr ] );
			}
		}
	}
}
