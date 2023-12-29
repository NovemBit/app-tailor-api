<?php

namespace AT_API\V1\Content\Parsers\Traits;

use AT_API\V1\Content\Components\Base_Component;

trait Sets_Italic_Prop {
	public function set_italic_prop( ?array $attrs, Base_Component $component ) {
		$prop = 'italic';
		$attr = $attrs[ 'style' ] ?? null;

		if ( $attr && isset( $attr[ 'typography' ][ 'fontStyle' ] ) ) {
			$component->set_prop( $prop, true );
		}
	}
}
