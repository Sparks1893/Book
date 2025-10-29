<?php
/**
 * Template: Shop Product Card
 * Used in both [bookshive_shop] and [bookshive_author_store].
 */

if (!defined('ABSPATH')) exit;

$price = get_post_meta(get_the_ID(), '_bookshive_price', true);
$isbn = get_post_meta(get_the_ID(), '_bookshive_isbn', true);
$buy_link = get_post_meta(get_the_ID(), '_bookshive_buy_link', true);
$author_name = get_the_author_meta('display_name');
$thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: BOOKSHIVE_URL . 'assets/img/shop-placeholder.png';
?>

<div class="bookshive-product-card" data-book-id="<?php the_ID(); ?>">
    <div class="book-cover">
        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
    </div>

    <div class="book-info">
        <h3 class="book-title"><?php the_title(); ?></h3>
        <p class="book-author"><?php echo esc_html__('by ', 'bookshive') . esc_html($author_name); ?></p>

        <?php if ($price): ?>
            <p class="book-price">£<?php echo esc_html(number_format((float)$price, 2)); ?></p>
        <?php else: ?>
            <p class="book-price book-price-free"><?php _e('Free', 'bookshive'); ?></p>
        <?php endif; ?>

        <?php if ($buy_link): ?>
            <a href="<?php echo esc_url($buy_link); ?>" class="book-btn buy-btn" target="_blank" rel="noopener">
                <?php _e('Buy Now', 'bookshive'); ?>
            </a>
        <?php else: ?>
            <a href="<?php the_permalink(); ?>" class="book-btn view-btn">
                <?php _e('View Details', 'bookshive'); ?>
            </a>
        <?php endif; ?>
    </div>
</div>
