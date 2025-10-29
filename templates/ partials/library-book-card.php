<?php
if (!defined('ABSPATH')) exit;

/**
 * Partial Template: Library Book Card
 * Description: Displays a single book item in the user’s library.
 */

// Expected variables: $book (from class-library-display.php)
?>

<div class="bookshive-book-card" data-book-id="<?php echo esc_attr($book->book_id ?? $book->id); ?>">

    <div class="book-cover">
        <?php
        $thumbnail = !empty($book->thumbnail_url)
            ? esc_url($book->thumbnail_url)
            : BOOKSHIVE_URL . 'assets/img/shop-placeholder.png';
        ?>
        <img src="<?php echo $thumbnail; ?>" alt="<?php echo esc_attr($book->title ?? ''); ?>" loading="lazy">
    </div>

    <div class="book-info">
        <h3 class="book-title"><?php echo esc_html($book->title ?? 'Untitled'); ?></h3>
        <p class="book-author">
            <?php echo !empty($book->author) ? esc_html($book->author) : __('Unknown Author', 'bookshive'); ?>
        </p>

        <?php if (!empty($book->genre)): ?>
            <span class="book-genre"><?php echo esc_html(ucwords($book->genre)); ?></span>
        <?php endif; ?>

        <div class="book-meta">
            <?php if (!empty($book->personal_rating)): ?>
                <div class="rating">
                    <?php
                    for ($i = 0; $i < 5; $i++) {
                        echo $i < $book->personal_rating ? '★' : '☆';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($book->spicy_rating)): ?>
                <div class="spice">
                    <?php
                    for ($i = 0; $i < $book->spicy_rating; $i++) {
                        echo '🌶️';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($book->reading_status)): ?>
            <span class="status status-<?php echo esc_attr($book->reading_status); ?>">
                <?php echo esc_html(ucwords(str_replace('_', ' ', $book->reading_status))); ?>
            </span>
        <?php endif; ?>

        <div class="book-actions">
            <button class="book-action mark-reading" data-status="reading"><?php _e('Reading', 'bookshive'); ?></button>
            <button class="book-action mark-paused" data-status="paused"><?php _e('Paused', 'bookshive'); ?></button>
            <button class="book-action mark-dnf" data-status="did_not_finish"><?php _e('Did Not Finish', 'bookshive'); ?></button>
            <button class="book-action mark-complete" data-status="completed"><?php _e('Completed', 'bookshive'); ?></button>
        </div>
    </div>
</div>
