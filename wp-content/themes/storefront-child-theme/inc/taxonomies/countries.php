<?php

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the custom taxonomy "Countries" for cities.
 *
 * @see register_taxonomy
 */
function register_taxonomy_countries(): void {
	$labels = array(
		'name'          => __( 'Countries', 'storefront' ),
		'singular_name' => __( 'Country', 'storefront' ),
		'search_items'  => __( 'Search Countries', 'storefront' ),
		'all_items'     => __( 'All Countries', 'storefront' ),
		'edit_item'     => __( 'Edit Country', 'storefront' ),
		'add_new_item'  => __( 'Add New Country', 'storefront' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
	);

	register_taxonomy( 'countries', 'cities', $args );
}

add_action( 'init', 'register_taxonomy_countries' );