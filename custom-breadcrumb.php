<?php
/**
 * Plugin Name: Custom Breadcrumb
 * Plugin URI: https://github.com/webAnalyste/WP-Custom-Breadcrumb
 * Description: Personnalisez vos fils d'Ariane en quelques clics
 * Version: 2.0.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: webAnalyste
 * Author URI: https://www.webanalyste.com
 * Text Domain: custom-breadcrumb
 * License: GPL v2 or later
 * GitHub Plugin URI: webAnalyste/WP-Custom-Breadcrumb
 * GitHub Branch: main
 * Update URI: https://github.com/webAnalyste/WP-Custom-Breadcrumb
 */

if (! defined('ABSPATH')) {
    exit;
}

define('CUSTOM_BREADCRUMB_VERSION', '2.0.0');
define('CUSTOM_BREADCRUMB_FILE', __FILE__);
define('CUSTOM_BREADCRUMB_PATH', plugin_dir_path(__FILE__));
define('CUSTOM_BREADCRUMB_URL', plugin_dir_url(__FILE__));

require_once CUSTOM_BREADCRUMB_PATH . 'includes/class-config.php';
require_once CUSTOM_BREADCRUMB_PATH . 'includes/class-context.php';
require_once CUSTOM_BREADCRUMB_PATH . 'includes/class-builder.php';
require_once CUSTOM_BREADCRUMB_PATH . 'includes/class-renderer.php';
require_once CUSTOM_BREADCRUMB_PATH . 'includes/class-updater.php';
require_once CUSTOM_BREADCRUMB_PATH . 'admin/class-admin.php';

new Custom_Breadcrumb_Updater(
    CUSTOM_BREADCRUMB_FILE,
    'webAnalyste/WP-Custom-Breadcrumb',
    CUSTOM_BREADCRUMB_VERSION
);

class Custom_Breadcrumb
{
    private static ?Custom_Breadcrumb $instance = null;
    private Custom_Breadcrumb_Config $config;
    private Custom_Breadcrumb_Renderer $renderer;
    private Custom_Breadcrumb_Admin $admin;

    public static function instance(): Custom_Breadcrumb
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->config = new Custom_Breadcrumb_Config();
        $this->renderer = new Custom_Breadcrumb_Renderer($this->config);
        
        if (is_admin()) {
            $this->admin = new Custom_Breadcrumb_Admin($this->config);
        }

        add_action('init', [$this, 'register_shortcode']);
        add_filter('the_content', [$this, 'maybe_auto_insert'], 10);
    }

    public function register_shortcode(): void
    {
        add_shortcode('custom_breadcrumb', [$this->renderer, 'render']);
    }

    public function maybe_auto_insert(string $content): string
    {
        if (!is_singular() && !is_archive() && !is_search()) {
            return $content;
        }

        $settings = $this->config->get_global_settings();
        
        if (!empty($settings['auto_insert'])) {
            $breadcrumb = $this->renderer->render();
            return $breadcrumb . $content;
        }

        return $content;
    }

    public function get_renderer(): Custom_Breadcrumb_Renderer
    {
        return $this->renderer;
    }
}

function custom_breadcrumb(): void
{
    echo Custom_Breadcrumb::instance()->get_renderer()->render();
}

function custom_breadcrumb_get(): string
{
    return Custom_Breadcrumb::instance()->get_renderer()->render();
}

register_activation_hook(__FILE__, function() {
    $config = new Custom_Breadcrumb_Config();
    $config->set_defaults();
});

add_action('plugins_loaded', function() {
    Custom_Breadcrumb::instance();
});
