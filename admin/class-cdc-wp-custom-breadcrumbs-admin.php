<?php

if (! defined('ABSPATH')) {
    exit;
}

final class CDC_WP_Custom_Breadcrumbs_Admin
{
    private CDC_WP_Custom_Breadcrumbs_Plugin $plugin;

    public function __construct(CDC_WP_Custom_Breadcrumbs_Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function register_menu(): void
    {
        add_options_page(
            __('CDC Breadcrumbs', 'cdc-wp-custom-breadcrumbs'),
            __('CDC Breadcrumbs', 'cdc-wp-custom-breadcrumbs'),
            'manage_options',
            'cdc-wp-custom-breadcrumbs',
            [$this, 'render_page']
        );
    }

    public function register_settings(): void
    {
        register_setting(
            'cdc_wpcb_settings_group',
            CDC_WP_Custom_Breadcrumbs_Plugin::OPTION_NAME,
            [
                'sanitize_callback' => [$this, 'sanitize_settings'],
                'default'           => CDC_WP_Custom_Breadcrumbs_Plugin::default_settings(),
            ]
        );

        add_settings_section(
            'cdc_wpcb_general_section',
            __('Réglages généraux', 'cdc-wp-custom-breadcrumbs'),
            '__return_false',
            'cdc-wp-custom-breadcrumbs'
        );

        add_settings_field(
            'root_label',
            __('Libellé racine', 'cdc-wp-custom-breadcrumbs'),
            [$this, 'render_text_field'],
            'cdc-wp-custom-breadcrumbs',
            'cdc_wpcb_general_section',
            [
                'key'         => 'root_label',
                'placeholder' => __('Accueil', 'cdc-wp-custom-breadcrumbs'),
            ]
        );

        add_settings_field(
            'separator',
            __('Séparateur visuel', 'cdc-wp-custom-breadcrumbs'),
            [$this, 'render_text_field'],
            'cdc-wp-custom-breadcrumbs',
            'cdc_wpcb_general_section',
            [
                'key'         => 'separator',
                'placeholder' => '/',
            ]
        );

        add_settings_field(
            'enable_jsonld',
            __('Activer le JSON-LD', 'cdc-wp-custom-breadcrumbs'),
            [$this, 'render_checkbox_field'],
            'cdc-wp-custom-breadcrumbs',
            'cdc_wpcb_general_section',
            [
                'key'   => 'enable_jsonld',
                'label' => __('Injecter les données structurées BreadcrumbList dans le front.', 'cdc-wp-custom-breadcrumbs'),
            ]
        );

        add_settings_field(
            'auto_insert',
            __('Insertion automatique', 'cdc-wp-custom-breadcrumbs'),
            [$this, 'render_checkbox_field'],
            'cdc-wp-custom-breadcrumbs',
            'cdc_wpcb_general_section',
            [
                'key'   => 'auto_insert',
                'label' => __('Préfixer automatiquement le contenu principal sur les vues compatibles.', 'cdc-wp-custom-breadcrumbs'),
            ]
        );

        add_settings_field(
            'show_on_front_page',
            __('Afficher sur la page d’accueil', 'cdc-wp-custom-breadcrumbs'),
            [$this, 'render_checkbox_field'],
            'cdc-wp-custom-breadcrumbs',
            'cdc_wpcb_general_section',
            [
                'key'   => 'show_on_front_page',
                'label' => __('Afficher un breadcrumb minimal sur la page d’accueil.', 'cdc-wp-custom-breadcrumbs'),
            ]
        );
    }

    public function sanitize_settings($input): array
    {
        $defaults = CDC_WP_Custom_Breadcrumbs_Plugin::default_settings();
        $input    = is_array($input) ? $input : [];

        $settings = [
            'root_label'         => sanitize_text_field($input['root_label'] ?? $defaults['root_label']),
            'separator'          => sanitize_text_field($input['separator'] ?? $defaults['separator']),
            'enable_jsonld'      => empty($input['enable_jsonld']) ? 0 : 1,
            'auto_insert'        => empty($input['auto_insert']) ? 0 : 1,
            'show_on_front_page' => empty($input['show_on_front_page']) ? 0 : 1,
        ];

        if ('' === $settings['root_label']) {
            $settings['root_label'] = $defaults['root_label'];
        }

        if ('' === $settings['separator']) {
            $settings['separator'] = $defaults['separator'];
        }

        $settings['separator'] = function_exists('mb_substr') ? mb_substr($settings['separator'], 0, 10) : substr($settings['separator'], 0, 10);

        $this->plugin->refresh_settings();

        return $settings;
    }

    public function render_text_field(array $args): void
    {
        $settings    = $this->plugin->get_settings();
        $key         = (string) $args['key'];
        $value       = isset($settings[$key]) ? (string) $settings[$key] : '';
        $placeholder = isset($args['placeholder']) ? (string) $args['placeholder'] : '';

        printf(
            '<input type="text" class="regular-text" name="%1$s[%2$s]" value="%3$s" placeholder="%4$s" />',
            esc_attr(CDC_WP_Custom_Breadcrumbs_Plugin::OPTION_NAME),
            esc_attr($key),
            esc_attr($value),
            esc_attr($placeholder)
        );
    }

    public function render_checkbox_field(array $args): void
    {
        $settings = $this->plugin->get_settings();
        $key      = (string) $args['key'];
        $label    = isset($args['label']) ? (string) $args['label'] : '';
        $checked  = ! empty($settings[$key]);

        printf(
            '<label><input type="checkbox" name="%1$s[%2$s]" value="1" %3$s /> %4$s</label>',
            esc_attr(CDC_WP_Custom_Breadcrumbs_Plugin::OPTION_NAME),
            esc_attr($key),
            checked($checked, true, false),
            esc_html($label)
        );
    }

    public function render_page(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        $settings = $this->plugin->get_settings();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('CDC WP Custom Breadcrumbs', 'cdc-wp-custom-breadcrumbs'); ?></h1>
            <p><?php echo esc_html__('Plugin WordPress de breadcrumbs personnalisés avec rendu HTML et données structurées JSON-LD.', 'cdc-wp-custom-breadcrumbs'); ?></p>

            <h2><?php echo esc_html__('État du plugin', 'cdc-wp-custom-breadcrumbs'); ?></h2>
            <table class="widefat striped" style="max-width: 900px; margin-bottom: 24px;">
                <tbody>
                    <tr>
                        <td><strong><?php echo esc_html__('Version', 'cdc-wp-custom-breadcrumbs'); ?></strong></td>
                        <td><?php echo esc_html(CDC_WPCB_VERSION); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo esc_html__('Shortcode', 'cdc-wp-custom-breadcrumbs'); ?></strong></td>
                        <td><code>[cdc_breadcrumbs]</code></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo esc_html__('Fonction PHP', 'cdc-wp-custom-breadcrumbs'); ?></strong></td>
                        <td><code>&lt;?php cdc_wp_custom_breadcrumbs(); ?&gt;</code></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo esc_html__('JSON-LD', 'cdc-wp-custom-breadcrumbs'); ?></strong></td>
                        <td><?php echo ! empty($settings['enable_jsonld']) ? esc_html__('Activé', 'cdc-wp-custom-breadcrumbs') : esc_html__('Désactivé', 'cdc-wp-custom-breadcrumbs'); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo esc_html__('Insertion automatique', 'cdc-wp-custom-breadcrumbs'); ?></strong></td>
                        <td><?php echo ! empty($settings['auto_insert']) ? esc_html__('Activée', 'cdc-wp-custom-breadcrumbs') : esc_html__('Désactivée', 'cdc-wp-custom-breadcrumbs'); ?></td>
                    </tr>
                </tbody>
            </table>

            <form action="options.php" method="post">
                <?php
                settings_fields('cdc_wpcb_settings_group');
                do_settings_sections('cdc-wp-custom-breadcrumbs');
                submit_button(__('Enregistrer les réglages', 'cdc-wp-custom-breadcrumbs'));
                ?>
            </form>
        </div>
        <?php
    }
}
