<?php

namespace AT_API\V1\Content\Parsers\Traits;

use AT_API\V1\Content\Components\Base_Component;

trait Sets_Align_Prop {
	public function set_align_prop( ?array $attrs, Base_Component $component ) {
		$prop = 'align';
		$attr = $attrs[ 'textAlign' ] ?? null;

		if ( $attr ) {
			$component->set_prop( $prop, $attr );
		}
	}
}
