<?php

namespace AT_API\V1\Content\Parsers\Traits;

use AT_API\V1\Content\Components\Base_Component;

trait Sets_Background_Prop {
	public function set_background_prop_from_styles( ?array $attrs, Base_Component $component ) {
		$prop = 'background';
		$attr = $attrs[ 'style' ] ?? null;

		if ( $attr && isset( $attr['color']['background'] ) ) {
			list($r, $g, $b, $a) = sscanf($attr['color']['background'], "#%02x%02x%02x%02x");
			$a = $a ? round( $a / 255, 2 ) : 1;
			$component->set_prop( $prop, array(
				'r' => $r,
				'g' => $g,
				'b' => $b,
				'a' => $a,
			) );
		}
	}

	public function set_background_prop( ?array $attrs, Base_Component $component ) {
		$prop = 'background';
		$attr = $attrs[ 'backgroundColor' ] ?? null;
		$colors = array(
			'black' => '#000000',
			'white' => '#ffffff',
			'light-gray' => '#666666',
			'primary' => '#db6720',
			'secondary' => '#b2d13f',
		);

		if ( $attr ) {
			foreach ( $colors as $color_key => $color ) {
				if ( $color_key === $attr ) {
					list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
					$component->set_prop( $prop, array(
						'r' => $r,
						'g' => $g,
						'b' => $b,
						'a' => 1,
					) );
				}
			}
		}
	}
}