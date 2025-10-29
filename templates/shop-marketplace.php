<?php
if (!defined('ABSPATH')) exit;

/**
 * Template: Author Dashboard
 * Description: Allows indie authors to manage their books, pricing, and availability.
 */

$current_user = wp_get_current_user();
$is_author = in_array('author', (array) $current_user->roles) || current_user_can('publish_posts');

if (!$is_author) {
    echo '<p>' . __('You must be an author to access this dashboard.', 'bookshive') . '</p>';
    return;
}

$books = get_option('bookshive_indie_books', []);
$user_books = array_filter($books, fn($b) => $b['author_id'] == $current_user->ID);
?>

<div class="bookshive-author-dashboard">
  <h2><?php echo sprintf(__('Welcome, %s!', 'bookshive'), esc_html($current_user->display_name)); ?></h2>
  <p><?php _e('Manage your indie book listings below:', 'bookshive'); ?></p>

  <form id="add-indie-book-form" class="add-book-form">
    <input type="text" name="title" placeholder="<?php esc_attr_e('Book Title', 'bookshive'); ?>" required>
    <input type="text" name="genre" placeholder="<?php esc_attr_e('Genre', 'bookshive'); ?>" required>
    <input type="number" name="price" step="0.01" placeholder="<?php esc_attr_e('Price (£)', 'bookshive'); ?>" required>
    <input type="url" name="purchase_link" placeholder="<?php esc_attr_e('Purchase URL', 'bookshive'); ?>">
    <textarea name="description" placeholder="<?php esc_attr_e('Short Description', 'bookshive'); ?>"></textarea>
    <button class="button-primary"><?php _e('Add Book', 'bookshive'); ?></button>
  </form>

  <div class="author-book-list">
    <h3><?php _e('Your Books', 'bookshive'); ?></h3>
    <?php if (!empty($user_books)): ?>
      <table class="wp-list-table widefat fixed striped">
        <thead>
          <tr>
            <th><?php _e('Title', 'bookshive'); ?></th>
            <th><?php _e('Genre', 'bookshive'); ?></th>
            <th><?php _e('Price', 'bookshive'); ?></th>
            <th><?php _e('Actions', 'bookshive'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($user_books as $book): ?>
            <tr>
              <td><?php echo esc_html($book['title']); ?></td>
              <td><?php echo esc_html($book['genre']); ?></td>
              <td>£<?php echo esc_html($book['price']); ?></td>
              <td>
                <button class="button-secondary edit-book" data-id="<?php echo esc_attr($book['id']); ?>"><?php _e('Edit', 'bookshive'); ?></button>
                <button class="button delete-book" data-id="<?php echo esc_attr($book['id']); ?>"><?php _e('Delete', 'bookshive'); ?></button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p><?php _e('You haven’t listed any books yet.', 'bookshive'); ?></p>
    <?php endif; ?>
  </div>
</div>
