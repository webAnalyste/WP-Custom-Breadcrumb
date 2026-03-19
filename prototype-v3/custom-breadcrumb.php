<?php
/**
 * Plugin Name: Custom Breadcrumb
 * Description: Personnalisez vos fils d'Ariane en quelques clics
 * Version: 1.3.0
 * Author: webAnalyste
 * Author URI: https://www.webanalyste.com
 */

if (! defined('ABSPATH')) {
    exit;
}

class Custom_Breadcrumb_Simple
{
    private static ?Custom_Breadcrumb_Simple $instance = null;

    public static function instance(): Custom_Breadcrumb_Simple
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
            'Breadcrumb',
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
            plugin_dir_url(__FILE__) . 'style.css',
            [],
            '1.3.0'
        );

        wp_enqueue_script(
            'custom-breadcrumb',
            plugin_dir_url(__FILE__) . 'script.js',
            ['jquery'],
            '1.3.0',
            true
        );
    }

    public function render_page(): void
    {
        require_once __DIR__ . '/page.php';
    }
}

add_action('plugins_loaded', function() {
    Custom_Breadcrumb_Simple::instance();
});
