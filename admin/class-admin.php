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
        add_action('wp_ajax_custom_breadcrumb_reset', [$this, 'ajax_reset']);
        add_filter('plugin_row_meta', [$this, 'plugin_row_meta'], 10, 2);
        add_filter('plugin_action_links_' . plugin_basename(CUSTOM_BREADCRUMB_FILE), [$this, 'plugin_action_links']);
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
            'ajaxUrl'    => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('custom_breadcrumb_save'),
            'settings'   => $this->config->get_settings(),
            'postTypes'  => $this->get_public_post_types(),
            'taxonomies' => $this->get_public_taxonomies(),
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
            // Forcer WordPress à re-vérifier les mises à jour au prochain chargement
            delete_site_transient('update_plugins');
            wp_send_json_success(['message' => 'Configuration enregistrée']);
        } else {
            wp_send_json_error(['message' => 'Erreur lors de l\'enregistrement']);
        }
    }

    public function ajax_reset(): void
    {
        check_ajax_referer('custom_breadcrumb_save', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission refusée']);
            return;
        }

        delete_option('custom_breadcrumb_settings');
        wp_send_json_success(['message' => 'Configuration supprimée']);
    }

    private function get_public_post_types(): array
    {
        $post_types = get_post_types(['public' => true], 'objects');
        $result = [];
        foreach ($post_types as $pt) {
            if ($pt->name === 'attachment') {
                continue;
            }
            $result[] = ['name' => $pt->name, 'label' => $pt->labels->name];
        }
        return $result;
    }

    private function get_public_taxonomies(): array
    {
        $taxonomies = get_taxonomies(['public' => true], 'objects');
        $result = [];
        foreach ($taxonomies as $tax) {
            if ($tax->name === 'post_format') {
                continue;
            }
            $result[] = ['name' => $tax->name, 'label' => $tax->labels->singular_name];
        }
        return $result;
    }

    public function plugin_action_links(array $links): array
    {
        $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=custom-breadcrumb')) . '">' . __('Réglages') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function plugin_row_meta(array $links, string $file): array
    {
        if (plugin_basename(CUSTOM_BREADCRUMB_FILE) !== $file) {
            return $links;
        }

        $links[] = '<a href="https://github.com/webAnalyste/WP-Custom-Breadcrumb" target="_blank">GitHub</a>';
        $links[] = '<a href="https://github.com/webAnalyste/WP-Custom-Breadcrumb/blob/main/README.md" target="_blank">Documentation</a>';
        $links[] = '<a href="https://github.com/webAnalyste/WP-Custom-Breadcrumb/blob/main/CHANGELOG.md" target="_blank">Changelog</a>';

        return $links;
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
