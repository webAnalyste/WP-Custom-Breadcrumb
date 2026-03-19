<?php

if (! defined('ABSPATH')) {
    exit;
}

final class CDC_WP_Custom_Breadcrumbs_Public
{
    private CDC_WP_Custom_Breadcrumbs_Plugin $plugin;

    public function __construct(CDC_WP_Custom_Breadcrumbs_Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function render_shortcode($atts = [], ?string $content = null, string $tag = ''): string
    {
        return $this->render_current_breadcrumb();
    }

    public function maybe_prepend_to_content(string $content): string
    {
        if (is_admin() || ! is_main_query() || ! in_the_loop()) {
            return $content;
        }

        $settings = $this->plugin->get_settings();

        if (empty($settings['auto_insert'])) {
            return $content;
        }

        if (! is_singular() && ! is_home()) {
            return $content;
        }

        $breadcrumb = $this->render_current_breadcrumb();

        if ('' === $breadcrumb) {
            return $content;
        }

        return $breadcrumb . $content;
    }

    public function render_current_breadcrumb(): string
    {
        $items = $this->get_items_for_current_context();

        if ([] === $items) {
            return '';
        }

        $settings  = $this->plugin->get_settings();
        $separator = isset($settings['separator']) ? (string) $settings['separator'] : '/';
        $output    = '<nav class="cdc-wpcb" aria-label="' . esc_attr__('Fil d\'Ariane', 'cdc-wp-custom-breadcrumbs') . '"><ol class="cdc-wpcb__list">';
        $last_index = count($items) - 1;

        foreach ($items as $index => $item) {
            $label      = isset($item['label']) ? (string) $item['label'] : '';
            $url        = isset($item['url']) ? (string) $item['url'] : '';
            $is_current = $index === $last_index;
            $classes    = 'cdc-wpcb__item' . ($is_current ? ' cdc-wpcb__item--current' : '');

            $output .= '<li class="' . esc_attr($classes) . '">';

            if ($is_current) {
                $output .= '<span aria-current="page">' . esc_html($label) . '</span>';
            } elseif ('' !== $url) {
                $output .= '<a href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
            } else {
                $output .= '<span>' . esc_html($label) . '</span>';
            }

            if ($index < $last_index) {
                $output .= '<span class="cdc-wpcb__separator" aria-hidden="true">' . esc_html($separator) . '</span>';
            }

            $output .= '</li>';
        }

        $output .= '</ol></nav>';

        return $output;
    }

    public function maybe_output_jsonld(): void
    {
        if (is_admin()) {
            return;
        }

        $settings = $this->plugin->get_settings();

        if (empty($settings['enable_jsonld'])) {
            return;
        }

        $items = $this->get_items_for_current_context();

        if ([] === $items) {
            return;
        }

        $list_items = [];

        foreach ($items as $index => $item) {
            if (empty($item['label'])) {
                continue;
            }

            $list_item = [
                '@type'    => 'ListItem',
                'position' => $index + 1,
                'name'     => $item['label'],
            ];

            if (! empty($item['url'])) {
                $list_item['item'] = $item['url'];
            }

            $list_items[] = $list_item;
        }

        if ([] === $list_items) {
            return;
        }

        $payload = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $list_items,
        ];

        $json = wp_json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (false === $json) {
            return;
        }

        echo '<script type="application/ld+json">' . $json . '</script>';
    }

