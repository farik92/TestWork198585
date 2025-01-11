<?php
// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX handler for searching cities.
 *
 * @return void
 */
function search_cities_ajax(): void {
	check_ajax_referer( 'search_cities_nonce', 'security' );

	// Get request parameters
	$query           = isset( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : '';
	$paged           = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
	$cities_per_page = 10; // Number of cities per page

	global $wpdb;

	$where = "WHERE p.post_type = 'cities' AND p.post_status = 'publish'";
	if ( ! empty( $query ) ) {
		// Add search condition by city name
		$where .= $wpdb->prepare( " AND p.post_title LIKE %s", '%' . $wpdb->esc_like( $query ) . '%' );
	}

	// Calculate offset for LIMIT
	$offset  = ( $paged - 1 ) * $cities_per_page;
	$cities = $wpdb->get_results( $wpdb->prepare( "
        SELECT p.ID, p.post_title, t.name AS country, pm1.meta_value AS latitude, pm2.meta_value AS longitude
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
        LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_latitude'
        LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_longitude'
        $where
        LIMIT %d OFFSET %d
    ", $cities_per_page, $offset ) );

	if ( is_null( $cities ) ) {
		wp_send_json_error( __( 'Database error occurred.', 'storefront' ) );
	}

	$total_cities = $wpdb->get_var( "
        SELECT COUNT(*)
        FROM {$wpdb->posts} p
        $where
    " );

	/**
	 * Converts each element in the $cities array to a new array with specific keys.
	 *
	 * @param stdClass $city An element in the $cities array.
	 * @return array A new array with keys: 'name', 'country', 'latitude', 'longitude', 'temperature'.
	 */
	$results = array_map( function ( $city ) {
		if ($city->latitude || $city->longitude) {
			$temperature = get_temperature($city->latitude, $city->longitude);
		} else {
			$temperature = '';
		}
		return [
			'name'        => $city->post_title,
			'country'     => $city->country,
			'latitude'    => $city->latitude,
			'longitude'   => $city->longitude,
			'temperature' => is_string($temperature) ? $temperature : '',
		];
	}, $cities );

	wp_send_json_success( [
		'cities' => $results,
		'total'  => $total_cities,
		'pages'  => ceil( $total_cities / $cities_per_page ),
	] );
}

add_action( 'wp_ajax_search_cities', 'search_cities_ajax' );
add_action( 'wp_ajax_nopriv_search_cities', 'search_cities_ajax' );