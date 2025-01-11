<?php
/**
 * Enqueue city search script
 *
 * @return void
 */
function city_search_script(): void {
	wp_enqueue_script( 'city-search', get_stylesheet_directory_uri() . '/js/city-search.js', [ 'jquery' ], null, true );

	wp_localize_script( 'city-search', 'citySearchData', [
		'ajaxurl'  => admin_url( 'admin-ajax.php' ),
		'security' => wp_create_nonce( 'search_cities_nonce' ),
	] );
}

add_action( 'wp_enqueue_scripts', 'city_search_script' );

/**
 * Enqueue gutenberg city search script
 *
 * @return void
 */
function enqueue_custom_gutenberg_script(): void {
	wp_enqueue_script(
		'custom-gutenberg',
		get_stylesheet_directory_uri() . '/js/custom-gutenberg.js',
		['wp-plugins', 'wp-edit-post', 'wp-components', 'wp-data', 'wp-element'],
		'1.0.0',
		true
	);
}
add_action('enqueue_block_editor_assets', 'enqueue_custom_gutenberg_script');

/**
 * Enqueue gutenberg meta fields validation script
 *
 * @return void
 */
function enqueue_gutenberg_validation_script(): void {
	wp_enqueue_script(
		'validate-meta-js',
		get_stylesheet_directory_uri() . '/js/validate-meta.js',
		array( 'wp-data', 'wp-edit-post' ),
		null,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'enqueue_gutenberg_validation_script' );