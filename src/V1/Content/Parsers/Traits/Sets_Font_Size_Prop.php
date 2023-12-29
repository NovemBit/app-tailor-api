<?php

namespace AT_API\V1\Content\Parsers\Traits;

use AT_API\V1\Content\Components\Base_Component;

trait Sets_Font_Size_Prop {
	public function set_font_size_prop( ?array $attrs, Base_Component $component ) {
		$prop = 'fontSize';
		$attr = $attrs[ $prop ] ?? null;

		if ( $attr ) {
			$mapping = array(
				'small' => 13,
				'medium' => 20,
				'large' => 36,
				'x-large' => 42,
			);

			$component->set_prop( $prop, $mapping[ $attr ] );
		}
	}

	public function set_font_size_prop_from_typography( ?array $attrs, Base_Component $component ) {
		$prop = 'fontSize';
		$attr = $attrs[ 'style' ] ?? null;

		if ( $attr && isset( $attr[ 'typography' ][ 'fontSize' ] ) ) {
			$size = $attr[ 'typography' ][ 'fontSize' ];
			preg_match( '/^\d+/', $size, $matches );

			if ( is_numeric( $matches[ 0 ] ) ) {
				$component->set_prop( $prop, ( int ) $matches[ 0 ] );
			}
		}
	}
}
