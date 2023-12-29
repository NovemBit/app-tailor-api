<?php

namespace AT_API\V1\Content\Parsers\Traits;

use AT_API\V1\Content\Components\Base_Component;

Trait Sets_Width_Prop {
	public function set_width_prop( ?array $attrs, Base_Component $component ) {
		$prop = 'width';
		$attr = $attrs[ $prop ] ?? null;

		if ( $attr ) {
			$component->set_prop( $prop, $attr );
		}
	}
}
