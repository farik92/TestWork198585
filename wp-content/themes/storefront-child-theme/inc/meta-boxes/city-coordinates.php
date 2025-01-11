<?php

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds meta boxes for latitude and longitude fields.
 * @see add_meta_box
 */
function add_city_meta_box(): void {
	add_meta_box(
		'city_meta_box',
		__( 'City Coordinates', 'storefront' ),
		'display_city_meta_box',
		'cities',
		'normal',
		'high'
	);
}

add_action( 'add_meta_boxes', 'add_city_meta_box' );

/**
 * Displays the meta box for latitude and longitude fields.
 *
 * @param WP_Post $post Current post object.
 */
function display_city_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'save_city_meta', 'city_meta_nonce' );
	$latitude  = get_post_meta( $post->ID, '_latitude', true );
	$longitude = get_post_meta( $post->ID, '_longitude', true );

	echo '<table class="form-table">
		<tbody>
			<tr>
				<th><label for="latitude">Latitude:</label></th>
				<td><input type="text" id="latitude" required name="latitude" value="' . esc_attr( $latitude ) . '" /></td>
			</tr>
			<tr>
				<th><label for="longitude">Longitude:</label></th>
				<td><input type="text" id="longitude" required name="longitude" value="' . esc_attr( $longitude ) . '" /></td>
			</tr>
		</tbody>
	</table>';
}

/**
 * Saves latitude and longitude meta fields for cities.
 *
 * @param int $post_id Post ID.
 *
 */
function save_city_meta_box( int $post_id ): void {
	if ( ! isset( $_POST['city_meta_nonce'] ) || ! wp_verify_nonce( $_POST['city_meta_nonce'], 'save_city_meta' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( isset( $_POST['latitude'] ) && preg_match( '/^-?\d{1,2}(\.\d+)?$/', $_POST['latitude'] ) ) {
		update_post_meta( $post_id, '_latitude', sanitize_text_field( $_POST['latitude'] ) );
	} else {
		delete_post_meta( $post_id, '_latitude' );
	}

	if ( isset( $_POST['longitude'] ) && preg_match( '/^-?\d{1,3}(\.\d+)?$/', $_POST['longitude'] ) ) {
		update_post_meta( $post_id, '_longitude', sanitize_text_field( $_POST['longitude'] ) );
	} else {
		delete_post_meta( $post_id, '_longitude' );
	}
}

add_action( 'save_post', 'save_city_meta_box', 10, 2 );