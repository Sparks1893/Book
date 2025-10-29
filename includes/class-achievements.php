<?php
if (!defined('ABSPATH')) exit;

/**
 * Class: Bookshive_Achievements
 * Description: Handles user achievements, badges, and reading milestones.
 */

class Bookshive_Achievements {

    public static function init() {
        add_action('init', [__CLASS__, 'register_badges']);
        add_action('bookshive_on_book_completed', [__CLASS__, 'check_completion_achievements'], 10, 2);
        add_action('bookshive_on_genre_explored', [__CLASS__, 'check_genre_badges'], 10, 2);
        add_action('bookshive_on_streak_update', [__CLASS__, 'check_streak_badges'], 10, 2);

        add_shortcode('bookshive_achievements', [__CLASS__, 'display_user_achievements']);
    }

    /**
     * Define system badges
     */
    public static function register_badges() {
        self::$badges = [
            'first_read' => [
                'title' => __('First Book Finished', 'bookshive'),
                'desc'  => __('Completed your first book!'),
                'icon'  => '🏁',
                'criteria' => function ($user_id) {
                    return self::count_completed_books($user_id) >= 1;
                }
            ],
            'genre_explorer' => [
                'title' => __('Genre Explorer', 'bookshive'),
                'desc'  => __('Read 5 different genres.'),
                'icon'  => '🌈',
                'criteria' => function ($user_id) {
                    return self::count_unique_genres($user_id) >= 5;
                }
            ],
            'series_slayer' => [
                'title' => __('Series Slayer', 'bookshive'),
                'desc'  => __('Completed an entire book series.'),
                'icon'  => '⚔️',
                'criteria' => function ($user_id) {
                    return self::has_completed_series($user_id);
                }
            ],
            'speed_reader' => [
                'title' => __('Speed Reader', 'bookshive'),
                'desc'  => __('Finished a book in under 3 days!'),
                'icon'  => '⚡',
                'criteria' => function ($user_id) {
                    return self::has_fast_read($user_id);
                }
            ],
            'streak_keeper' => [
                'title' => __('Streak Keeper', 'bookshive'),
                'desc'  => __('Read daily for 7 days straight.'),
                'icon'  => '🔥',
                'criteria' => function ($user_id) {
                    return self::get_reading_streak($user_id) >= 7;
                }
            ]
        ];
    }

    /**
     * Check & award achievements for book completion
     */
    public static function check_completion_achievements($user_id, $book_id) {
        foreach (self::$badges as $key => $badge) {
            if (!self::user_has_badge($user_id, $key) && call_user_func($badge['criteria'], $user_id)) {
                self::award_badge($user_id, $key);
            }
        }
    }

    /**
     * Check genre exploration
     */
    public static function check_genre_badges($user_id, $genre) {
        if (!self::user_has_badge($user_id, 'genre_explorer') && self::count_unique_genres($user_id) >= 5) {
            self::award_badge($user_id, 'genre_explorer');
        }
    }

    /**
     * Check reading streak badges
     */
    public static function check_streak_badges($user_id, $streak) {
        if ($streak >= 7 && !self::user_has_badge($user_id, 'streak_keeper')) {
            self::award_badge($user_id, 'streak_keeper');
        }
    }

    /**
     * Award badge
     */
    public static function award_badge($user_id, $badge_key) {
        $badges = get_user_meta($user_id, '_bookshive_achievements', true) ?: [];
        if (!in_array($badge_key, $badges, true)) {
            $badges[] = $badge_key;
            update_user_meta($user_id, '_bookshive_achievements', $badges);

            // Optional notification
            do_action('bookshive_badge_awarded', $user_id, $badge_key);

            // Log or send alert
            error_log("User {$user_id} earned badge: {$badge_key}");
        }
    }

    /**
     * Display user achievements (shortcode)
     */
    public static function display_user_achievements($atts) {
        $atts = shortcode_atts(['user_id' => get_current_user_id()], $atts);
        $user_id = intval($atts['user_id']);
        $earned = get_user_meta($user_id, '_bookshive_achievements', true) ?: [];

        self::register_badges(); // Ensure badges loaded
        $badges = self::$badges;

        ob_start();
        echo '<div class="bookshive-achievements">';
        echo '<h3>' . __('Achievements', 'bookshive') . '</h3>';

        foreach ($badges as $key => $badge) {
            $earnedClass = in_array($key, $earned, true) ? 'earned' : 'locked';
            echo '<div class="achievement ' . esc_attr($earnedClass) . '">';
            echo '<span class="icon">' . esc_html($badge['icon']) . '</span>';
            echo '<strong>' . esc_html($badge['title']) . '</strong>';
            echo '<p>' . esc_html($badge['desc']) . '</p>';
            echo '</div>';
        }

        echo '</div>';
        return ob_get_clean();
    }

    /** Helper methods **/

    private static function user_has_badge($user_id, $badge_key) {
        $badges = get_user_meta($user_id, '_bookshive_achievements', true) ?: [];
        return in_array($badge_key, $badges, true);
    }

    private static function count_completed_books($user_id) {
        global $wpdb;
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}personal_library_user_books WHERE user_id = %d AND reading_status = 'completed'",
            $user_id
        )));
    }

    private static function count_unique_genres($user_id) {
        global $wpdb;
        $results = $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT b.categories
             FROM {$wpdb->prefix}personal_library_user_books ub
             INNER JOIN {$wpdb->prefix}personal_library_books b ON ub.book_id = b.id
             WHERE ub.user_id = %d",
            $user_id
        ));
        return count($results);
    }

    private static function has_completed_series($user_id) {
        global $wpdb;
        return (bool)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}personal_library_books WHERE series_name IS NOT NULL AND series_total_books = (
                SELECT COUNT(*) FROM {$wpdb->prefix}personal_library_books WHERE user_id = %d AND series_name IS NOT NULL
            )",
            $user_id
        ));
    }

    private static function has_fast_read($user_id) {
        global $wpdb;
        return (bool)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}personal_library_user_books
             WHERE user_id = %d AND TIMESTAMPDIFF(DAY, date_started, date_finished) <= 3",
            $user_id
        ));
    }

    private static function get_reading_streak($user_id) {
        return intval(get_user_meta($user_id, '_bookshive_reading_streak', true));
    }

    private static $badges = [];
}

Bookshive_Achievements::init();
