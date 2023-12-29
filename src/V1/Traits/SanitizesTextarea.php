<?php

namespace AT_API\V1\Traits;

trait SanitizesTextarea {
	public function sanitize_text( ?string $text ): string {
		if ( ! $text ) {
			return '';
		}

		return str_replace( "\r", '', trim( $text ) );
	}
}
