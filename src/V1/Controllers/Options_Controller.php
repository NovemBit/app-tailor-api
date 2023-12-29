<?php

namespace AT_API\V1\Controllers;

use AT_API\V1\Content\Content_Transformer;

class Options_Controller
{
	private string $namespace;
	private string $resource_name;
	private array $settings = array();
	private const MAX_RESOURCES = 20;

	public function __construct() {
		$this->namespace = 'app-tailor/v1';
		$this->resource_name = 'options';
	}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			$this->resource_name,
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_options' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function get_options(): array {
		$this->settings = function_exists( 'get_fields' ) ? get_fields( 'app-settings' ) : array();

		return $this->sanitize_app_settings();
	}

	private function sanitize_app_settings(): array {
		$this->sanitize_diagnoses();
		$this->sanitize_locations();
		$this->sanitize_resources();
		$this->sanitize_donate_content();
		$this->sanitize_support_content();
		$this->unset_fields();

		return $this->settings;
	}

	private function sanitize_diagnoses(): void {
		if ( ! empty( $this->settings['general']['diagnosis'] ) ) {
			$data = array();

			foreach ( $this->settings['general']['diagnosis'] as $diagnosis ) {
				$data[] = array(
					'id' => $diagnosis->term_id,
					'title' => $diagnosis->name,
					'slug' => $diagnosis->slug,
				);
			}

			$this->settings['general']['diagnosis'] = $data;
		}
	}

	private function sanitize_locations(): void {
		if ( ! empty( $this->settings['general']['locations'] ) ) {
			$data = array();

			foreach ( $this->settings['general']['locations'] as $location ) {
				$data[] = array(
					'id' => $location->ID,
					'title' => str_replace( ' Bladder Cancer Resources', '', $location->post_title ),
					'slug' => $location->post_name,
				);
			}

			$this->settings['general']['locations'] = $data;
		}
	}

	private function sanitize_resources(): void {
		$result = array();

		if ( ! empty( $this->settings['resources'] ) ) {
			$resources = $this->settings['resources'];
			$transformer = new Content_Transformer();

			for ( $i = 1; $i < self::MAX_RESOURCES; $i++ ) {
				if ( ! isset( $resources[ 'title_' . $i ], $resources[ 'link_' . $i ] ) ) {
					break;
				}

				$url = $resources[ 'link_' . $i ];
				$component = $transformer->transform( "<a href='${url}'></a>" )[0] ?? null;
				$props = null;

				if ( $component && $component->content ) {
					$link = $component->content[0] ?? null;

					if ( $link && $link->props ) {
						$props = $component->content[0]->props;
					}
				}

				$result[] = array(
					'title' => $resources[ 'title_' . $i ],
					'link' => $props,
				);
			}
		}

		$this->settings['resources'] = $result;
	}

	private function sanitize_donate_content() {
		if ( ! empty( $this->settings['donate']['content'] ) ) {
			$id = $this->settings['donate']['content'];
			$post = get_post( $id );
			$transformer = new Content_Transformer();
			$content = $transformer->transform( $post->post_content );
			$this->settings['donate']['content'] = $content;
		}
	}

	private function sanitize_support_content() {
		if ( ! empty( $this->settings['support']['survivorToSurvivor']['buttonLink'] ) ) {
			$url = $this->settings['support']['survivorToSurvivor']['buttonLink'];
			$transformer = new Content_Transformer();
			$component = $transformer->transform( "<a href='${url}'></a>" )[0];
			$props = $component->content[0]->props;
			$this->settings['support']['survivorToSurvivor']['buttonLink'] = $props;
		}
	}

	private function unset_fields() {
		unset( $this->settings['trials'] );
		unset( $this->settings['stateResources'] );
		unset( $this->settings['matrix'] );
		unset( $this->settings['faq'] );
		unset( $this->settings['general']['feed'] );
	}
}
