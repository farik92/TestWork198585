<?php

define( 'OPENWEATHER_API_KEY', getenv( 'OPENWEATHER_API_KEY' ) ?: 'a74ca8edb2e984d306f41b4a99435a6a' );


require_once get_stylesheet_directory() . '/inc/post-types/cities.php';
require_once get_stylesheet_directory() . '/inc/meta-boxes/city-coordinates.php';
require_once get_stylesheet_directory() . '/inc/taxonomies/countries.php';
require_once get_stylesheet_directory() . '/inc/widgets/class-city-temperature.php';
require_once get_stylesheet_directory() . '/inc/functions/city-functions.php';
require_once get_stylesheet_directory() . '/inc/ajax/city-search.php';
require_once get_stylesheet_directory() . '/inc/scripts.php';
