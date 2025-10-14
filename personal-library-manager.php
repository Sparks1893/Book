<?php
/*
Plugin Name: Personal Library Manager
Description: A feature-rich personal and social library system for book lovers.
Version: 1.0.0
Author: E. Durant
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/book-post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-menu.php';

function plm_activate() {
    plm_register_book_post_type();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'plm_activate');

function plm_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'plm_deactivate');

require_once plugin_dir_path(__FILE__) . 'includes/book-taxonomies.php';
require_once plugin_dir_path(__FILE__) . 'includes/book-meta.php';
require_once plugin_dir_path(__FILE__) . 'includes/library-display.php';

echo '<button class="plm-btn-wishlist" data-book="'.get_the_ID().'">♡ Wishlist</button>';

function plm_enqueue_scripts() {
    wp_enqueue_script('plm-user-actions', plugin_dir_url(__FILE__).'assets/js/user-actions.js', ['jquery'], '1.0', true);
    wp_localize_script('plm-user-actions', 'plm_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
}
add_action('wp_enqueue_scripts', 'plm_enqueue_scripts');
require_once plugin_dir_path(__FILE__) . 'includes/user-actions.php';

function plm_enqueue_scripts() {
    wp_enqueue_script('plm-user-actions', PERSONAL_LIBRARY_URL . 'assets/js/user-actions.js', ['jquery'], '1.0', true);
    wp_localize_script('plm-user-actions', 'plm_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('personal_library_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'plm_enqueue_scripts');

function plm_enqueue_scripts() {
    // Existing scripts...

    // ✅ Add the toast script
    wp_enqueue_script('plm-toast', PERSONAL_LIBRARY_URL . 'assets/js/toast.js', [], '1.0', true);

    // ✅ User actions script after toast
    wp_enqueue_script('plm-user-actions', PERSONAL_LIBRARY_URL . 'assets/js/user-actions.js', ['plm-toast'], '1.0', true);

    wp_localize_script('plm-user-actions', 'plm_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('personal_library_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'plm_enqueue_scripts');

function plm_enqueue_styles() {
    wp_enqueue_style('plm-toast', PERSONAL_LIBRARY_URL . 'assets/css/toast.css');
}
add_action('wp_enqueue_scripts', 'plm_enqueue_styles');

