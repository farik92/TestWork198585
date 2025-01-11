<?php
/**
 * Template Name: Cities Table
 */

get_header();
?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <div id="city-search" class="city-search">
                <label for="city-search-input"><?php echo __( 'Live cities search', 'storefront' ) ?></label>
                <input type="text" id="city-search-input"
                       placeholder="<?php echo __( 'Search cities...', 'storefront' ) ?>">
            </div>

            <div id="city-results" class="city-results"></div>
        </main>
    </div>

<?php
do_action( 'storefront_sidebar' );
get_footer();
