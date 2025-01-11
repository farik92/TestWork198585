<?php
// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core class used to implement a City Temperature widget.
 *
 * @see WP_Widget
 */
class City_Temperature extends WP_Widget {

	/**
	 * Sets up a new widget instance.
	 */
	public function __construct() {
		parent::__construct(
			'city_temperature',
			__( 'City Temperature', 'storefront' ),
			[ 'description' => __( 'Display city name and temperature.', 'storefront' ) ]
		);
	}

	/**
	 * Outputs the content for the current widget instance.
	 *
	 * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current City Temperature widget instance.
	 */
	public function widget( $args, $instance ): void {
		$city_id   = ! empty( $instance['city'] ) ? $instance['city'] : '';
		$latitude  = get_post_meta( $city_id, '_latitude', true );
		$longitude = get_post_meta( $city_id, '_longitude', true );
		$city_name = get_the_title( $city_id );

		/**
		 * Retrieve temperature using helper function.
		 *
		 * @see get_temperature
		 */
		$temperature = ($latitude !== null && $longitude !== null) ? get_temperature( $latitude, $longitude ) : __( 'No coordinates available', 'storefront' );

		echo $args['before_widget'];
		echo $args['before_title'] . esc_html( $city_name ) . $args['after_title'];
		echo '<p>' . sprintf( __( 'Temperature: %s', 'storefront' ), esc_html( $temperature ) ) . '</p>';
		echo $args['after_widget'];
	}

	/**
	 * Outputs the widget settings form in the admin area.
	 *
	 * @param array $instance Current widget settings.
	 */
	public function form( $instance ): void {
		$selected_city = ! empty( $instance['city'] ) ? $instance['city'] : '';
		$cities        = get_posts( [ 'post_type' => 'cities', 'posts_per_page' => - 1 ] );

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'city' ) . '">' . __( 'Select City', 'storefront' ) . '</label>';
		echo '<select id="' . $this->get_field_id( 'city' ) . '" name="' . $this->get_field_name( 'city' ) . '">';
		foreach ( $cities as $city ) {
			echo '<option value="' . esc_attr( $city->ID ) . '"' . selected( $city->ID, $selected_city, false ) . '>' . esc_html( $city->post_title ) . '</option>';
		}
		echo '</select>';
		echo '</p>';
	}

	/**
	 * Handles updating the settings for the current widget instance.
	 *
	 * @param array $new_instance New widget settings.
	 * @param array $old_instance Previous widget settings.
	 *
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ): array {
		$instance         = [];
		$instance['city'] = ( ! empty( $new_instance['city'] ) ) ? absint( $new_instance['city'] ) : '';

		return $instance;
	}
}

/**
 * Register the City Temperature widget.
 */
add_action( 'widgets_init', function () {
	register_widget( 'City_Temperature' );
} );