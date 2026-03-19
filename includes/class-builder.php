<?php
/**
 * Construction du breadcrumb selon le contexte
 */

if (! defined('ABSPATH')) {
    exit;
}

class Custom_Breadcrumb_Builder
{
    private Custom_Breadcrumb_Config $config;
    private Custom_Breadcrumb_Context $context;
    private array $items = [];

    public function __construct(Custom_Breadcrumb_Config $config, Custom_Breadcrumb_Context $context)
    {
        $this->config = $config;
        $this->context = $context;
    }

    public function build(): array
    {
        $this->items = [];

        $this->add_home();

        // Chercher une règle applicable
        $rule = $this->find_applicable_rule();

        if ($rule && !empty($rule['enabled'])) {
            // Utiliser la règle configurée
            $this->build_from_rule($rule);
        } else {
            // Fallback sur le comportement par défaut
            switch ($this->context->get_type()) {
                case 'singular':
                    $this->build_singular();
                    break;
                case 'taxonomy':
                    $this->build_taxonomy();
                    break;
                case 'post_type_archive':
                    $this->build_post_type_archive();
                    break;
                case 'search':
                    $this->build_search();
                    break;
                case 'author':
                    $this->build_author();
                    break;
                case '404':
                    $this->build_404();
                    break;
            }
        }

        return apply_filters('custom_breadcrumb_items', $this->items, $this->context);
    }

    private function find_applicable_rule(): ?array
    {
        $settings = $this->config->get_settings();
        
        if (empty($settings['rules'])) {
            return null;
        }

        $current_post_type = $this->context->get_post_type();
        $context_type = $this->context->get_type();

        foreach ($settings['rules'] as $rule) {
            if (empty($rule['enabled'])) {
                continue;
            }

            $rule_post_type = $rule['postType'] ?? '';

            // Correspondance exacte du post type
            if ($context_type === 'singular' && $rule_post_type === $current_post_type) {
                return $rule;
            }

            // Archives de catégories
            if ($context_type === 'taxonomy' && $rule_post_type === 'category') {
                $term = $this->context->get_term();
                if ($term && $term->taxonomy === 'category') {
                    return $rule;
                }
            }

            // Archives de tags
            if ($context_type === 'taxonomy' && $rule_post_type === 'tag') {
                $term = $this->context->get_term();
                if ($term && $term->taxonomy === 'post_tag') {
                    return $rule;
                }
            }

            // Archives de taxonomies personnalisées
            if (strpos($rule_post_type, 'tax_') === 0) {
                $tax_name = substr($rule_post_type, 4);
                $term = $this->context->get_term();
                if ($term && $term->taxonomy === $tax_name) {
                    return $rule;
                }
            }
        }

        return null;
    }

    private function build_from_rule(array $rule): void
    {
        // Ajouter les segments personnalisés
        if (!empty($rule['segments'])) {
            foreach ($rule['segments'] as $segment) {
                $this->add_segment($segment);
            }
        }

        // Afficher la taxonomie si demandé
        if (!empty($rule['showTaxonomy']) && $this->context->is_singular()) {
            $post = $this->context->get_post();
            if ($post) {
                $taxonomy = $rule['taxonomy'] ?? 'category';
                $terms = get_the_terms($post->ID, $taxonomy);
                
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term = $terms[0];
                    
                    // Afficher les parents si demandé
                    if (!empty($rule['showParents']) && $term->parent) {
                        $ancestors = get_ancestors($term->term_id, $taxonomy);
                        $ancestors = array_reverse($ancestors);
                        
                        foreach ($ancestors as $ancestor_id) {
                            $ancestor = get_term($ancestor_id, $taxonomy);
                            if ($ancestor && !is_wp_error($ancestor)) {
                                $this->items[] = [
                                    'label' => $ancestor->name,
                                    'url' => get_term_link($ancestor),
                                    'type' => 'taxonomy',
                                ];
                            }
                        }
                    }
                    
                    $this->items[] = [
                        'label' => $term->name,
                        'url' => get_term_link($term),
                        'type' => 'taxonomy',
                    ];
                }
            }
        }

