<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Bookshive Admin Menu
 * Registers admin pages for managing books, settings, and plugin tools.
 */

class Bookshive_Admin_Menu {

    /**
     * Initialise hooks
     */
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'register_menu']);
    }

    /**
     * Register main menu + submenus
     */
    public static function register_menu() {

        // MAIN MENU
        add_menu_page(
            __('Bookshive', 'bookshive'),
            __('Bookshive', 'bookshive'),
            'manage_options',
            'bookshive-dashboard',
            [__CLASS__, 'dashboard_page'],
            'dashicons-book-alt',
            25
        );

        // SUBMENU: Dashboard
        add_submenu_page(
            'bookshive-dashboard',
            __('Dashboard', 'bookshive'),
            __('Dashboard', 'bookshive'),
            'manage_options',
            'bookshive-dashboard',
            [__CLASS__, 'dashboard_page']
        );

        // SUBMENU: Library
        add_submenu_page(
            'bookshive-dashboard',
            __('Library Manager', 'bookshive'),
            __('Library Manager', 'bookshive'),
            'manage_options',
            'bookshive-library',
            [__CLASS__, 'library_page']
        );

        // SUBMENU: Achievements
        add_submenu_page(
            'bookshive-dashboard',
            __('Achievements', 'bookshive'),
            __('Achievements', 'bookshive'),
            'manage_options',
            'bookshive-achievements',
            [__CLASS__, 'achievements_page']
        );

        // SUBMENU: Settings
        add_submenu_page(
            'bookshive-dashboard',
            __('Settings', 'bookshive'),
            __('Settings', 'bookshive'),
            'manage_options',
            'bookshive-settings',
            [__CLASS__, 'settings_page']
        );
    }



    /**
     * Page: Dashboard
     */
    public static function dashboard_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Bookshive Dashboard', 'bookshive'); ?></h1>
            <p>Welcome to Bookshive. Manage your library, achievements, and plugin settings.</p>
        </div>
        <?php
    }

    /**
     * Page: Library Manager
     */
    public static function library_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Library Manager', 'bookshive'); ?></h1>
            <p>Here you can manage books stored in the system (custom post type, metadata, etc.).</p>
        </div>
        <?php
    }

    /**
     * Page: Achievements
     */
    public static function achievements_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Achievements', 'bookshive'); ?></h1>
            <p>Manage badges, milestones, and achievement settings.</p>
        </div>
        <?php
    }

    /**
     * Page: Settings
     */
    public static function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Bookshive Settings', 'bookshive'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('bookshive_settings');
                do_settings_sections('bookshive_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

// Initialise admin menu
Bookshive_Admin_Menu::init();
