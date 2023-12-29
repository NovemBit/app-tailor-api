<?php

namespace AT_API\V1\Content\Parsers\Traits;

use AT_API\V1\Content\Components\Base_Component;

trait Sets_Letter_Spacing_Prop {
	public function set_letter_spacing_prop( ?array $attrs, Base_Component $component ) {
		$prop = 'letterSpacing';
		$attr = $attrs[ 'style' ] ?? null;

		if ( $attr && isset( $attr[ 'typography' ][ 'letterSpacing' ] ) ) {
			$size = $attr[ 'typography' ][ 'letterSpacing' ];
			preg_match( '/^\d+/', $size, $matches );

			if ( is_numeric( $matches[ 0 ] ) ) {
				$component->set_prop( $prop, ( int ) $matches[ 0 ] );
			}
		}
	}
}