        // Afficher les parents hiérarchiques pour les pages
        if (!empty($rule['showParents']) && $this->context->is_singular()) {
            $post = $this->context->get_post();
            if ($post && $post->post_type === 'page' && $post->post_parent) {
                $ancestors = get_post_ancestors($post);
                $ancestors = array_reverse($ancestors);

                foreach ($ancestors as $ancestor_id) {
                    $this->items[] = [
                        'label' => get_the_title($ancestor_id),
                        'url' => get_permalink($ancestor_id),
                        'type' => 'ancestor',
                    ];
                }
            }
        }

        // Ajouter l'élément actuel
        if ($this->context->is_singular()) {
            $post = $this->context->get_post();
            if ($post) {
                $this->items[] = [
                    'label' => get_the_title($post),
                    'url' => '',
                    'type' => 'current',
                ];
            }
        } elseif ($this->context->is_taxonomy()) {
            $term = $this->context->get_term();
            if ($term) {
                $this->items[] = [
                    'label' => $term->name,
                    'url' => '',
                    'type' => 'current',
                ];
            }
        }
    }

    private function add_segment(array $segment): void
    {
        $type = $segment['type'] ?? 'text';
        $value = $segment['value'] ?? '';

        if (empty($value)) {
            return;
        }

        switch ($type) {
            case 'text':
                $this->items[] = [
                    'label' => $value,
                    'url' => '',
                    'type' => 'segment',
                ];
                break;

            case 'page':
                $page_id = intval($value);
                $page = get_post($page_id);
                if ($page) {
                    $this->items[] = [
                        'label' => get_the_title($page),
                        'url' => get_permalink($page),
                        'type' => 'segment',
                    ];
                }
                break;

            case 'archive':
                if ($value === 'blog') {
                    $blog_url = get_post_type_archive_link('post') ?: home_url('/blog/');
                    $this->items[] = [
                        'label' => 'Blog',
                        'url' => $blog_url,
                        'type' => 'segment',
                    ];
                } else {
                    $archive_url = get_post_type_archive_link($value);
                    $post_type_obj = get_post_type_object($value);
                    if ($post_type_obj) {
                        $this->items[] = [
                            'label' => $post_type_obj->labels->name,
                            'url' => $archive_url ?: '',
                            'type' => 'segment',
                        ];
                    }
                }
                break;

            case 'taxonomy':
                // Pour les taxonomies dans les segments, on affiche juste le nom
                $tax_obj = get_taxonomy($value);
                if ($tax_obj) {
                    $this->items[] = [
                        'label' => $tax_obj->labels->singular_name,
                        'url' => '',
                        'type' => 'segment',
                    ];
                }
                break;
        }
    }

    private function add_home(): void
    {
        $global_settings = $this->config->get_global_settings();
        $home_label = $global_settings['home_label'] ?? 'Accueil';

        $this->items[] = [
            'label' => $home_label,
            'url' => home_url('/'),
            'type' => 'home',
        ];
    }

    private function build_singular(): void
    {
        $post = $this->context->get_post();
        if (!$post) {
            return;
        }

        $post_type = $post->post_type;
        $settings = $this->config->get_post_type_settings($post_type);

        if ($post_type === 'page') {
            $this->build_page_hierarchy($post);
        } elseif ($post_type === 'post') {
            $this->build_post_breadcrumb($post, $settings);
        } else {
            $this->build_cpt_breadcrumb($post, $settings);
        }

        $this->items[] = [
            'label' => get_the_title($post),
            'url' => '',
            'type' => 'current',
        ];
    }

    private function build_page_hierarchy(WP_Post $post): void
    {
        $settings = $this->config->get_post_type_settings('page');
        
        if (empty($settings['show_parents'])) {
            return;
        }

        $ancestors = get_post_ancestors($post);
        $ancestors = array_reverse($ancestors);

        foreach ($ancestors as $ancestor_id) {
            $this->items[] = [
                'label' => get_the_title($ancestor_id),
                'url' => get_permalink($ancestor_id),
                'type' => 'ancestor',
            ];
        }
    }

    private function build_post_breadcrumb(WP_Post $post, array $settings): void
    {
        if (!empty($settings['section_label'])) {
            $blog_url = get_post_type_archive_link('post') ?: home_url('/blog/');
            
            $this->items[] = [
                'label' => $settings['section_label'],
                'url' => $blog_url,
                'type' => 'section',
            ];
        }

        if (!empty($settings['show_category'])) {
            $categories = get_the_category($post->ID);
            
            if (!empty($categories)) {
                $category = $categories[0];
                
                if (!empty($settings['show_parent_categories']) && $category->parent) {
                    $parent_cats = get_ancestors($category->term_id, 'category');
                    $parent_cats = array_reverse($parent_cats);
                    
                    foreach ($parent_cats as $parent_id) {
                        $parent = get_term($parent_id, 'category');
                        if ($parent && !is_wp_error($parent)) {
                            $this->items[] = [
                                'label' => $parent->name,
                                'url' => get_term_link($parent),
                                'type' => 'category',
                            ];
                        }
                    }
                }

                $this->items[] = [
                    'label' => $category->name,
                    'url' => get_term_link($category),
                    'type' => 'category',
                ];
            }
        }
    }

    private function build_cpt_breadcrumb(WP_Post $post, array $settings): void
    {
        if (!empty($settings['section_label'])) {
            $archive_url = get_post_type_archive_link($post->post_type);
            
            $this->items[] = [
                'label' => $settings['section_label'],
                'url' => $archive_url ?: '',
                'type' => 'section',
            ];
        }

        if (!empty($settings['taxonomy'])) {
            $terms = get_the_terms($post->ID, $settings['taxonomy']);
            
            if (!empty($terms) && !is_wp_error($terms)) {
                $term = $terms[0];
                
                $this->items[] = [
                    'label' => $term->name,
                    'url' => get_term_link($term),
                    'type' => 'taxonomy',
                ];
            }
        }
    }

    private function build_taxonomy(): void
    {
        $term = $this->context->get_term();
        if (!$term) {
            return;
        }

        if ($term->parent) {
            $ancestors = get_ancestors($term->term_id, $term->taxonomy);
            $ancestors = array_reverse($ancestors);
            
            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_term($ancestor_id, $term->taxonomy);
                if ($ancestor && !is_wp_error($ancestor)) {
                    $this->items[] = [
                        'label' => $ancestor->name,
                        'url' => get_term_link($ancestor),
                        'type' => 'taxonomy',
                    ];
                }
            }
        }

        $this->items[] = [
            'label' => $term->name,
            'url' => '',
            'type' => 'current',
        ];
    }

    private function build_post_type_archive(): void
    {
        $post_type = $this->context->get_post_type();
        $post_type_obj = get_post_type_object($post_type);
        
        if ($post_type_obj) {
            $this->items[] = [
                'label' => $post_type_obj->labels->name,
                'url' => '',
                'type' => 'current',
            ];
        }
    }

    private function build_search(): void
    {
        $this->items[] = [
            'label' => sprintf('Résultats pour : %s', get_search_query()),
            'url' => '',
            'type' => 'current',
        ];
    }

    private function build_author(): void
    {
        $author = get_queried_object();
        
        if ($author) {
            $this->items[] = [
                'label' => sprintf('Articles de %s', $author->display_name),
                'url' => '',
                'type' => 'current',
            ];
        }
    }

    private function build_404(): void
    {
        $this->items[] = [
            'label' => 'Page non trouvée',
            'url' => '',
            'type' => 'current',
        ];
    }
}
