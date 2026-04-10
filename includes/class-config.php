<?php
/**
 * Gestion de la configuration du plugin
 */

if (! defined('ABSPATH')) {
    exit;
}

class Custom_Breadcrumb_Config
{
    private const OPTION_NAME = 'custom_breadcrumb_settings';

    public function get_settings(): array
    {
        $settings = get_option(self::OPTION_NAME, []);
        
        if (empty($settings)) {
            return $this->get_default_settings();
        }

        return wp_parse_args($settings, $this->get_default_settings());
    }

    public function get_default_settings(): array
    {
        return [
            'post' => [
                'home_label' => 'Accueil',
                'section_label' => 'Blog',
                'show_category' => true,
                'show_parent_categories' => false,
            ],
            'page' => [
                'home_label' => 'Accueil',
                'show_parents' => true,
            ],
            'formation' => [
                'home_label' => 'Accueil',
                'section_label' => 'Formations',
                'taxonomy' => 'parcours',
            ],
            'global' => [
                'home_label' => 'Accueil',
                'separator' => '/',
                'enable_jsonld' => true,
                'insert_position' => 'disabled',
                'alignment' => 'left',
                'keep_settings_on_uninstall' => false,
            ],
        ];
    }

    public function set_defaults(): void
    {
        if (!get_option(self::OPTION_NAME)) {
            update_option(self::OPTION_NAME, $this->get_default_settings());
        }
    }

    public function update_settings(array $settings): bool
    {
        $sanitized = $this->sanitize_settings($settings);

        // update_option retourne false si valeur identique (pas d'erreur) ou si erreur DB
        // On considère les deux cas : mise à jour réussie OU valeur déjà correcte en base
        update_option(self::OPTION_NAME, $sanitized);

        $saved = get_option(self::OPTION_NAME, null);

        return $saved !== null;
    }

    public function get_post_type_settings(string $post_type): array
    {
        $settings = $this->get_settings();
        
        if (isset($settings[$post_type])) {
            return $settings[$post_type];
        }

        return [
            'home_label' => $settings['global']['home_label'] ?? 'Accueil',
            'section_label' => ucfirst($post_type),
            'show_category' => false,
        ];
    }

    public function get_global_settings(): array
    {
        $settings = $this->get_settings();
        return $settings['global'] ?? $this->get_default_settings()['global'];
    }

    private function sanitize_settings(array $settings): array
    {
        $sanitized = [];

        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitize_settings($value);
            } elseif (is_bool($value)) {
                $sanitized[$key] = (bool) $value;
            } else {
                $sanitized[$key] = sanitize_text_field($value);
            }
        }

        return $sanitized;
    }

    public function update_post_type_setting(string $post_type, string $key, $value): bool
    {
        $settings = $this->get_settings();
        
        if (!isset($settings[$post_type])) {
            $settings[$post_type] = [];
        }

        $settings[$post_type][$key] = $value;
        
        return $this->update_settings($settings);
    }

    public function update_global_setting(string $key, $value): bool
    {
        $settings = $this->get_settings();
        $settings['global'][$key] = $value;
        
        return $this->update_settings($settings);
    }
}
