<?php
/**
 * Prototype UX CDC WP Custom Breadcrumbs
 * À activer temporairement pour tester l'interface avant développement métier
 */

if (! defined('ABSPATH')) {
    exit;
}

class CDC_WP_Custom_Breadcrumbs_Prototype
{
    private static ?CDC_WP_Custom_Breadcrumbs_Prototype $instance = null;

    public static function instance(): CDC_WP_Custom_Breadcrumbs_Prototype
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
            'CDC Breadcrumbs [PROTOTYPE]',
            'CDC Breadcrumbs',
            'manage_options',
            'cdc-breadcrumbs-prototype',
            [$this, 'render_dashboard'],
            'dashicons-networking',
            30
        );

        add_submenu_page(
            'cdc-breadcrumbs-prototype',
            'Tableau de bord',
            'Tableau de bord',
            'manage_options',
            'cdc-breadcrumbs-prototype',
            [$this, 'render_dashboard']
        );

        add_submenu_page(
            'cdc-breadcrumbs-prototype',
            'Règles',
            'Règles',
            'manage_options',
            'cdc-breadcrumbs-rules',
            [$this, 'render_rules']
        );

        add_submenu_page(
            'cdc-breadcrumbs-prototype',
            'Nouvelle règle',
            'Nouvelle règle',
            'manage_options',
            'cdc-breadcrumbs-new-rule',
            [$this, 'render_new_rule']
        );

        add_submenu_page(
            'cdc-breadcrumbs-prototype',
            'Réglages',
            'Réglages',
            'manage_options',
            'cdc-breadcrumbs-settings',
            [$this, 'render_settings']
        );

        add_submenu_page(
            'cdc-breadcrumbs-prototype',
            'Backups',
            'Backups',
            'manage_options',
            'cdc-breadcrumbs-backups',
            [$this, 'render_backups']
        );
    }

    public function enqueue_assets($hook): void
    {
        if (strpos($hook, 'cdc-breadcrumbs') === false) {
            return;
        }

        wp_enqueue_style(
            'cdc-breadcrumbs-prototype',
            plugin_dir_url(__FILE__) . 'assets/prototype.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'cdc-breadcrumbs-prototype',
            plugin_dir_url(__FILE__) . 'assets/prototype.js',
            ['jquery'],
            '1.0.0',
            true
        );
    }

    public function render_dashboard(): void
    {
        require_once __DIR__ . '/views/dashboard.php';
    }

    public function render_rules(): void
    {
        require_once __DIR__ . '/views/rules.php';
    }

    public function render_new_rule(): void
    {
        require_once __DIR__ . '/views/new-rule.php';
    }

    public function render_settings(): void
    {
        require_once __DIR__ . '/views/settings.php';
    }

    public function render_backups(): void
    {
        require_once __DIR__ . '/views/backups.php';
    }
}

add_action('plugins_loaded', function() {
    CDC_WP_Custom_Breadcrumbs_Prototype::instance();
});
