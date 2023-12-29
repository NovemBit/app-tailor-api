<?php

namespace AT_API\V1\Content\Parsers\Traits;

use WP_Query;
use AT_API\V1\Content\Components\Elements\Link;

trait Sets_Link_Additional_Props
{
	protected function set_link_additional_props( Link $component ): Link {
		if ( ! $component->props[ 'href' ] || ! function_exists( 'url_to_query' ) ) {
			return $component;
		}

		$component->props[ 'href' ] = apply_filters( 'app_tailor_pre_set_link_props', $component->props[ 'href' ] );

		if ( parse_url( $component->props[ 'href' ], PHP_URL_HOST ) !== $_SERVER['SERVER_NAME'] ) {
			return $component;
		}

		$args = url_to_query( $component->props[ 'href' ] );

		if ( is_a( $args, 'WP_Error' ) ) {
			return $component;
		}

		$args['fields'] = 'ids';
		$query = new WP_Query( $args );

		if (
			$query->is_embed()
			|| $query->is_404()
			|| $query->is_search()
			|| $query->is_front_page()
			|| $query->is_home()
			|| $query->is_privacy_policy()
			|| $query->is_attachment()
			|| $query->is_author()
			|| $query->is_date()
		) {
			return $component;
		}

		if ( $query->is_tax() ) {
			return $this->set_taxonomy_resource_props( $component, $query );
		} elseif ( $query->is_single() || $query->is_page() ) {
			return $this->set_single_resource_props( $component, $query );
		} elseif ( $query->is_category() ) {
			return $this->set_category_resource_props( $component, $query );
		} elseif ( $query->is_tag() ) {
			return $this->set_tag_resource_props( $component, $query );
		} elseif ( $query->is_archive() ) {
			return $this->set_collection_resource_props( $component, $query );
		}

		return $component;
	}

	private function set_taxonomy_resource_props( Link $component, WP_Query $query ): Link {
		$taxonomy = $query->query_vars['taxonomy'] ?: '';
		$data = get_taxonomy( $taxonomy );

		if ( ! $taxonomy || ! $query->get_queried_object_id() ) {
			return $component;
		}

		$component->add_prop( Link::PROP_RESOURCE_TYPE, 'taxonomy' );
		$component->add_prop( Link::PROP_TAXONOMY, $taxonomy );
		$component->add_prop( Link::PROP_RESOURCE_ID, $query->get_queried_object_id() );
		$component->add_prop( Link::PROP_POST_TYPE, $data->object_type[0] );

		return $component;
	}

	private function set_single_resource_props( Link $component, WP_Query $query ): Link {
		if ( ! $query->posts[0] ) {
			return $component;
		}

		$id = $query->posts[0];

		if ( get_post_meta( $id, 'web_only', true ) ) {
			return $component;
		}

		$post_type = $query->query_vars['post_type'];

		if ( ! $post_type ) {
			$post_type = $query->is_page() ? 'page' : 'post';
		}

		$component->add_prop( Link::PROP_RESOURCE_TYPE, 'single' );
		$component->add_prop( Link::PROP_POST_TYPE, $post_type );
		$component->add_prop( Link::PROP_RESOURCE_ID, $query->posts[0] );

		return $component;
	}

	private function set_category_resource_props( Link $component, WP_Query $query ): Link {
		if ( ! $query->get_queried_object_id() ) {
			return $component;
		}

		$component->add_prop( Link::PROP_RESOURCE_TYPE, 'category' );
		$component->add_prop( Link::PROP_RESOURCE_ID, $query->get_queried_object_id() );

		return $component;
	}

	private function set_tag_resource_props( Link $component, WP_Query $query ): Link {
		if ( ! $query->get_queried_object_id() ) {
			return $component;
		}

		$component->add_prop( Link::PROP_RESOURCE_TYPE, 'tag' );
		$component->add_prop( Link::PROP_RESOURCE_ID, $query->get_queried_object_id() );

		return $component;
	}

	private function set_collection_resource_props( Link $component, WP_Query $query ): Link {
		$post_type = $query->query_vars['post_type'] ?: '';

		if ( ! $post_type ) {
			return $component;
		}

		$component->add_prop( Link::PROP_RESOURCE_TYPE, 'collection' );
		$component->add_prop( Link::PROP_POST_TYPE, $post_type );

		return $component;
	}
}
