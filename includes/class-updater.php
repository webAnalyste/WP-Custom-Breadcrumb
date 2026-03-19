<?php
/**
 * Système de mise à jour automatique depuis GitHub
 */

if (! defined('ABSPATH')) {
    exit;
}

class Custom_Breadcrumb_Updater
{
    private string $plugin_slug;
    private string $plugin_file;
    private string $github_repo;
    private string $version;

    public function __construct(string $plugin_file, string $github_repo, string $version)
    {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = plugin_basename($plugin_file);
        $this->github_repo = $github_repo;
        $this->version = $version;

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 20, 3);
        add_filter('upgrader_post_install', [$this, 'after_install'], 10, 3);
    }

    public function check_update($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $remote_version = $this->get_remote_version();

        if ($remote_version && version_compare($this->version, $remote_version, '<')) {
            $plugin_data = [
                'slug' => dirname($this->plugin_slug),
                'plugin' => $this->plugin_slug,
                'new_version' => $remote_version,
                'url' => "https://github.com/{$this->github_repo}",
                'package' => "https://github.com/{$this->github_repo}/archive/refs/tags/v{$remote_version}.zip",
                'tested' => '6.4',
                'requires_php' => '7.4',
            ];

            $transient->response[$this->plugin_slug] = (object) $plugin_data;
        }

        return $transient;
    }

    public function plugin_info($false, $action, $args)
    {
        if ($action !== 'plugin_information') {
            return $false;
        }

        if (!isset($args->slug) || $args->slug !== dirname($this->plugin_slug)) {
            return $false;
        }

        $remote_version = $this->get_remote_version();
        $changelog = $this->get_changelog();

        $info = [
            'name' => 'Custom Breadcrumb',
            'slug' => dirname($this->plugin_slug),
            'version' => $remote_version,
            'author' => '<a href="https://www.webanalyste.com">webAnalyste</a>',
            'homepage' => "https://github.com/{$this->github_repo}",
            'requires' => '6.4',
            'tested' => '6.4',
            'requires_php' => '7.4',
            'download_link' => "https://github.com/{$this->github_repo}/archive/refs/tags/v{$remote_version}.zip",
            'sections' => [
                'description' => 'Personnalisez vos fils d\'Ariane en quelques clics',
                'changelog' => $changelog,
            ],
        ];

        return (object) $info;
    }

    public function after_install($response, $hook_extra, $result)
    {
        global $wp_filesystem;

        $install_directory = plugin_dir_path($this->plugin_file);
        $wp_filesystem->move($result['destination'], $install_directory);
        $result['destination'] = $install_directory;

        if ($this->plugin_slug) {
            activate_plugin($this->plugin_slug);
        }

        return $result;
    }

    private function get_remote_version(): ?string
    {
        $response = wp_remote_get(
            "https://api.github.com/repos/{$this->github_repo}/releases/latest",
            ['timeout' => 10]
        );

        if (is_wp_error($response)) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['tag_name'])) {
            return ltrim($data['tag_name'], 'v');
        }

        return null;
    }

    private function get_changelog(): string
    {
        $response = wp_remote_get(
            "https://raw.githubusercontent.com/{$this->github_repo}/main/CHANGELOG.md",
            ['timeout' => 10]
        );

        if (is_wp_error($response)) {
            return 'Voir le changelog sur GitHub';
        }

        $changelog = wp_remote_retrieve_body($response);
        
        return nl2br(esc_html($changelog));
    }
}
