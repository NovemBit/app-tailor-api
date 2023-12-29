<?php

function add_app_settings_page() {
    add_menu_page(
        'App Settings',
        'App Settings',
        'manage_options',
        'app-settings',
        'render_app_settings_page',
        'dashicons-admin-generic',
        81
    );
}

add_action( 'admin_menu', 'add_app_settings_page' );

function render_app_settings_page() {
    acf_form(array(
        'post_id' => 'app-settings',
        'field_groups' => array( 'group_64a7e34368bb0' ),
        'submit_value' => 'Save',
    ));
}

function app_settings_admin_head() {
    if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] === 'app-settings' ) {
        acf_form_head();
    }
}

add_action('admin_init', 'app_settings_admin_head');
