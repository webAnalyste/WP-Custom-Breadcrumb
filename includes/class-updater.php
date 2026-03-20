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
                'package' => "https://github.com/{$this->github_repo}/releases/download/v{$remote_version}/custom-breadcrumb-{$remote_version}.zip",
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
            'download_link' => "https://github.com/{$this->github_repo}/releases/download/v{$remote_version}/custom-breadcrumb-{$remote_version}.zip",
            'sections' => [
                'description' => $this->get_readme(),
                'changelog' => $changelog,
            ],
        ];

        return (object) $info;
    }

    public function after_install($response, $hook_extra, $result)
    {
        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->plugin_slug) {
            return $result;
        }

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

    private function get_readme(): string
    {
        $response = wp_remote_get(
            "https://raw.githubusercontent.com/{$this->github_repo}/main/README.md",
            ['timeout' => 10]
        );

        if (is_wp_error($response)) {
            return 'Voir la documentation sur <a href="https://github.com/' . esc_attr($this->github_repo) . '" target="_blank">GitHub</a>.';
        }

        return $this->markdown_to_html(wp_remote_retrieve_body($response));
    }

    private function markdown_to_html(string $md): string
    {
        // Titres
        $md = preg_replace('/^### (.+)$/m', '<h4>$1</h4>', $md);
        $md = preg_replace('/^## (.+)$/m', '<h3>$1</h3>', $md);
        $md = preg_replace('/^# (.+)$/m', '<h2>$1</h2>', $md);
        // Gras
        $md = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $md);
        // Italique
        $md = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $md);
        // Code inline
        $md = preg_replace('/`([^`\n]+)`/', '<code>$1</code>', $md);
        // Blocs code
        $md = preg_replace('/```[\w]*\n(.*?)```/s', '<pre><code>$1</code></pre>', $md);
        // Liens
        $md = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank">$1</a>', $md);
        // Listes
        $md = preg_replace('/^- (.+)$/m', '<li>$1</li>', $md);
        $md = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $md);
        // Paragraphes (doubles sauts de ligne)
        $md = preg_replace('/\n{2,}/', '</p><p>', $md);

        return '<p>' . trim($md) . '</p>';
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
