<?php
/**
 * Plugin Name:       Visualwise-Jules
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Handle the basics with this plugin.
 * Version:           1.0.0
 * Author:            WordPress Contributor
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       visualwise-jules
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'VISUALWISE_JULES_VERSION', '1.0.0' );

// Include admin settings
if ( file_exists( plugin_dir_path( __FILE__ ) . 'admin/admin-settings.php' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'admin/admin-settings.php';
}

// Admin menu
function visualwise_jules_admin_menu() {
    add_menu_page(
        'Visualwise-Jules',
        'Visualwise-Jules',
        'manage_options',
        'visualwise-jules',
        'visualwise_jules_admin_page_callback',
        'dashicons-admin-generic',
        20
    );
}
add_action( 'admin_menu', 'visualwise_jules_admin_menu' );

// Admin page callback function
function visualwise_jules_admin_page_callback() {
    // Include the admin page
    if ( file_exists( plugin_dir_path( __FILE__ ) . 'admin/admin-page.php' ) ) {
        require_once plugin_dir_path( __FILE__ ) . 'admin/admin-page.php';
    } else {
        echo '<div class="wrap"><h1>Error: Admin page file not found.</h1></div>';
    }
}

// Include calculations file
if ( file_exists( plugin_dir_path( __FILE__ ) . 'includes/calculations.php' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/calculations.php';
}

// Include frontend shortcode
if ( file_exists( plugin_dir_path( __FILE__ ) . 'frontend/shortcode.php' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'frontend/shortcode.php';
}

// Enqueue scripts and styles for frontend
function visualwise_jules_frontend_assets() {
    global $post;

    // Check if the shortcode is present in the current post content.
    if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'visualwise_jules_visualizer' ) ) {

        wp_enqueue_style(
            'visualwise-jules-frontend-css',
            plugin_dir_url( __FILE__ ) . 'assets/css/frontend.css',
            array(),
            filemtime( plugin_dir_path( __FILE__ ) . 'assets/css/frontend.css' )
        );

        // Enqueue Chart.js from CDN
        // Note: For production plugins, consider bundling or providing a local fallback for CDN assets.
        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js',
            array(),
            '3.7.0', // Version of Chart.js
            true     // Load in footer
        );

        wp_enqueue_script(
            'visualwise-jules-frontend-js',
            plugin_dir_url( __FILE__ ) . 'assets/js/frontend.js',
            array( 'jquery', 'chart-js' ), // Dependencies
            filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/frontend.js' ),
            true     // Load in footer
        );

        // Get currency settings for localization
        $general_settings = get_option( 'visualwise_jules_general_settings', array() );
        $currency_symbol = isset( $general_settings['currency_symbol'] ) ? $general_settings['currency_symbol'] : '$';
        $currency_position = isset( $general_settings['currency_position'] ) ? $general_settings['currency_position'] : 'prefix';
        $currency_spacing = !empty( $general_settings['currency_spacing'] ) ? ' ' : '';

        // Localize script with AJAX URL, nonce, and other necessary data
        wp_localize_script(
            'visualwise-jules-frontend-js', // Handle of the script to attach data to
            'visualwise_jules_ajax_object', // Object name in JavaScript
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'visualwise_jules_visualizer_nonce' ),
                'currency' => array(
                    'symbol'   => $currency_symbol,
                    'position' => $currency_position,
                    'spacing'  => $currency_spacing,
                ),
                // Add any other settings needed by frontend.js, e.g., default slider values from admin
                // 'google_ads_defaults' => get_option('visualwise_jules_google_ads_settings'),
                // 'seo_defaults' => get_option('visualwise_jules_seo_settings'),
                // 'meta_ads_defaults' => get_option('visualwise_jules_meta_ads_settings'),
            )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'visualwise_jules_frontend_assets' );


// Enqueue admin scripts and styles
function visualwise_jules_admin_assets( $hook_suffix ) {
    // Get the slug for the top-level menu page
    // The hook_suffix for the top-level page is 'toplevel_page_visualwise-jules'
    if ( 'toplevel_page_visualwise-jules' !== $hook_suffix ) {
        return;
    }

    // Admin specific JavaScript for tab handling
    wp_enqueue_script(
        'visualwise-jules-admin-js',
        plugin_dir_url( __FILE__ ) . 'admin/js/admin-page.js',
        array(), // No dependencies for this simple script
        filemtime( plugin_dir_path( __FILE__ ) . 'admin/js/admin-page.js' ),
        true // Load in footer
    );

    // If you had admin-specific CSS, you would enqueue it here:
    // wp_enqueue_style(
    //     'visualwise-jules-admin-css',
    //     plugin_dir_url( __FILE__ ) . 'admin/css/admin-styles.css',
    //     array(),
    //     filemtime( plugin_dir_path( __FILE__ ) . 'admin/css/admin-styles.css' )
    // );
}
add_action( 'admin_enqueue_scripts', 'visualwise_jules_admin_assets' );

?>
