<?php

namespace AT_API\V1\Content\Parsers;
use DOMDocument;

abstract class Abstract_Parser {
	private static ?DOMDocument $dom = null;
	abstract public function parse( array $block );

	protected static function get_dom_instance(): DOMDocument {
		if ( ! self::$dom ) {
			self::$dom = new DOMDocument();
		}

		return self::$dom;
	}
}
