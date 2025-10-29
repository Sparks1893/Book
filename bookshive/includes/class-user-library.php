<?php
/**
 * Handles the frontend library display and filters.
 *
 * @package Bookshive
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Bookshive_Library_Display {

    /**
     * Enqueue scripts and styles.
     */
    public static function enqueue_assets() {
        wp_enqueue_style(
            'bookshive-library-style',
            plugin_dir_url( __FILE__ ) . '../assets/css/library-display.css',
            [],
            BOOKSHIVE_VERSION
        );

        wp_enqueue_script(
            'bookshive-library-script',
            plugin_dir_url( __FILE__ ) . '../assets/js/library-display.js',
            [ 'jquery' ],
            BOOKSHIVE_VERSION,
            true
        );

        wp_localize_script( 'bookshive-library-script', 'bookshive_ajax', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'bookshive_library_nonce' ),
        ]);
    }

    /**
     * Render the library view shortcode.
     */
    public static function render_library( $atts ) {
        $atts = shortcode_atts( [
            'user_id' => get_current_user_id(),
            'layout'  => 'grid',
        ], $atts );

        if ( ! $atts['user_id'] ) {
            return '<p class="bookshive-notice">Please log in to view your library.</p>';
        }

        ob_start();
        ?>
        <div id="bookshive-library-container" data-user="<?php echo esc_attr( $atts['user_id'] ); ?>">
            <div class="bookshive-library-header">
                <h2>📚 My Library</h2>
                <div class="bookshive-filters">
                    <select id="filter-genre">
                        <option value="">All Genres</option>
                    </select>
                    <select id="filter-author">
                        <option value="">All Authors</option>
                    </select>
                    <select id="filter-rating">
                        <option value="">All Ratings</option>
                        <option value="5">★★★★★</option>
                        <option value="4">★★★★☆</option>
                        <option value="3">★★★☆☆</option>
                        <option value="2">★★☆☆☆</option>
                        <option value="1">★☆☆☆☆</option>
                    </select>
                    <select id="filter-status">
                        <option value="">All Status</option>
                        <option value="unread">Unread</option>
                        <option value="reading">Reading</option>
                        <option value="paused">Paused</option>
                        <option value="dnf">Did Not Finish</option>
                        <option value="finished">Finished</option>
                    </select>

                    <select id="sort-books">
                        <option value="title_asc">A → Z</option>
                        <option value="title_desc">Z → A</option>
                        <option value="rating_desc">Highest Rated</option>
                        <option value="rating_asc">Lowest Rated</option>
                        <option value="newest">Newest Added</option>
                        <option value="oldest">Oldest Added</option>
                    </select>

                    <div class="bookshive-view-toggle">
                        <button class="view-btn active" data-view="grid" title="Grid View">🔳</button>
                        <button class="view-btn" data-view="list" title="List View">📜</button>
                        <button class="view-btn" data-view="shelf" title="Shelf View">📚</button>
                        <button class="view-btn" data-view="wall" title="Wall View">🧱</button>
                    </div>
                </div>
            </div>

            <div id="bookshive-library-content" class="view-grid">
                <p class="bookshive-loading">Loading your library...</p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX handler: fetch and filter books.
     */
    public static function ajax_fetch_books() {
        check_ajax_referer( 'bookshive_library_nonce', 'nonce' );

        $user_id  = intval( $_POST['user_id'] );
        $filters  = [
            'genre'  => sanitize_text_field( $_POST['genre'] ?? '' ),
            'author' => sanitize_text_field( $_POST['author'] ?? '' ),
            'rating' => intval( $_POST['rating'] ?? 0 ),
            'status' => sanitize_text_field( $_POST['status'] ?? '' ),
            'sort'   => sanitize_text_field( $_POST['sort'] ?? 'title_asc' ),
        ];

        $args = [
            'post_type'      => 'bookshive_book',
            'post_status'    => 'publish',
            'author'         => $user_id,
            'posts_per_page' => -1,
            'meta_query'     => [],
            'orderby'        => 'title',
            'order'          => 'ASC',
        ];

        if ( $filters['genre'] ) {
            $args['meta_query'][] = [
                'key'   => '_book_genre',
                'value' => $filters['genre'],
            ];
        }

        if ( $filters['author'] ) {
            $args['meta_query'][] = [
                'key'   => '_book_author',
                'value' => $filters['author'],
            ];
        }

        if ( $filters['rating'] ) {
            $args['meta_query'][] = [
                'key'     => '_book_rating',
                'value'   => $filters['rating'],
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ];
        }

        if ( $filters['status'] ) {
            $args['meta_query'][] = [
                'key'   => '_book_status',
                'value' => $filters['status'],
            ];
        }

        // Sorting logic
        switch ( $filters['sort'] ) {
            case 'rating_desc':
                $args['meta_key'] = '_book_rating';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'rating_asc':
                $args['meta_key'] = '_book_rating';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'ASC';
                break;
            case 'title_desc':
                $args['orderby'] = 'title';
                $args['order']   = 'DESC';
                break;
            case 'newest':
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
                break;
            case 'oldest':
                $args['orderby'] = 'date';
                $args['order']   = 'ASC';
                break;
        }

        $query = new WP_Query( $args );
        $books = [];

        foreach ( $query->posts as $book ) {
            $books[] = [
                'id'      => $book->ID,
                'title'   => get_the_title( $book ),
                'author'  => get_post_meta( $book->ID, '_book_author', true ),
                'genre'   => get_post_meta( $book->ID, '_book_genre', true ),
                'rating'  => get_post_meta( $book->ID, '_book_rating', true ),
                'spice'   => get_post_meta( $book->ID, '_book_spice', true ),
                'status'  => get_post_meta( $book->ID, '_book_status', true ),
                'thumb'   => get_the_post_thumbnail_url( $book->ID, 'medium' ),
            ];
        }

        wp_send_json_success( [ 'books' => $books ] );
    }

    /**
     * Register AJAX actions and shortcode.
     */
    public static function init() {
        add_shortcode( 'bookshive_library', [ __CLASS__, 'render_library' ] );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
        add_action( 'wp_ajax_bookshive_fetch_books', [ __CLASS__, 'ajax_fetch_books' ] );
        add_action( 'wp_ajax_nopriv_bookshive_fetch_books', [ __CLASS__, 'ajax_fetch_books' ] );
    }
}

Bookshive_Library_Display::init();
