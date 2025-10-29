<?php
/**
 * Plugin Name: Bookshive
 * Plugin URI:  https://bookshive.example.com
 * Description: A personal library manager and book discovery plugin that helps users organize, track, and explore their reading collections.
 * Version:     1.0
 * Author:      E. Durant
 * Author URI:  https://bookshive.example.com
 * License:     GPL2
 * Text Domain: bookshive
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// -----------------------------------------------------------------------------
// 1️⃣ DEFINE CONSTANTS
// -----------------------------------------------------------------------------

define( 'BOOKSHIVE_VERSION', '1.0' );
define( 'BOOKSHIVE_PATH', plugin_dir_path( __FILE__ ) );
define( 'BOOKSHIVE_URL', plugin_dir_url( __FILE__ ) );

// -----------------------------------------------------------------------------
// 2️⃣ AUTOLOAD CORE FILES
// -----------------------------------------------------------------------------

// Load helper functions
require_once BOOKSHIVE_PATH . 'includes/helpers.php';

// Load feature classes
$includes = [
    'class-user-library.php',
    'class-library-display.php',
    'class-reading-status.php',
    'class-series-checker.php',
    'class-book-recommendations.php',
    'class-shop-system.php',
];

foreach ( $includes as $file ) {
    $path = BOOKSHIVE_PATH . 'includes/' . $file;
    if ( file_exists( $path ) ) require_once $path;
}

// -----------------------------------------------------------------------------
// 3️⃣ ENQUEUE SCRIPTS & STYLES
// -----------------------------------------------------------------------------

add_action( 'wp_enqueue_scripts', 'bookshive_enqueue_assets' );
function bookshive_enqueue_assets() {
    // CSS
    wp_enqueue_style( 'bookshive-library', BOOKSHIVE_URL . 'assets/css/library-display.css', [], BOOKSHIVE_VERSION );
    
    // JS
    wp_enqueue_script( 'bookshive-library', BOOKSHIVE_URL . 'assets/js/library-display.js', ['jquery'], BOOKSHIVE_VERSION, true );

    // Pass AJAX URL to script
    wp_localize_script( 'bookshive-library', 'bookshive_ajax', [
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'bookshive_nonce' ),
    ]);
}

// -----------------------------------------------------------------------------
// 4️⃣ ACTIVATE / DEACTIVATE HOOKS
// -----------------------------------------------------------------------------

register_activation_hook( __FILE__, 'bookshive_activate_plugin' );
function bookshive_activate_plugin() {
    // Create necessary tables or default options
    bookshive_create_default_options();
}

register_deactivation_hook( __FILE__, 'bookshive_deactivate_plugin' );
function bookshive_deactivate_plugin() {
    // Clean up temporary data if needed
}

// -----------------------------------------------------------------------------
// 5️⃣ SHORTCODES
// -----------------------------------------------------------------------------

add_shortcode( 'bookshive_user_library', [ 'Bookshive_Library_Display', 'render_user_library' ] );
add_shortcode( 'bookshive_marketplace', [ 'Bookshive_Shop_System', 'render_marketplace' ] );
add_shortcode( 'bookshive_series_checker', [ 'Bookshive_Series_Checker', 'render_series_checker' ] );

// -----------------------------------------------------------------------------
// 6️⃣ AJAX HANDLERS
// -----------------------------------------------------------------------------

add_action( 'wp_ajax_bookshive_filter_library', [ 'Bookshive_Library_Display', 'ajax_filter_library' ] );
add_action( 'wp_ajax_nopriv_bookshive_filter_library', [ 'Bookshive_Library_Display', 'ajax_filter_library' ] );

add_action( 'wp_ajax_bookshive_save_preferences', [ 'Bookshive_Library_Display', 'ajax_save_preferences' ] );
add_action( 'wp_ajax_nopriv_bookshive_save_preferences', [ 'Bookshive_Library_Display', 'ajax_save_preferences' ] );

// -----------------------------------------------------------------------------
// 7️⃣ INITIALIZATION MESSAGE (DEBUG)
// -----------------------------------------------------------------------------

add_action( 'init', function() {
    // Developers can check if plugin loaded correctly
    do_action( 'bookshive_loaded' );
});
