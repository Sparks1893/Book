<?php
/**
 * Handles user library management (CRUD).
 *
 * @package Bookshive
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Bookshive_User_Library {

    /**
     * Register custom post type for user books.
     */
    public static function register_post_type() {
        $labels = [
            'name'               => __( 'My Library', 'bookshive' ),
            'singular_name'      => __( 'Book', 'bookshive' ),
            'add_new'            => __( 'Add Book', 'bookshive' ),
            'add_new_item'       => __( 'Add New Book', 'bookshive' ),
            'edit_item'          => __( 'Edit Book', 'bookshive' ),
            'new_item'           => __( 'New Book', 'bookshive' ),
            'view_item'          => __( 'View Book', 'bookshive' ),
            'search_items'       => __( 'Search Books', 'bookshive' ),
            'not_found'          => __( 'No books found', 'bookshive' ),
            'not_found_in_trash' => __( 'No books found in Trash', 'bookshive' ),
            'menu_name'          => __( 'Bookshive Library', 'bookshive' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'capability_type'    => 'post',
            'supports'           => [ 'title', 'thumbnail', 'editor', 'custom-fields' ],
            'menu_icon'          => 'dashicons-book',
        ];

        register_post_type( 'bookshive_book', $args );
    }

    /**
     * Add book to user's library.
     */
    public static function add_book( $user_id, $book_data ) {
        $book_id = wp_insert_post( [
            'post_type'   => 'bookshive_book',
            'post_title'  => sanitize_text_field( $book_data['title'] ?? 'Untitled' ),
            'post_status' => 'publish',
            'post_author' => $user_id,
        ] );

        if ( is_wp_error( $book_id ) ) return false;

        // Save metadata
        update_post_meta( $book_id, '_book_author', sanitize_text_field( $book_data['author'] ?? '' ) );
        update_post_meta( $book_id, '_book_genre', sanitize_text_field( $book_data['genre'] ?? '' ) );
        update_post_meta( $book_id, '_book_rating', intval( $book_data['rating'] ?? 0 ) );
        update_post_meta( $book_id, '_book_spice', intval( $book_data['spice'] ?? 0 ) );
        update_post_meta( $book_id, '_book_status', sanitize_text_field( $book_data['status'] ?? 'unread' ) );

        return $book_id;
    }

    /**
     * Update existing book details.
     */
    public static function update_book( $book_id, $book_data ) {
        if ( ! get_post( $book_id ) ) return false;

        wp_update_post( [
            'ID'         => $book_id,
            'post_title' => sanitize_text_field( $book_data['title'] ?? 'Untitled' ),
        ] );

        foreach ( [ 'author', 'genre', 'rating', 'spice', 'status' ] as $field ) {
            if ( isset( $book_data[ $field ] ) ) {
                update_post_meta( $book_id, "_book_{$field}", sanitize_text_field( $book_data[ $field ] ) );
            }
        }

        return true;
    }

    /**
     * Delete a book from user's library.
     */
    public static function delete_book( $book_id ) {
        if ( get_post_type( $book_id ) !== 'bookshive_book' ) return false;
        wp_delete_post( $book_id, true );
        return true;
    }

    /**
     * Fetch books for a user.
     */
    public static function get_user_books( $user_id ) {
        $query = new WP_Query( [
            'post_type'      => 'bookshive_book',
            'post_status'    => 'publish',
            'author'         => $user_id,
            'posts_per_page' => -1,
        ] );

        return $query->posts;
    }

    /**
     * Register the custom post type when WordPress initializes.
     */
    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_post_type' ] );
    }
}

// Initialize
Bookshive_User_Library::init();
