<?php

use AT_API\V1\Controllers\Options_Controller;
use AT_API\V1\Controllers\Analyze_Content_Controller;

function register_routes() {
	$options_controller = new Options_Controller();
	$options_controller->register_routes();
	$analyze_content_controller = new Analyze_Content_Controller();
	$analyze_content_controller->register_routes();
}

add_action( 'rest_api_init', 'register_routes' );
