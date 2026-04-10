<?php
/**
 * Plugin Name: Custom Breadcrumb
 * Plugin URI: https://github.com/webAnalyste/WP-Custom-Breadcrumb
 * Description: Personnalisez vos fils d'Ariane en quelques clics
 * Version: 2.1.15
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

define('CUSTOM_BREADCRUMB_VERSION', '2.1.15');
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
        add_action('wp', [$this, 'register_position_hooks']);
    }

    public function register_shortcode(): void
    {
        add_shortcode('custom_breadcrumb', function(array $atts = []) {
            return $this->renderer->render($atts, 'shortcode');
        });
    }

    public function register_position_hooks(): void
    {
        $settings = $this->config->get_global_settings();
        $position = $settings['insert_position'] ?? 'disabled';

        if ($position === 'disabled') {
            return;
        }

        if ($position === 'before_article') {
            add_action('loop_start', [$this, 'render_before_article'], 10);
        } else {
            add_filter('the_content', [$this, 'inject_into_content'], 10);
        }
    }

    public function render_before_article(\WP_Query $query): void
    {
        static $rendered = false;

        if ($rendered || is_admin() || !$query->is_main_query() || is_front_page()) {
            return;
        }

        $rendered = true;
        echo $this->renderer->render();
    }

    public function inject_into_content(string $content): string
    {
        if (is_admin() || !in_the_loop() || !is_main_query()) {
            return $content;
        }

        $settings = $this->config->get_global_settings();
        $position = $settings['insert_position'] ?? 'before_content';
        $breadcrumb = $this->renderer->render();

        if ($position === 'after_content') {
            return $content . $breadcrumb;
        }

        // before_content (default)
        return $breadcrumb . $content;
    }

    public function get_renderer(): Custom_Breadcrumb_Renderer
    {
        return $this->renderer;
    }
}

function custom_breadcrumb(): void
{
    echo Custom_Breadcrumb::instance()->get_renderer()->render([], 'shortcode');
}

function custom_breadcrumb_get(): string
{
    return Custom_Breadcrumb::instance()->get_renderer()->render([], 'shortcode');
}

register_activation_hook(__FILE__, function() {
    $config = new Custom_Breadcrumb_Config();
    $config->set_defaults();
});

add_action('plugins_loaded', function() {
    Custom_Breadcrumb::instance();
});
