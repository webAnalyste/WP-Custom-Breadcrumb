<?php
/**
 * Rendu HTML et JSON-LD du breadcrumb
 */

if (! defined('ABSPATH')) {
    exit;
}

class Custom_Breadcrumb_Renderer
{
    private Custom_Breadcrumb_Config $config;

    public function __construct(Custom_Breadcrumb_Config $config)
    {
        $this->config = $config;
        
        add_action('wp_head', [$this, 'render_jsonld']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
    }

    public function enqueue_styles(): void
    {
        wp_enqueue_style(
            'custom-breadcrumb',
            CUSTOM_BREADCRUMB_URL . 'assets/breadcrumb.css',
            [],
            CUSTOM_BREADCRUMB_VERSION
        );
    }

    public function render(array $atts = []): string
    {
        $context = new Custom_Breadcrumb_Context();
        
        if ($context->get_type() === 'front_page') {
            return '';
        }

        $builder = new Custom_Breadcrumb_Builder($this->config, $context);
        $items = $builder->build();

        if (empty($items)) {
            return '';
        }

        $html = $this->render_html($items);
        
        return apply_filters('custom_breadcrumb_html', $html, $items);
    }

    private function render_html(array $items): string
    {
        $settings = $this->config->get_global_settings();
        $separator = esc_html($settings['separator'] ?? '/');

        $alignment = $settings['alignment'] ?? 'left';
        $html = '<nav class="custom-breadcrumb custom-breadcrumb--' . esc_attr($alignment) . '" aria-label="Fil d\'Ariane">';
        $html .= '<ol class="custom-breadcrumb__list">';

        $total = count($items);
        
        foreach ($items as $index => $item) {
            $is_last = ($index === $total - 1);
            $classes = ['custom-breadcrumb__item'];
            
            if ($item['type'] === 'current' || $is_last) {
                $classes[] = 'custom-breadcrumb__item--current';
            }

            $html .= '<li class="' . esc_attr(implode(' ', $classes)) . '">';

            if (!empty($item['url']) && !$is_last) {
                $html .= '<a href="' . esc_url($item['url']) . '">';
                $html .= esc_html($item['label']);
                $html .= '</a>';
            } else {
                $html .= '<span aria-current="page">' . esc_html($item['label']) . '</span>';
            }

            if (!$is_last) {
                $html .= '<span class="custom-breadcrumb__separator" aria-hidden="true">' . $separator . '</span>';
            }

            $html .= '</li>';
        }

        $html .= '</ol>';
        $html .= '</nav>';

        return $html;
    }

    public function render_jsonld(): void
    {
        $settings = $this->config->get_global_settings();
        
        if (empty($settings['enable_jsonld'])) {
            return;
        }

        $context = new Custom_Breadcrumb_Context();
        
        if ($context->get_type() === 'front_page') {
            return;
        }

        $builder = new Custom_Breadcrumb_Builder($this->config, $context);
        $items = $builder->build();

        if (empty($items)) {
            return;
        }

        $jsonld = $this->build_jsonld($items);
        
        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo "\n" . '</script>' . "\n";
    }

    private function build_jsonld(array $items): array
    {
        $list_items = [];
        $position = 1;

        foreach ($items as $item) {
            $list_item = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $item['label'],
            ];

            if (!empty($item['url'])) {
                $list_item['item'] = $item['url'];
            }

            $list_items[] = $list_item;
            $position++;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list_items,
        ];
    }
}
