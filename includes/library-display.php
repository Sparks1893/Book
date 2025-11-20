<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * PLM Library Shortcode
 * Outputs a grid of plm_book posts with rating + action buttons.
 */
class PLM_Library_Shortcode {

    /**
     * Initialise the shortcode.
     */
    public static function init() {
        add_shortcode('plm_library', [__CLASS__, 'render_library']);
    }

    /**
     * Render the [plm_library] shortcode.
     *
     * @param array $atts
     * @return string
     */
    public static function render_library($atts = []) {
        // Allow light future extension via atts if needed.
        $atts = shortcode_atts([
            'posts_per_page' => -1,
        ], $atts, 'plm_library');

        $query = new WP_Query([
            'post_type'      => 'plm_book',
            'posts_per_page' => (int) $atts['posts_per_page'],
        ]);

        if (!$query->have_posts()) {
            return '<p>No books found in your library.</p>';
        }

        ob_start();

        echo '<div class="plm-library-grid">';

        while ($query->have_posts()) {
            $query->the_post();

            $book_id = get_the_ID();
            $rating  = get_post_meta($book_id, 'plm_rating', true);

            echo '<div class="plm-book-card">';

            if (has_post_thumbnail()) {
                echo '<div class="plm-book-cover">';
                the_post_thumbnail('medium');
                echo '</div>';
            }

            echo '<h4 class="plm-book-title">' . esc_html(get_the_title()) . '</h4>';

            if (!empty($rating)) {
                echo '<p class="plm-book-rating">' . sprintf(
                    /* translators: %s is rating value */
                    esc_html__('Rating: %s ⭐', 'plm'),
                    esc_html($rating)
                ) . '</p>';
            } else {
                echo '<p class="plm-book-rating plm-book-rating-empty">' . esc_html__('No rating yet', 'plm') . '</p>';
            }

            // Action buttons
            echo '<div class="plm-actions">
                    <button class="plm-btn-wishlist" data-book="' . esc_attr($book_id) . '">♡ ' . esc_html__('Wishlist', 'plm') . '</button>
                    <button class="plm-btn-favorite" data-book="' . esc_attr($book_id) . '">❤️ ' . esc_html__('Favorite', 'plm') . '</button>
                    <button class="plm-btn-like" data-book="' . esc_attr($book_id) . '">👍 ' . esc_html__('Like', 'plm') . '</button>
                  </div>';

            echo '</div>'; // .plm-book-card
        }

        echo '</div>'; // .plm-library-grid

        wp_reset_postdata();

        return ob_get_clean();
    }
}

// Boot it up.
PLM_Library_Shortcode::init();
