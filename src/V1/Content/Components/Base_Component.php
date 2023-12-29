<?php

namespace AT_API\V1\Content\Components;

abstract class Base_Component {
	public string $type;
	public ?array $props = null;
	protected array $errors = array();
	protected array $eligible_props = array();
	protected array $private_props = array();
	protected array $allowed_children = array();

	public function add_prop( string $name, $value ) {
		if ( in_array( $name, $this->eligible_props, true ) ) {
			$this->props[ $name ] = $value;
		}
	}

	public function set_prop( string $key, $value ) {
		$this->props[ $key ] = $value;
	}

	public function add_private_prop( string $name, $value ) {
		$this->private_props[ $name ] = $value;
	}

	public function get_private_props(): array {
		return $this->private_props;
	}

	public function add_error( array $error ) {
		$this->errors[] = $error;
	}

	public function get_errors(): array {
		return $this->errors;
	}

	public function get_allowed_children(): array {
		return $this->allowed_children;
	}
}
