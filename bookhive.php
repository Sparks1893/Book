<?php
/**
 * Plugin Name: Bookhive
 * Plugin URI: https://bookhive.example
 * Description: A modern personal library manager with community features, recommendations, and series tracking.
 * Version: 1.0.0
 * Author: E. Durant
 * License: GPL v2 or later
 * Text Domain: bookhive
 */

if (!defined('ABSPATH')) exit;

define('BOOKHIVE_VERSION', '1.0.0');
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
    wp_enqueue_style('bookhive-core', BOOKHIVE_URL . 'assets/css/bookhive-core.css', [], BOOKHIVE_VERSION);
    wp_enqueue_script('bookhive-core', BOOKHIVE_URL . 'assets/js/bookhive-core.js', ['jquery'], BOOKHIVE_VERSION, true);
    wp_localize_script('bookhive-core', 'bh_ajax', [
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => wp_create_nonce('bookhive_nonce'),
    ]);
  }

  public function render_dashboard() {
    ob_start();
    include BOOKHIVE_PATH . 'templates/dashboard-template.php';
    return ob_get_clean();
  }
}

new Bookhive();
