<?php
if (!defined('ABSPATH')) exit;

function plm_create_user_actions_table() {
    global $wpdb;
    $table = $wpdb->prefix . "plm_user_actions";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        book_id BIGINT(20) NOT NULL,
        action_type VARCHAR(20) NOT NULL,
        date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'plm_create_user_actions_table');

// Add or remove wishlist
function plm_toggle_wishlist() {
    if (!is_user_logged_in()) wp_die('Login required');
    global $wpdb;
    $table = $wpdb->prefix . 'plm_user_actions';
    $user_id = get_current_user_id();
    $book_id = intval($_POST['book_id']);

    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE user_id=%d AND book_id=%d AND action_type='wishlist'", $user_id, $book_id));

    if ($exists) {
        $wpdb->delete($table, ['id' => $exists]);
        echo 'removed';
    } else {
        $wpdb->insert($table, ['user_id'=>$user_id,'book_id'=>$book_id,'action_type'=>'wishlist']);
        echo 'added';
    }
    wp_die();

    // Add or remove favorite
function plm_toggle_favorite() {
    if (!is_user_logged_in()) wp_die('Login required');
    global $wpdb;
    $table = $wpdb->prefix . 'plm_user_actions';
    $user_id = get_current_user_id();
    $book_id = intval($_POST['book_id']);

    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE user_id=%d AND book_id=%d AND action_type='favorite'", $user_id, $book_id));

    if ($exists) {
        $wpdb->delete($table, ['id' => $exists]);
        echo 'removed';
    } else {
        $wpdb->insert($table, ['user_id'=>$user_id,'book_id'=>$book_id,'action_type'=>'favorite']);
        echo 'added';
    }
    wp_die();
}
add_action('wp_ajax_plm_toggle_favorite', 'plm_toggle_favorite');

}
add_action('wp_ajax_plm_toggle_wishlist', 'plm_toggle_wishlist');

// Add or remove like
function plm_toggle_like() {
    if (!is_user_logged_in()) wp_die('Login required');
    global $wpdb;
    $table = $wpdb->prefix . 'plm_user_actions';
    $user_id = get_current_user_id();
    $book_id = intval($_POST['book_id']);

    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE user_id=%d AND book_id=%d AND action_type='like'", $user_id, $book_id));

    if ($exists) {
        $wpdb->delete($table, ['id' => $exists]);
        echo 'removed';
    } else {
        $wpdb->insert($table, ['user_id'=>$user_id,'book_id'=>$book_id,'action_type'=>'like']);
        echo 'added';
    }
    wp_die();
}
add_action('wp_ajax_plm_toggle_like', 'plm_toggle_like');
/**
 * User Actions System
 * Handles wishlist, favorites, likes
 */

if (!defined('ABSPATH')) exit;

function plm_create_user_actions_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'plm_user_actions';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        book_id BIGINT(20) NOT NULL,
        action_type VARCHAR(20) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_book (user_id, book_id),
        KEY action_type (action_type)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'plm_create_user_actions_table');
