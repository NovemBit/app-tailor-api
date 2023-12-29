<?php

use AT_API\V1\Enum\Post_Environment;
use AT_API\V1\Content\Content_Transformer;

const APP_CONTEXT = 'app';

function get_rest_thumbnail( $object ) {
	$img = get_the_post_thumbnail_url( $object['id'], 'thumbnail' );

	return $img ?: null;
}

function add_app_context( array $schema ): array {
	$fields = array(
		'id',
		'date',
		'date_gmt',
		'modified',
		'modified_gmt',
		'slug',
		'status',
		'type',
		'link',
		'author',
	);
	$rendered_fields = array(
		'content',
		'title',
		'guid',
		'excerpt'
	);

	foreach ( $fields as $field ) {
		if (
			isset( $schema['properties'][ $field ]['context'] )
			&& is_array( $schema['properties'][ $field ]['context'] )
		) {
			$schema['properties'][ $field ]['context'][] = APP_CONTEXT;
		}
	}

	foreach ( $rendered_fields as $field ) {
		if (
			isset( $schema['properties'][ $field ]['context'] )
			&& is_array( $schema['properties'][ $field ]['context'] )
		) {
			$schema['properties'][ $field ]['context'][] = APP_CONTEXT;
		}
	}

	foreach ( $rendered_fields as $field ) {
		if (
			isset( $schema['properties'][ $field ]['properties']['rendered']['context'] )
			&& is_array( $schema['properties'][ $field ]['properties']['rendered']['context'] )
		) {
			$schema['properties'][ $field ]['properties']['rendered']['context'][] = APP_CONTEXT;
		}
	}

	return $schema;
}

function modify_post_content( WP_REST_Response $response, WP_Post $post, WP_REST_Request $request ): WP_REST_Response {
	$context = $request->get_param( 'context' );

	if ( APP_CONTEXT !== $context ) {
		return $response;
	}

	$transformer = new Content_Transformer();
	$mode        = get_post_meta( $post->ID, 'classic-editor-remember', true );
	$result      = $transformer->transform( $post->post_content, $mode );

	$data                        = $response->get_data();
	$data['content']['rendered'] = $result;
	$data['title']['rendered']   = htmlspecialchars_decode( $post->post_title );
	$external_url                = function_exists( 'get_field' ) ? get_field( 'external_url', $post->ID ) : '';
	$data['link']                = $external_url ?: $data['link'];
	$website_only                = get_post_meta( $post->ID, 'web_only', true );
	$data['environment']         = $website_only ? Post_Environment::WEBSITE_ONLY : Post_Environment::WEBSITE_AND_APP;
	$display_header_image        = get_post_meta( $post->ID, 'display_header_image', true );
	$header_image                = get_post_meta( $post->ID, 'header_image', true );
	$header_image_url            = null;

	if ( 'page' === $post->post_type ) {
		if ( $display_header_image ) {
			if ( $header_image ) {
				$header_image_url = wp_get_attachment_image_url( $header_image, 'full' );
			} else {
				$header_image_url = get_the_post_thumbnail_url( $post->ID, 'full' );
			}
		}
	}

	$data['headerImage'] = $header_image_url ?: null;
	$response->set_data( $data );

	return $response;
}

function customize_api_resources( string $post_type, WP_Post_Type $post_type_object ) {
	if ( ! $post_type_object->public || ! $post_type_object->show_ui || ! $post_type_object->show_in_menu ) {
		return;
	}

	add_filter( "rest_${post_type}_item_schema", 'add_app_context' );
	add_filter( "rest_prepare_${post_type}", 'modify_post_content', 10, 3 );
	register_rest_field( $post_type, 'thumbnail', array(
		'get_callback' => 'get_rest_thumbnail',
	) );
}

add_action( 'registered_post_type', 'customize_api_resources', 10, 2 );

function add_pages_to_posts( array $args, WP_REST_Request $request ): array {
	$context = $request->get_param( 'context' );
	$tags = $request->get_param( 'tags' );

	if ( APP_CONTEXT !== $context || empty( $tags ) ) {
		return $args;
	}

	$args['post_type'] = array( 'post', 'page' );

	return $args;
}

add_filter( 'rest_post_query', 'add_pages_to_posts', 10, 2 );

function add_link_to_post_type_resource(
	WP_REST_Response $response,
	WP_Post_Type $post_type,
	WP_REST_Request $request
): WP_REST_Response {
	$data = $response->get_data();

	$link = get_post_type_archive_link( $post_type->name );
	$data['link'] = $link ?: null;
	$response->set_data( $data );

	return $response;
}

add_filter( 'rest_prepare_post_type', 'add_link_to_post_type_resource', 10, 3 );

function show_notices() {
	global $post, $current_screen;
	$screens = array( 'post', 'page' );

	if ( ! in_array( $current_screen->id, $screens, true ) ) {
		return;
	}

	$transformer = new Content_Transformer();
	$errors = $transformer->analyze( $post->post_content );

	foreach ( $errors as $error ) {
		?>
		<div class="notice notice-error is-dismissible">
			<p style="margin: 5px 0 15px; font-size: 16px;">
				<?php echo "<b>${error['tag']}: </b>" . $error['message']; ?>
			</p>
		</div>
		<?php
	}
}

add_action( 'admin_notices', 'show_notices' );