    private function get_items_for_current_context(): array
    {
        $settings = $this->plugin->get_settings();
        $items    = [];

        if (is_front_page()) {
            if (empty($settings['show_on_front_page'])) {
                return [];
            }

            $items[] = $this->build_item((string) $settings['root_label'], home_url('/'));

            return $items;
        }

        $items[] = $this->build_item((string) $settings['root_label'], home_url('/'));

        if (is_home()) {
            $posts_page_id = (int) get_option('page_for_posts');
            $posts_label   = $posts_page_id ? get_the_title($posts_page_id) : __('Blog', 'cdc-wp-custom-breadcrumbs');
            $posts_url     = $posts_page_id ? get_permalink($posts_page_id) : home_url('/');
            $items[]       = $this->build_item($posts_label, $posts_url);

            return $items;
        }

        if (is_page()) {
            $page = get_queried_object();

            if ($page instanceof WP_Post) {
                $ancestor_ids = array_reverse(get_post_ancestors($page));

                foreach ($ancestor_ids as $ancestor_id) {
                    $items[] = $this->build_item(get_the_title($ancestor_id), get_permalink($ancestor_id));
                }

                $items[] = $this->build_item(get_the_title($page), get_permalink($page));
            }

            return $items;
        }

        if (is_single()) {
            $post = get_queried_object();

            if ($post instanceof WP_Post) {
                $post_type = get_post_type($post);

                if ('post' === $post_type) {
                    $items = array_merge($items, $this->get_post_category_items($post));
                } else {
                    $post_type_object = get_post_type_object($post_type);

                    if ($post_type_object && $post_type_object->has_archive) {
                        $archive_url = get_post_type_archive_link($post_type);
                        $archive_label = $post_type_object->labels->name ?? $post_type_object->label;

                        if ($archive_url && $archive_label) {
                            $items[] = $this->build_item($archive_label, $archive_url);
                        }
                    }
                }

                if (is_post_type_hierarchical($post_type)) {
                    $ancestor_ids = array_reverse(get_post_ancestors($post));

                    foreach ($ancestor_ids as $ancestor_id) {
                        $items[] = $this->build_item(get_the_title($ancestor_id), get_permalink($ancestor_id));
                    }
                }

                $items[] = $this->build_item(get_the_title($post), get_permalink($post));
            }

            return $this->deduplicate_items($items);
        }

        if (is_category() || is_tax()) {
            $term = get_queried_object();

            if ($term instanceof WP_Term) {
                $ancestors = array_reverse(get_ancestors($term->term_id, $term->taxonomy, 'taxonomy'));

                foreach ($ancestors as $ancestor_id) {
                    $ancestor = get_term($ancestor_id, $term->taxonomy);

                    if ($ancestor instanceof WP_Term && ! is_wp_error($ancestor)) {
                        $items[] = $this->build_item($ancestor->name, get_term_link($ancestor));
                    }
                }

                $items[] = $this->build_item($term->name, get_term_link($term));
            }

            return $items;
        }

        if (is_post_type_archive()) {
            $post_type = get_query_var('post_type');
            $post_type = is_array($post_type) ? reset($post_type) : $post_type;
            $object    = $post_type ? get_post_type_object($post_type) : null;
            $label     = $object && ! empty($object->labels->name) ? $object->labels->name : post_type_archive_title('', false);
            $url       = $post_type ? get_post_type_archive_link($post_type) : '';

            $items[] = $this->build_item($label, $url);

            return $items;
        }

        if (is_author()) {
            $author = get_queried_object();

            if ($author instanceof WP_User) {
                $items[] = $this->build_item($author->display_name, get_author_posts_url($author->ID));
            }

            return $items;
        }

        if (is_search()) {
            $items[] = $this->build_item(sprintf(__('Résultats de recherche pour : %s', 'cdc-wp-custom-breadcrumbs'), get_search_query()), get_search_link(get_search_query()));

            return $items;
        }

        if (is_404()) {
            $items[] = $this->build_item(__('Page introuvable', 'cdc-wp-custom-breadcrumbs'));

            return $items;
        }

        $title = wp_get_document_title();

        if ('' !== $title) {
            $items[] = $this->build_item($title);
        }

        return $items;
    }

    private function get_post_category_items(WP_Post $post): array
    {
        $items      = [];
        $categories = get_the_category($post->ID);

        if (empty($categories)) {
            return $items;
        }

        usort(
            $categories,
            static function (WP_Term $left, WP_Term $right): int {
                return $left->parent <=> $right->parent;
            }
        );

        $category  = reset($categories);
        $ancestors = array_reverse(get_ancestors($category->term_id, 'category', 'taxonomy'));

        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_term($ancestor_id, 'category');

            if ($ancestor instanceof WP_Term && ! is_wp_error($ancestor)) {
                $items[] = $this->build_item($ancestor->name, get_term_link($ancestor));
            }
        }

        $items[] = $this->build_item($category->name, get_term_link($category));

        return $items;
    }

    private function deduplicate_items(array $items): array
    {
        $unique = [];
        $seen   = [];

        foreach ($items as $item) {
            $signature = wp_json_encode([$item['label'] ?? '', $item['url'] ?? '']);

            if (isset($seen[$signature])) {
                continue;
            }

            $seen[$signature] = true;
            $unique[]         = $item;
        }

        return $unique;
    }

    private function build_item(string $label, string $url = ''): array
    {
        return [
            'label' => wp_strip_all_tags($label),
            'url'   => is_wp_error($url) ? '' : $url,
        ];
    }
}
