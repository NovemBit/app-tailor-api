<?php

namespace AT_API\V1\Controllers;

use WP_REST_Request;
use AT_API\V1\Content\Content_Transformer;

class Analyze_Content_Controller {
	private string $namespace;
	private string $resource_name;
	public function __construct() {
		$this->namespace = 'app-tailor/v1';
		$this->resource_name = 'analyze-content';
	}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			$this->resource_name . '/(?P<id>[\d]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function get_item( WP_REST_Request $request ): array {
		$id = $request->get_param( 'id' ) ?? null;
		$post = get_post( $id );

		if ( ! $post ) {
			return array();
		}

		$is_web_only = get_post_meta( $id, 'web_only', true );

		if ( $is_web_only ) {
			return array();
		}

		$transformer = new Content_Transformer();
		return $transformer->analyze( $post->post_content );
	}
}
