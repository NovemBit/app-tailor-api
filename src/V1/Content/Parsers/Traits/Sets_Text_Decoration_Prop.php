<?php

namespace AT_API\V1\Content\Parsers\Traits;

use AT_API\V1\Content\Components\Base_Component;

trait Sets_Text_Decoration_Prop {
	public function set_text_decoration_prop( ?array $attrs, Base_Component $component ) {
		$prop = 'textDecoration';
		$attr = $attrs[ 'style' ] ?? null;

		if ( $attr && isset( $attr[ 'typography' ][ 'textDecoration' ] ) ) {
			$component->set_prop( $prop, $attr[ 'typography' ][ 'textDecoration' ] );
		}
	}
}
