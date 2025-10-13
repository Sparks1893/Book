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
