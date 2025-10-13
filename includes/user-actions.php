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
