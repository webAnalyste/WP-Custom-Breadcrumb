<?php

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

if (is_multisite()) {
    $site_ids = get_sites([
        'fields' => 'ids',
        'number' => 0,
    ]);

    foreach ($site_ids as $site_id) {
        switch_to_blog((int) $site_id);
        $settings = get_option('custom_breadcrumb_settings', []);
        $keep = !empty($settings['global']['keep_settings_on_uninstall']);
        if (!$keep) {
            delete_option('custom_breadcrumb_settings');
        }
        restore_current_blog();
    }
} else {
    $settings = get_option('custom_breadcrumb_settings', []);
    $keep = !empty($settings['global']['keep_settings_on_uninstall']);
    if (!$keep) {
        delete_option('custom_breadcrumb_settings');
    }
}
