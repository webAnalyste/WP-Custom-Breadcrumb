<?php
/**
 * Ancien fichier prototype — conservé pour référence uniquement.
 * Ne pas activer : utiliser custom-breadcrumb.php
 */

if (! defined('ABSPATH')) {
    exit;
}

define('CDC_WPCB_VERSION', '0.1.0');
define('CDC_WPCB_FILE', __FILE__);
define('CDC_WPCB_PATH', plugin_dir_path(__FILE__));
define('CDC_WPCB_URL', plugin_dir_url(__FILE__));

require_once CDC_WPCB_PATH . 'includes/class-cdc-wp-custom-breadcrumbs-plugin.php';
require_once CDC_WPCB_PATH . 'admin/class-cdc-wp-custom-breadcrumbs-admin.php';
require_once CDC_WPCB_PATH . 'public/class-cdc-wp-custom-breadcrumbs-public.php';

function cdc_wp_custom_breadcrumbs_plugin(): CDC_WP_Custom_Breadcrumbs_Plugin
{
    return CDC_WP_Custom_Breadcrumbs_Plugin::instance();
}

function cdc_wp_custom_breadcrumbs(): void
{
    echo cdc_wp_custom_breadcrumbs_plugin()->get_public()->render_current_breadcrumb();
}

register_activation_hook(__FILE__, ['CDC_WP_Custom_Breadcrumbs_Plugin', 'activate']);

add_action(
    'plugins_loaded',
    static function (): void {
        cdc_wp_custom_breadcrumbs_plugin()->register_hooks();
    }
);
