<?php
/**
 * Interface d'administration
 */

if (! defined('ABSPATH')) {
    exit;
}

class Custom_Breadcrumb_Admin
{
    private Custom_Breadcrumb_Config $config;

    public function __construct(Custom_Breadcrumb_Config $config)
    {
        $this->config = $config;

        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_custom_breadcrumb_save', [$this, 'ajax_save']);
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

    public function enqueue_assets(string $hook): void
    {
        if ($hook !== 'toplevel_page_custom-breadcrumb') {
            return;
        }

        wp_enqueue_style(
            'custom-breadcrumb-admin',
            CUSTOM_BREADCRUMB_URL . 'admin/assets/style-advanced.css',
            [],
            CUSTOM_BREADCRUMB_VERSION
        );

        wp_enqueue_script(
            'custom-breadcrumb-admin',
            CUSTOM_BREADCRUMB_URL . 'admin/assets/script-advanced.js',
            ['jquery'],
            CUSTOM_BREADCRUMB_VERSION,
            true
        );

        wp_localize_script('custom-breadcrumb-admin', 'customBreadcrumb', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('custom_breadcrumb_save'),
            'settings' => $this->config->get_settings(),
        ]);
    }

    public function ajax_save(): void
    {
        check_ajax_referer('custom_breadcrumb_save', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission refusée']);
            return;
        }

        $settings = isset($_POST['settings']) ? json_decode(stripslashes($_POST['settings']), true) : [];

        if (empty($settings)) {
            wp_send_json_error(['message' => 'Données invalides']);
            return;
        }

        $success = $this->config->update_settings($settings);

        if ($success) {
            wp_send_json_success(['message' => 'Configuration enregistrée']);
        } else {
            wp_send_json_error(['message' => 'Erreur lors de l\'enregistrement']);
        }
    }

    public function render_page(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Permission refusée');
        }

        $settings = $this->config->get_settings();
        
        require_once CUSTOM_BREADCRUMB_PATH . 'admin/views/page-advanced.php';
    }
}
