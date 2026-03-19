<?php
/**
 * Plugin Name: Custom Breadcrumb
 * Plugin URI: https://github.com/webAnalyste/WP-Custom-Breadcrumb
 * Description: Personnalisez vos fils d'Ariane selon vos besoins : articles, pages, formations, produits...
 * Version: 0.2.0-prototype
 * Author: webAnalyste
 * Author URI: https://www.webanalyste.com
 */

if (! defined('ABSPATH')) {
    exit;
}

class Custom_Breadcrumb_Prototype
{
    private static ?Custom_Breadcrumb_Prototype $instance = null;

    public static function instance(): Custom_Breadcrumb_Prototype
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function register_menu(): void
    {
        add_menu_page(
            'Custom Breadcrumb',
            'Breadcrumb',
            'manage_options',
            'custom-breadcrumb',
            [$this, 'render_page'],
            'dashicons-arrow-right-alt',
            30
        );
    }

    public function enqueue_assets($hook): void
    {
        if ($hook !== 'toplevel_page_custom-breadcrumb') {
            return;
        }

        wp_enqueue_style(
            'custom-breadcrumb',
            plugin_dir_url(__FILE__) . 'assets/style.css',
            [],
            '0.2.0'
        );

        wp_enqueue_script(
            'custom-breadcrumb',
            plugin_dir_url(__FILE__) . 'assets/script.js',
            ['jquery'],
            '0.2.0',
            true
        );
    }

    public function render_page(): void
    {
        require_once __DIR__ . '/views/main.php';
    }
}

add_action('plugins_loaded', function() {
    Custom_Breadcrumb_Prototype::instance();
});
