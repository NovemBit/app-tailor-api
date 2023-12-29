<?php

namespace AT_API\V1\Content\Parsers;

use AT_API\V1\Content\Parsers\Traits\Parses_Element;
use AT_API\V1\Content\Components\Core\Embed as Embed_Component;

class Embed extends Abstract_Parser {
	use Parses_Element;

	private static ?Embed $instance = null;

	protected function __construct() {
	}

	public static function get_instance(): Embed {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function parse( array $block ) {
		$attrs = $block['attrs'] ?? null;

		if ( $attrs['providerNameSlug'] !== 'youtube' ) {
			return null;
		}

		$embed = new Embed_Component();
		$embed->set_prop( 'url', $attrs['url'] ?: '' );

		return $embed;
	}
}
