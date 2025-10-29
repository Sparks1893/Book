<?php
/**
 * Plugin Name: Bookshive
 * Description: A personal library and reading management system with user and admin tools.
 * Version: 1.0
 * Author: E. Durant
 * License: GPLv2 or later
 * Text Domain: bookshive
 */

if (!defined('ABSPATH')) exit; // Prevent direct access

// Define plugin constants
define('BOOKSHIVE_VERSION', '1.0');
define('BOOKSHIVE_PATH', plugin_dir_path(__FILE__));
define('BOOKSHIVE_URL', plugin_dir_url(__FILE__));

// --- Autoload All Core Files ---
function bookshive_load_includes() {
    $includes = [
        'class-library-display.php',
        'class-shop-system.php',
        'class-shop-shortcodes.php',
        'class-user-library.php',
        'class-book-recommendations.php',
        'class-series-checker.php',
        'class-reading-status.php',
        'class-achievements.php',
        'class-release-calendar.php',
        'class-community-feed.php',
        'class-ajax-handlers.php',
        'helpers.php',
    ];
    foreach ($includes as $file) {
        $path = BOOKSHIVE_PATH . 'includes/' . $file;
        if (file_exists($path)) require_once $path;
    }
}
add_action('plugins_loaded', 'bookshive_load_includes');

// --- Enqueue Scripts and Styles ---
function bookshive_enqueue_assets() {
    // CSS
    wp_enqueue_style('bookshive-library', BOOKSHIVE_URL . 'assets/css/library-display.css', [], BOOKSHIVE_VERSION);
    wp_enqueue_style('bookshive-shop', BOOKSHIVE_URL . 'assets/css/shop.css', [], BOOKSHIVE_VERSION);
    wp_enqueue_style('bookshive-dashboard', BOOKSHIVE_URL . 'assets/css/dashboard.css', [], BOOKSHIVE_VERSION);
    wp_enqueue_style('bookshive-community', BOOKSHIVE_URL . 'assets/css/community.css', [], BOOKSHIVE_VERSION);

    // JS
    wp_enqueue_script('bookshive-library', BOOKSHIVE_URL . 'assets/js/library-display.js', ['jquery'], BOOKSHIVE_VERSION, true);
    wp_enqueue_script('bookshive-shop', BOOKSHIVE_URL . 'assets/js/shop.js', ['jquery'], BOOKSHIVE_VERSION, true);
    wp_enqueue_script('bookshive-dashboard', BOOKSHIVE_URL . 'assets/js/dashboard.js', ['jquery'], BOOKSHIVE_VERSION, true);
    wp_enqueue_script('bookshive-recommendations', BOOKSHIVE_URL . 'assets/js/recommendations.js', ['jquery'], BOOKSHIVE_VERSION, true);

    wp_localize_script('bookshive-library', 'bookshiveAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('bookshive_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'bookshive_enqueue_assets');
add_action('admin_enqueue_scripts', 'bookshive_enqueue_assets');

// --- Setup Wizard Activation ---
function bookshive_activate() {
    require_once BOOKSHIVE_PATH . 'includes/class-setup-wizard.php';
    Bookshive_Setup_Wizard::run_on_activation();
}
register_activation_hook(__FILE__, 'bookshive_activate');

// --- Shortcodes Registration ---
function bookshive_register_shortcodes() {
    add_shortcode('bookshive_library', ['Bookshive_Library_Display', 'render']);
    add_shortcode('bookshive_shop', ['Bookshive_Shop_Shortcodes', 'render_marketplace']);
    add_shortcode('bookshive_author_dashboard', ['Bookshive_Shop_Shortcodes', 'render_author_dashboard']);
}
add_action('init', 'bookshive_register_shortcodes');

// --- Initialize AJAX Handlers ---
function bookshive_register_ajax_handlers() {
    if (class_exists('Bookshive_Ajax_Handlers')) {
        Bookshive_Ajax_Handlers::register_all();
    }
}
add_action('init', 'bookshive_register_ajax_handlers');

// --- Admin Menu Registration ---
function bookshive_register_admin_menu() {
    add_menu_page(
        __('Bookshive', 'bookshive'),
        __('Bookshive', 'bookshive'),
        'manage_options',
        'bookshive-dashboard',
        ['Bookshive_Admin_Dashboard', 'render'],
        'dashicons-book',
        26
    );
}
add_action('admin_menu', 'bookshive_register_admin_menu');

// --- Plugin Deactivation Cleanup ---
function bookshive_deactivate() {
    // Reserved for cleanup if needed
}
register_deactivation_hook(__FILE__, 'bookshive_deactivate');
