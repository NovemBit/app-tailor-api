<?php

namespace AT_API\V1\Content\Enum;

class Tag {
	const STRONG = 'strong';
	const B = 'b';
	const EM = 'em';
	const S = 's';
	const SUB = 'sub';
	const SUP = 'sup';
	const A = 'a';
	const IMG = 'img';
	const TEXT = '#text';
	const P = 'p';
	const UL = 'ul';
	const OL = 'ol';
	const LI = 'li';
	const CITE = 'cite';
	const U = 'u';
	const DIV = 'div';
	const SPAN = 'span';
	const H1 = 'h1';
	const H2 = 'h2';
	const H3 = 'h3';
	const H4 = 'h4';
	const H5 = 'h5';
	const H6 = 'h6';

	public static function get_all(): array {
		return array(
			self::STRONG,
			self::B,
			self::EM,
			self::S,
			self::SUB,
			self::SUP,
			self::A,
			self::IMG,
			self::TEXT,
			self::P,
			self::UL,
			self::OL,
			self::LI,
			self::CITE,
			self::U,
			self::DIV,
			self::SPAN,
			self::H1,
			self::H2,
			self::H3,
			self::H4,
			self::H5,
			self::H6,
		);
	}
}
