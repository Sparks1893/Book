<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * PLM Book Meta
 * Adds and saves meta fields for the plm_book post type.
 */
class PLM_Book_Meta {

    /**
     * Post type slug for PLM books.
     *
     * @var string
     */
    protected static $post_type = 'plm_book';

    /**
     * Init hooks.
     */
    public static function init() {
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
        add_action('save_post', [__CLASS__, 'save_meta']);
    }

    /**
     * Register the meta box.
     */
    public static function add_meta_boxes() {
        add_meta_box(
            'plm_book_details',
            __('Book Details', 'plm'),
            [__CLASS__, 'render_meta_box'],
            self::$post_type,
            'side',
            'default'
        );
    }

    /**
     * Render the meta box HTML.
     *
     * @param WP_Post $post
     */
    public static function render_meta_box($post) {
        // Security nonce
        wp_nonce_field('plm_save_book_meta', 'plm_book_meta_nonce');

        $rating = get_post_meta($post->ID, 'plm_rating', true);
        $status = get_post_meta($post->ID, 'plm_status', true);
        $series = get_post_meta($post->ID, 'plm_series', true);

        ?>
        <p>
            <label for="plm_rating"><strong><?php esc_html_e('Rating (1–5):', 'plm'); ?></strong></label>
            <input type="number"
                   id="plm_rating"
                   name="plm_rating"
                   min="1"
                   max="5"
                   step="1"
                   style="width:100%;"
                   value="<?php echo esc_attr($rating); ?>">
        </p>

        <p>
            <label for="plm_status"><strong><?php esc_html_e('Reading Status:', 'plm'); ?></strong></label>
            <select id="plm_status" name="plm_status" style="width:100%;">
                <option value="Unread"   <?php selected($status, 'Unread');   ?>><?php esc_html_e('Unread', 'plm'); ?></option>
                <option value="Reading"  <?php selected($status, 'Reading');  ?>><?php esc_html_e('Reading', 'plm'); ?></option>
                <option value="Completed"<?php selected($status, 'Completed');?>><?php esc_html_e('Completed', 'plm'); ?></option>
            </select>
        </p>

        <p>
            <label for="plm_series"><strong><?php esc_html_e('Series Name:', 'plm'); ?></strong></label>
            <input type="text"
                   id="plm_series"
                   name="plm_series"
                   style="width:100%;"
                   value="<?php echo esc_attr($series); ?>">
        </p>
        <?php
    }

    /**
     * Save meta values.
     *
     * @param int $post_id
     */
    public static function save_meta($post_id) {

        // Check post type early
        $post_type = get_post_type($post_id);
        if ($post_type !== self::$post_type) {
            return;
        }

        // Autosave?
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check nonce
        if (!isset($_POST['plm_book_meta_nonce']) ||
            !wp_verify_nonce($_POST['plm_book_meta_nonce'], 'plm_save_book_meta')) {
            return;
        }

        // Permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Rating
        if (isset($_POST['plm_rating'])) {
            $rating = (int) $_POST['plm_rating'];
            if ($rating >= 1 && $rating <= 5) {
                update_post_meta($post_id, 'plm_rating', $rating);
            } else {
                delete_post_meta($post_id, 'plm_rating');
            }
        }

        // Status
        if (isset($_POST['plm_status'])) {
            $status = sanitize_text_field(wp_unslash($_POST['plm_status']));
            update_post_meta($post_id, 'plm_status', $status);
        }

        // Series
        if (isset($_POST['plm_series'])) {
            $series = sanitize_text_field(wp_unslash($_POST['plm_series']));
            update_post_meta($post_id, 'plm_series', $series);
        }
    }
}

// Boot it up.
PLM_Book_Meta::init();
