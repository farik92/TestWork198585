<?php

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the custom post type "Cities".
 * @see register_post_type
 */
function register_cpt_cities(): void {
	$labels = [
		'name'               => __( 'Cities', 'storefront' ),
		'singular_name'      => __( 'City', 'storefront' ),
		'add_new'            => __( 'Add New City', 'storefront' ),
		'add_new_item'       => __( 'Add New City', 'storefront' ),
		'edit_item'          => __( 'Edit City', 'storefront' ),
		'new_item'           => __( 'New City', 'storefront' ),
		'view_item'          => __( 'View City', 'storefront' ),
		'search_items'       => __( 'Search Cities', 'storefront' ),
		'not_found'          => __( 'No cities found', 'storefront' ),
		'not_found_in_trash' => __( 'No cities found in trash', 'storefront' ),
	];

	$args = [
		'labels'        => $labels,
		'public'        => true,
		'has_archive'   => true,
		'menu_position' => 5,
		'menu_icon'     => 'dashicons-location',
		'supports'      => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
		'show_in_rest'  => true,
	];

	register_post_type( 'cities', $args );
}

add_action( 'init', 'register_cpt_cities' );