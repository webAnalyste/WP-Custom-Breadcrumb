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

    public function build(string $mode = 'auto'): array
    {
        $this->items = [];

        $settings = $this->config->get_settings();
        $has_rules = !empty($settings['rules']);

        $rule = $this->find_applicable_rule($mode);

        if ($rule) {
            // Règle correspondante trouvée : l'appliquer
            $this->add_home();
            $this->build_from_rule($rule);
        } elseif (!$has_rules) {
            // Aucune règle configurée : comportement par défaut (fallback)
            $this->add_home();
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
        // Règles configurées mais aucune ne correspond → tableau vide → pas de rendu

        return apply_filters('custom_breadcrumb_items', $this->items, $this->context);
    }

    private function find_applicable_rule(string $mode = 'auto'): ?array
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

            // En mode auto, ignorer les règles réservées au shortcode
            if ($mode === 'auto' && ($rule['insertMode'] ?? 'auto') === 'shortcode_only') {
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
                    // Prendre le terme le plus profond (le plus spécifique)
                    $term = $this->get_deepest_term($terms);

                    // Toujours afficher les ancêtres pour les taxonomies hiérarchiques
                    if ($term->parent) {
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
                // Remonter toute la chaîne d'ancêtres pour les taxonomies hiérarchiques
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
        }
    }

    private function add_segment(array $segment): void
    {
        $type = $segment['type'] ?? 'text';
        $value = $segment['value'] ?? '';
        $custom_label = isset($segment['label']) && $segment['label'] !== '' ? $segment['label'] : null;

        // dynamic_cpt n'utilise pas de champ value — les autres oui
        if ($type !== 'dynamic_cpt' && empty($value)) {
            return;
        }

        switch ($type) {
            case 'text':
                $this->items[] = [
                    'label' => $custom_label ?? $value,
                    'url' => '',
                    'type' => 'segment',
                ];
                break;

            case 'page':
                $page_id = intval($value);
                $page = get_post($page_id);
                if ($page) {
                    $this->items[] = [
                        'label' => $custom_label ?? get_the_title($page),
                        'url' => get_permalink($page),
                        'type' => 'segment',
                    ];
                }
                break;

            case 'archive':
                if ($value === 'blog') {
                    $blog_url = get_post_type_archive_link('post') ?: home_url('/blog/');
                    $this->items[] = [
                        'label' => $custom_label ?? 'Blog',
                        'url' => $blog_url,
                        'type' => 'segment',
                    ];
                } else {
                    $archive_url = get_post_type_archive_link($value);
                    $post_type_obj = get_post_type_object($value);
                    if ($post_type_obj) {
                        $this->items[] = [
                            'label' => $custom_label ?? $post_type_obj->labels->name,
                            'url' => $archive_url ?: '',
                            'type' => 'segment',
                        ];
                    }
                }
                break;

            case 'dynamic_cpt':
                // Résolution d'un post CPT via conditions taxonomiques croisées
                if (!$this->context->is_singular()) {
                    break;
                }

                $post = $this->context->get_post();
                if (!$post) {
                    break;
                }

                $target_cpt = $segment['cpt'] ?? '';
                $conditions  = $segment['conditions'] ?? [];

                if (empty($target_cpt) || empty($conditions)) {
                    break;
                }

                $tax_query = ['relation' => 'AND'];

                foreach ($conditions as $condition) {
                    $source_tax = $condition['source_tax'] ?? '';
                    $target_tax = $condition['target_tax'] ?? '';

                    if (empty($source_tax) || empty($target_tax)) {
                        continue;
                    }

                    $terms = get_the_terms($post->ID, $source_tax);

                    if (empty($terms) || is_wp_error($terms)) {
                        continue;
                    }

                    $tax_query[] = [
                        'taxonomy' => $target_tax,
                        'field'    => 'term_id',
                        'terms'    => wp_list_pluck($terms, 'term_id'),
                        'operator' => 'IN',
                    ];
                }

                // Au moins une condition effective doit exister
                if (count($tax_query) <= 1) {
                    break;
                }

                $query = new WP_Query([
                    'post_type'              => $target_cpt,
                    'post_status'            => 'publish',
                    'posts_per_page'         => 1,
                    'tax_query'              => $tax_query,
                    'no_found_rows'          => true,
                    'update_post_term_cache' => false,
                    'update_post_meta_cache' => false,
                ]);

                if ($query->have_posts()) {
                    $found = $query->posts[0];
                    $this->items[] = [
                        'label' => $custom_label ?? get_the_title($found),
                        'url'   => get_permalink($found),
                        'type'  => 'segment',
                    ];
                }
                break;

            case 'taxonomy':
                // Contexte singulier : afficher le vrai terme du post courant
                if ($this->context->is_singular()) {
                    $post = $this->context->get_post();
                    if ($post) {
                        $terms = get_the_terms($post->ID, $value);
                        if (!empty($terms) && !is_wp_error($terms)) {
                            // Terme le plus spécifique (le plus profond dans la hiérarchie)
                            $term = $this->get_deepest_term($terms);

                            // Remonter les ancêtres avant le terme courant
                            if ($term->parent) {
                                $ancestors = get_ancestors($term->term_id, $value);
                                $ancestors = array_reverse($ancestors);
                                foreach ($ancestors as $ancestor_id) {
                                    $ancestor = get_term($ancestor_id, $value);
                                    if ($ancestor && !is_wp_error($ancestor)) {
                                        $this->items[] = [
                                            'label' => $ancestor->name,
                                            'url' => get_term_link($ancestor),
                                            'type' => 'segment',
                                        ];
                                    }
                                }
                            }

                            $this->items[] = [
                                'label' => $custom_label ?? $term->name,
                                'url' => get_term_link($term),
                                'type' => 'segment',
                            ];
                        }
                    }
                } else {
                    // Contexte archive : nom de la taxonomie en fallback
                    $tax_obj = get_taxonomy($value);
                    if ($tax_obj) {
                        $this->items[] = [
                            'label' => $custom_label ?? $tax_obj->labels->singular_name,
                            'url' => '',
                            'type' => 'segment',
                        ];
                    }
                }
                break;
        }
    }

    /**
     * Retourne le terme le plus profond dans la hiérarchie parmi une liste de termes.
     * Évite de prendre un terme parent quand un enfant est disponible.
     */
    private function get_deepest_term(array $terms): \WP_Term
    {
        if (count($terms) === 1) {
            return $terms[0];
        }

        $deepest = $terms[0];
        $max_depth = count(get_ancestors($terms[0]->term_id, $terms[0]->taxonomy));

        foreach (array_slice($terms, 1) as $term) {
            $depth = count(get_ancestors($term->term_id, $term->taxonomy));
            if ($depth > $max_depth) {
                $max_depth = $depth;
                $deepest = $term;
            }
        }

        return $deepest;
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
