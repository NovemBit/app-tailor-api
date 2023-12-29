<?php

namespace AT_API\V1\Content\Parsers\Traits;

use AT_API\V1\Content\Components\Base_Component;

trait Sets_Text_Transform_Prop {
	public function set_text_transform_prop( ?array $attrs, Base_Component $component ) {
		$prop = 'textTransform';
		$attr = $attrs[ 'style' ] ?? null;

		if ( $attr && isset( $attr[ 'typography' ][ 'textTransform' ] ) ) {
			$component->set_prop( $prop, $attr[ 'typography' ][ 'textTransform' ] );
		}
	}
}
