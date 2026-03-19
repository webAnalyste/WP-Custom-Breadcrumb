<?php

if (! defined('ABSPATH')) {
    exit;
}

final class CDC_WP_Custom_Breadcrumbs_Plugin
{
    public const OPTION_NAME = 'cdc_wpcb_settings';

    private static ?CDC_WP_Custom_Breadcrumbs_Plugin $instance = null;

    private CDC_WP_Custom_Breadcrumbs_Admin $admin;

    private CDC_WP_Custom_Breadcrumbs_Public $public;

    private ?array $settings = null;

    private function __construct()
    {
        $this->admin  = new CDC_WP_Custom_Breadcrumbs_Admin($this);
        $this->public = new CDC_WP_Custom_Breadcrumbs_Public($this);
    }

    public static function instance(): CDC_WP_Custom_Breadcrumbs_Plugin
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function activate(): void
    {
        if (! get_option(self::OPTION_NAME)) {
            add_option(self::OPTION_NAME, self::default_settings());
        }
    }

    public static function default_settings(): array
    {
        return [
            'root_label'         => __('Accueil', 'cdc-wp-custom-breadcrumbs'),
            'separator'          => '/',
            'enable_jsonld'      => 1,
            'auto_insert'        => 0,
            'show_on_front_page' => 0,
        ];
    }

    public function register_hooks(): void
    {
        if (is_admin()) {
            add_action('admin_init', [$this->admin, 'register_settings']);
            add_action('admin_menu', [$this->admin, 'register_menu']);
        }

        add_shortcode('cdc_breadcrumbs', [$this->public, 'render_shortcode']);
        add_filter('the_content', [$this->public, 'maybe_prepend_to_content']);
        add_action('wp_head', [$this->public, 'maybe_output_jsonld']);
    }

    public function get_settings(): array
    {
        if (null === $this->settings) {
            $saved_settings  = get_option(self::OPTION_NAME, []);
            $saved_settings  = is_array($saved_settings) ? $saved_settings : [];
            $this->settings  = wp_parse_args($saved_settings, self::default_settings());
            $this->settings['enable_jsonld']      = empty($this->settings['enable_jsonld']) ? 0 : 1;
            $this->settings['auto_insert']        = empty($this->settings['auto_insert']) ? 0 : 1;
            $this->settings['show_on_front_page'] = empty($this->settings['show_on_front_page']) ? 0 : 1;
        }

        return $this->settings;
    }

    public function refresh_settings(): void
    {
        $this->settings = null;
    }

    public function get_admin(): CDC_WP_Custom_Breadcrumbs_Admin
    {
        return $this->admin;
    }

    public function get_public(): CDC_WP_Custom_Breadcrumbs_Public
    {
        return $this->public;
    }
}
