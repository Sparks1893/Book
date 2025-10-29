<?php
/**
 * Template: Shop Product Page
 * Description: Displays individual indie book details
 * Author: E. Durant
 */

if (!defined('ABSPATH')) exit;

global $post;

$book_id = get_query_var('book_id');
$book = get_post($book_id);

if (!$book) {
    echo '<p>Book not found.</p>';
    return;
}

// Custom metadata from shop system
$price = get_post_meta($book_id, '_bookshive_price', true);
$genre = get_post_meta($book_id, '_bookshive_genre', true);
$rating = get_post_meta($book_id, '_bookshive_rating', true);
$spicy = get_post_meta($book_id, '_bookshive_spicy_rating', true);
$cover = get_the_post_thumbnail_url($book_id, 'large') ?: BOOKSHIVE_URL . 'assets/img/shop-placeholder.png';
$author_name = get_post_meta($book_id, '_bookshive_author_name', true);
?>

<div class="bookshive-product-container">
  <div class="product-grid">
    <!-- LEFT COLUMN: COVER -->
    <div class="product-cover">
      <img src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_attr(get_the_title($book_id)); ?>" />
    </div>

    <!-- RIGHT COLUMN: DETAILS -->
    <div class="product-info">
      <h1 class="book-title"><?php echo esc_html(get_the_title($book_id)); ?></h1>
      <p class="author-name">By <?php echo esc_html($author_name); ?></p>
      
      <div class="rating-row">
        <div class="stars">
          <?php for ($i = 0; $i < 5; $i++): ?>
            <span class="dashicons dashicons-star-<?php echo ($i < intval($rating)) ? 'filled' : 'empty'; ?>"></span>
          <?php endfor; ?>
        </div>
        <?php if ($spicy): ?>
          <span class="spice">🌶️ <?php echo intval($spicy); ?>/5</span>
        <?php endif; ?>
      </div>

      <div class="price">£<?php echo esc_html(number_format($price, 2)); ?></div>

      <div class="actions">
        <button class="button-primary buy-now" data-book-id="<?php echo esc_attr($book_id); ?>">
          <span class="dashicons dashicons-cart"></span> Buy Now
        </button>
        <button class="button-secondary add-wishlist" data-book-id="<?php echo esc_attr($book_id); ?>">
          <span class="dashicons dashicons-heart"></span> Wishlist
        </button>
      </div>

      <div class="description">
        <h3>About the Book</h3>
        <p><?php echo wpautop(esc_html($book->post_content)); ?></p>
      </div>

      <?php if ($genre): ?>
      <div class="meta">
        <strong>Genre:</strong> <?php echo esc_html($genre); ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- RELATED BOOKS -->
  <?php
  $related = new WP_Query(array(
      'post_type' => 'bookshive_indie',
      'posts_per_page' => 4,
      'post__not_in' => array($book_id),
      'meta_key' => '_bookshive_author_name',
      'meta_value' => $author_name,
  ));
  if ($related->have_posts()): ?>
    <div class="related-books">
      <h2>More by <?php echo esc_html($author_name); ?></h2>
      <div class="related-grid">
        <?php while ($related->have_posts()): $related->the_post(); ?>
          <div class="related-card">
            <a href="<?php the_permalink(); ?>">
              <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: BOOKSHIVE_URL . 'assets/img/shop-placeholder.png'); ?>" alt="<?php the_title(); ?>">
              <h4><?php the_title(); ?></h4>
            </a>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  <?php wp_reset_postdata(); endif; ?>
</div>
