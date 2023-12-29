<?php

namespace AT_API\V1\Content\Parsers\Traits;

use AT_API\V1\Content\Components\Base_Component;

trait Sets_Full_Width_Prop {
	public function set_full_width_prop( ?array $attrs, Base_Component $component ) {
		$prop = 'fullWidth';
		$attr = $attrs[ 'align' ] ?? null;

		if ( 'full' === $attr ) {
			$component->set_prop( $prop, true );
		}
	}
}
