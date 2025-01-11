<?php

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetches the current temperature from the OpenWeatherMap API for a city based on latitude and longitude.
 *
 * @param string $latitude Latitude of the city.
 * @param string $longitude Longitude of the city.
 *
 * @return array|string Temperature in celsius, or an error message.
 */
function get_temperature( string $latitude, string $longitude ): array|string {
	// Cache key for the temperature data.
	$cache_key          = "weather_{$latitude}_{$longitude}";
	// Check if the temperature data is cached.
	$cached_temperature = get_transient( $cache_key );

	if ( $cached_temperature !== false ) {
		return $cached_temperature;
	}

	$api_key     = OPENWEATHER_API_KEY;
	$weather_url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$api_key}&units=metric";

	// Send a GET request to the OpenWeatherMap API.
	$response = wp_remote_get( $weather_url );
	if ( is_wp_error( $response ) ) {
		return ['error' => __( 'Error fetching temperature', 'storefront' )];
	}

	// Decode the JSON response from the OpenWeatherMap API.
	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( isset( $data['main']['temp'] ) ) {
		$temperature = $data['main']['temp'] . '&deg;C';

		// If the temperature data is available, format it and cache it for 1 hour.
		set_transient( $cache_key, $temperature, HOUR_IN_SECONDS );

		return $temperature;
	}

	// If the temperature data is not available, return an error message.
	return ['error' => __( 'Temperature not available', 'storefront' )];
}