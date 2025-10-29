<?php
if (!defined('ABSPATH')) exit;

/**
 * Class: Bookshive_Book_Recommendations
 * Description: Generates personalized book recommendations
 *              based on reading history, ratings, and trends.
 */

class Bookshive_Book_Recommendations {

    public static function init() {
        add_action('wp_ajax_bookshive_get_recommendations', [__CLASS__, 'get_recommendations']);
        add_action('wp_ajax_nopriv_bookshive_get_recommendations', [__CLASS__, 'get_recommendations']);
        add_shortcode('bookshive_recommendations', [__CLASS__, 'render_shortcode']);
    }

    /**
     * AJAX: Fetch smart recommendations for the current user
     */
    public static function get_recommendations() {
        check_ajax_referer('bookshive_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(__('You must be logged in to get recommendations.', 'bookshive'));
        }

        $user_id = get_current_user_id();
        $books = self::generate_recommendations($user_id);

        if (empty($books)) {
            wp_send_json_error(__('No recommendations available yet.', 'bookshive'));
        }

        ob_start();
        foreach ($books as $book) {
            include BOOKSHIVE_PATH . 'templates/partials/library-book-card.php';
        }
        $html = ob_get_clean();

        wp_send_json_success(['html' => $html]);
    }

    /**
     * Generate recommendations based on user's reading data
     */
    public static function generate_recommendations($user_id) {
        global $wpdb;

        $user_table = $wpdb->prefix . 'personal_library_user_books';
        $community_table = $wpdb->prefix . 'personal_library_community_books';

        // Step 1: Find user's favorite genres (top 3)
        $genres = $wpdb->get_col($wpdb->prepare("
            SELECT LOWER(JSON_UNQUOTE(JSON_EXTRACT(cb.categories, '$[0]'))) AS genre
            FROM $user_table ub
            JOIN {$wpdb->prefix}personal_library_books b ON ub.book
