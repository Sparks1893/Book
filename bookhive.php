<?php
/**
 * Plugin Name: Bookhive
 * Plugin URI: https://bookhive.example
 * Description: A modern personal library manager with community features, recommendations, and series tracking.
 * Version: 1.0.1
 * Author: E. Durant
 * License: GPL v2 or later
 * Text Domain: bookhive
 */

if (!defined('ABSPATH')) exit;

define('BOOKHIVE_VERSION', '1.0.1');
define('BOOKHIVE_PATH', plugin_dir_path(__FILE__));
define('BOOKHIVE_URL', plugin_dir_url(__FILE__));

class Bookhive {
  public function __construct() {
    add_action('init', [$this, 'init']);
    add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
  }

  public function init() {
    $this->load_includes();
    add_shortcode('bookhive_dashboard', [$this, 'render_dashboard']);
  }

  private function load_includes() {
    $files = glob(BOOKHIVE_PATH . 'includes/*.php');
    foreach ($files as $file) include_once $file;
  }

  public function enqueue_assets() {
    // Core frontend styles and JS
    wp_enqueue_style('bookhive-core', BOOKHIVE_URL . 'assets/css/bookhive-core.css', [], BOOKHIVE_VERSION);
    wp_enqueue_script('bookhive-core', BOOKHIVE_URL . 'assets/js/bookhive-core.js', ['jquery'], BOOKHIVE_VERSION, true);
    wp_localize_script('bookhive-core', 'bh_ajax', [
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => wp_create_nonce('bookhive_nonce'),
    ]);
  }

  public function render_dashboard() {
    ob_start();
    ?>
    <div class="bookhive-dashboard">

      <h2><?php esc_html_e('My Library', 'bookhive'); ?></h2>

      <!-- Dropdown Filter Controls -->
      <div class="bh-filter-bar">
        <label for="bh-status-filter"><?php esc_html_e('Filter by Status:', 'bookhive'); ?></label>
        <select id="bh-status-filter">
          <option value="all"><?php esc_html_e('All', 'bookhive'); ?></option>
          <option value="to-read"><?php esc_html_e('To Read', 'bookhive'); ?></option>
          <option value="reading"><?php esc_html_e('Reading', 'bookhive'); ?></option>
          <option value="read"><?php esc_html_e('Read', 'bookhive'); ?></option>
          <option value="dnf"><?php esc_html_e('Did Not Finish', 'bookhive'); ?></option>
        </select>
      </div>

      <!-- Example Book Grid -->
      <div id="bh-library-grid" class="bh-grid-view">
        <?php
        // Example static data — later you’ll pull user data here
        $example_books = [
          ['title' => 'The Night Circus', 'author' => 'Erin Morgenstern', 'status' => 'read'],
          ['title' => 'Fourth Wing', 'author' => 'Rebecca Yarros', 'status' => 'reading'],
          ['title' => 'A Court of Thorns and Roses', 'author' => 'Sarah J. Maas', 'status' => 'to-read'],
        ];

        foreach ($example_books as $book): ?>
          <div class="bh-book-card" data-status="<?php echo esc_attr($book['status']); ?>">
            <h3><?php echo esc_html($book['title']); ?></h3>
            <p class="author"><?php echo esc_html($book['author']); ?></p>
            <p class="status">
              <strong><?php esc_html_e('Status:', 'bookhive'); ?></strong>
              <?php echo esc_html(ucfirst(str_replace('-', ' ', $book['status']))); ?>
            </p>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
    <?php
    return ob_get_clean();
  }
}

new Bookhive();

/**
 * Enqueue achievements JS with badge data.
 */
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_script(
    'bookshive-achievements',
    BOOKHIVE_URL . 'assets/js/achievements.js',
    ['jquery'],
    BOOKHIVE_VERSION,
    true
  );

  $user_id = get_current_user_id();
  $new_badge = $user_id ? get_transient('bookshive_new_badge_' . $user_id) : false;

  wp_localize_script('bookshive-achievements', 'bookshiveBadgeData', [
    'has_new' => (bool)$new_badge,
    'badge'   => $new_badge ?: [],
  ]);

  if ($new_badge) {
    delete_transient('bookshive_new_badge_' . $user_id);
  }
});
