<?php
if (!defined('ABSPATH')) exit;

$user_id = get_current_user_id();
$books = []; // replace with real fetch logic later
?>

<div class="bookshive-user-library">
    <h2><?php echo esc_html__('My Library', 'bookshive'); ?></h2>

    <div class="filter-bar">
        <select id="filter-genre">
            <option value=""><?php _e('All Genres', 'bookshive'); ?></option>
        </select>
        <select id="filter-status">
            <option value=""><?php _e('All Statuses', 'bookshive'); ?></option>
            <option value="reading"><?php _e('Reading', 'bookshive'); ?></option>
            <option value="paused"><?php _e('Paused', 'bookshive'); ?></option>
            <option value="dnf"><?php _e('Did Not Finish', 'bookshive'); ?></option>
            <option value="completed"><?php _e('Completed', 'bookshive'); ?></option>
        </select>
    </div>

    <div class="library-grid">
        <?php foreach ($books as $book): ?>
            <div class="book-card">
                <img src="<?php echo esc_url(Bookshive_Helpers::get_thumbnail($book->ID)); ?>" alt="">
                <h4><?php echo esc_html($book->post_title); ?></h4>
                <p><?php echo esc_html($book->author_name); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
