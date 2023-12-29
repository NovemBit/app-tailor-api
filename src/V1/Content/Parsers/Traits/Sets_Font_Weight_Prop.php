<?php

namespace AT_API\V1\Content\Parsers\Traits;

use AT_API\V1\Content\Components\Base_Component;

trait Sets_Font_Weight_Prop {

	public function set_font_weight_prop( ?array $attrs, Base_Component $component ) {
		$prop = 'fontWeight';
		$attr = $attrs[ 'style' ] ?? null;

		if ( $attr && isset( $attr[ 'typography' ][ 'fontWeight' ] ) ) {
			$component->set_prop( $prop, ( int ) $attr[ 'typography' ][ 'fontWeight' ] );
		}
	}
}
