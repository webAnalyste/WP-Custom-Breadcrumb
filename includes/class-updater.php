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
        $this->version     = $version;

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
            $transient->response[$this->plugin_slug] = (object) [
                'slug'         => dirname($this->plugin_slug),
                'plugin'       => $this->plugin_slug,
                'new_version'  => $remote_version,
                'url'          => "https://github.com/{$this->github_repo}",
                'package'      => "https://github.com/{$this->github_repo}/releases/download/v{$remote_version}/custom-breadcrumb-{$remote_version}.zip",
                'tested'       => '6.7',
                'requires_php' => '7.4',
            ];
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

        return (object) [
            'name'          => 'Custom Breadcrumb',
            'slug'          => dirname($this->plugin_slug),
            'version'       => $remote_version,
            'author'        => '<a href="https://www.webanalyste.com">webAnalyste</a>',
            'homepage'      => "https://github.com/{$this->github_repo}",
            'requires'      => '6.4',
            'tested'        => '6.7',
            'requires_php'  => '7.4',
            'download_link' => "https://github.com/{$this->github_repo}/releases/download/v{$remote_version}/custom-breadcrumb-{$remote_version}.zip",
            'sections'      => [
                'description' => $this->get_readme(),
                'changelog'   => $this->get_changelog(),
            ],
        ];
    }

    public function after_install($response, $hook_extra, $result)
    {
        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->plugin_slug) {
            return $result;
        }

        global $wp_filesystem;

        $install_directory = plugin_dir_path($this->plugin_file);

        // Déplacer uniquement si la destination diffère (cas d'un dossier mal nommé après extraction)
        if (realpath($result['destination']) !== realpath($install_directory)) {
            if ($wp_filesystem->exists($install_directory)) {
                $wp_filesystem->delete($install_directory, true);
            }
            $wp_filesystem->move($result['destination'], $install_directory);
            $result['destination'] = $install_directory;
        }

        return $result;
    }

    private function get_remote_version(): ?string
    {
        $response = wp_remote_get(
            "https://api.github.com/repos/{$this->github_repo}/releases/latest",
            [
                'timeout'    => 10,
                'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url'),
                'headers'    => ['Accept' => 'application/vnd.github.v3+json'],
            ]
        );

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return null;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        return isset($data['tag_name']) ? ltrim($data['tag_name'], 'v') : null;
    }

    private function get_readme(): string
    {
        $response = wp_remote_get(
            "https://raw.githubusercontent.com/{$this->github_repo}/main/README.md",
            [
                'timeout'    => 10,
                'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url'),
            ]
        );

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return 'Voir la documentation sur <a href="https://github.com/' . esc_attr($this->github_repo) . '" target="_blank">GitHub</a>.';
        }

        return $this->markdown_to_html(wp_remote_retrieve_body($response));
    }

    private function get_changelog(): string
    {
        $response = wp_remote_get(
            "https://raw.githubusercontent.com/{$this->github_repo}/main/CHANGELOG.md",
            [
                'timeout'    => 10,
                'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url'),
            ]
        );

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return 'Voir le changelog sur <a href="https://github.com/' . esc_attr($this->github_repo) . '/blob/main/CHANGELOG.md" target="_blank">GitHub</a>.';
        }

        return nl2br(esc_html(wp_remote_retrieve_body($response)));
    }

    private function markdown_to_html(string $md): string
    {
        $md = preg_replace('/^### (.+)$/m', '<h4>$1</h4>', $md);
        $md = preg_replace('/^## (.+)$/m', '<h3>$1</h3>', $md);
        $md = preg_replace('/^# (.+)$/m', '<h2>$1</h2>', $md);
        $md = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $md);
        $md = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $md);
        $md = preg_replace('/`([^`\n]+)`/', '<code>$1</code>', $md);
        $md = preg_replace('/```[\w]*\n(.*?)```/s', '<pre><code>$1</code></pre>', $md);
        $md = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank">$1</a>', $md);
        $md = preg_replace('/^- (.+)$/m', '<li>$1</li>', $md);
        $md = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $md);
        $md = preg_replace('/\n{2,}/', '</p><p>', $md);

        return '<p>' . trim($md) . '</p>';
    }
}
