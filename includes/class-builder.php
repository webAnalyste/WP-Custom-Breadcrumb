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
    public array $debug_log = []; // lignes de debug visibles dans le source HTML

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
        // Ajouter les segments personnalisés.
        // En mode chaîne (chain=true sur un dynamic_cpt), les segments sont pré-résolus
        // en ordre inverse : chaque segment externe requête depuis le post trouvé par le
        // segment interne, plutôt que depuis le post WP courant.
        if (!empty($rule['segments'])) {
            $resolved = $this->pre_resolve_chained_segments($rule['segments']);

            foreach ($rule['segments'] as $idx => $segment) {
                if (array_key_exists($idx, $resolved)) {
                    $found = $resolved[$idx];
                    if ($found) {
                        $custom_label = isset($segment['label']) && $segment['label'] !== '' ? $segment['label'] : null;
                        $this->items[] = [
                            'label' => $custom_label ?? get_the_title($found),
                            'url'   => get_permalink($found),
                            'type'  => 'segment',
                        ];
                    }
                } else {
                    $this->add_segment($segment);
                }
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
                // La logique complète est dans resolve_dynamic_cpt_post().
                // Les segments avec chain=true sont pré-résolus dans build_from_rule()
                // et n'atteignent jamais ce case — seuls les segments normaux passent ici.
                if (!$this->context->is_singular()) {
                    break;
                }
                $post = $this->context->get_post();
                if (!$post) {
                    break;
                }
                $found = $this->resolve_dynamic_cpt_post($segment, $post);
                if (!$found) {
                    break;
                }
                $this->items[] = [
                    'label' => $custom_label ?? get_the_title($found),
                    'url'   => get_permalink($found),
                    'type'  => 'segment',
                ];
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
     * Pré-résout les segments dynamic_cpt en ordre inverse pour le mode chaîne.
     * Retourne [idx => ?\WP_Post] pour chaque segment dynamic_cpt trouvé.
     * Si aucun segment n'a chain=true, retourne [] → traitement normal via add_segment().
     */
    private function pre_resolve_chained_segments(array $segments): array
    {
        $has_chain = false;
        foreach ($segments as $seg) {
            if (($seg['type'] ?? '') === 'dynamic_cpt' && !empty($seg['chain'])) {
                $has_chain = true;
                break;
            }
        }

        if (!$has_chain) {
            return [];
        }

        $resolved     = [];
        $chain_source = null; // post trouvé par le segment plus interne (alimenté au fil du parcours)

        $wp_post = $this->context->get_post();
        $this->debug_log[] = sprintf(
            'CHAIN démarrage — post courant: #%d "%s" (type: %s)',
            $wp_post ? $wp_post->ID : 0,
            $wp_post ? get_the_title($wp_post->ID) : 'none',
            $wp_post ? $wp_post->post_type : 'none'
        );

        for ($i = count($segments) - 1; $i >= 0; $i--) {
            $seg = $segments[$i];
            if (($seg['type'] ?? '') !== 'dynamic_cpt') {
                continue;
            }

            $is_chained  = !empty($seg['chain']);
            $source_post = ($is_chained && $chain_source !== null)
                ? $chain_source
                : $this->context->get_post();

            $this->debug_log[] = sprintf(
                'seg[%d] cpt=%s chain=%s — source: #%d "%s"',
                $i,
                $seg['cpt'] ?? '?',
                $is_chained ? 'true' : 'false',
                $source_post ? $source_post->ID : 0,
                $source_post ? get_the_title($source_post->ID) : 'none'
            );

            $found        = $source_post ? $this->resolve_dynamic_cpt_post($seg, $source_post) : null;
            $resolved[$i] = $found;

            $this->debug_log[] = sprintf(
                'seg[%d] résultat: %s',
                $i,
                $found ? sprintf('#%d "%s"', $found->ID, get_the_title($found->ID)) : 'NULL'
            );

            if ($found) {
                $chain_source = $found; // devient la source pour les segments encore plus externes
            }
        }

        return $resolved;
    }

    /**
     * Requête et sélection du post CPT correspondant aux conditions du segment.
     * Accepte un post source explicite : en mode normal c'est le post WP courant,
     * en mode chaîne c'est le post trouvé par le segment interne.
     */
    private function resolve_dynamic_cpt_post(array $segment, \WP_Post $post): ?\WP_Post
    {
        $target_cpt = $segment['cpt'] ?? '';
        $conditions  = $segment['conditions'] ?? [];

        if (empty($target_cpt) || empty($conditions)) {
            return null;
        }

        $tax_query    = ['relation' => 'AND'];
        $skip_segment = false;
        $post_filters = [];
        $sort_hints   = [];

        foreach ($conditions as $condition) {
            $cond_type = $condition['type'] ?? 'tax_match';
            if ($cond_type === 'tax_cross') {
                $cond_type = 'tax_match';
            }

            if ($cond_type === 'page_level') {
                $page_depth = count(get_post_ancestors($post->ID));
                $operator   = $condition['operator'] ?? '=';
                $n          = intval($condition['value'] ?? 0);
                if (!$this->compare_values($page_depth, $operator, $n)) {
                    $skip_segment = true;
                    break;
                }
                continue;
            }

            if ($cond_type === 'tax_level_compare') {
                $post_filters[] = $condition;
                continue;
            }

            // tax_match
            $source_tax = $condition['source_tax'] ?? '';
            $target_tax = $condition['target_tax'] ?? '';
            $match_mode = $condition['match_mode'] ?? 'exact';

            if (empty($source_tax) || empty($target_tax)) {
                continue;
            }

            $terms = get_the_terms($post->ID, $source_tax);
            if (empty($terms) || is_wp_error($terms)) {
                continue;
            }

            if ($match_mode === 'ancestors') {
                $deepest      = $this->get_deepest_term($terms);
                $ancestor_ids = get_ancestors($deepest->term_id, $source_tax);
                if (empty($ancestor_ids)) {
                    continue;
                }
                $tax_query[] = [
                    'taxonomy' => $target_tax,
                    'field'    => 'term_id',
                    'terms'    => $ancestor_ids,
                    'operator' => 'IN',
                ];
            } elseif ($match_mode === 'ancestors_or_equal') {
                $deepest      = $this->get_deepest_term($terms);
                $ancestor_ids = get_ancestors($deepest->term_id, $source_tax);
                $term_ids     = array_merge([$deepest->term_id], $ancestor_ids);
                $tax_query[]  = [
                    'taxonomy' => $target_tax,
                    'field'    => 'term_id',
                    'terms'    => $term_ids,
                    'operator' => 'IN',
                ];
                $sort_hints[] = [
                    'taxonomy' => $target_tax,
                    'term_id'  => $deepest->term_id,
                ];
            } else {
                if (isset($condition['source_depth'])) {
                    $terms = $this->get_terms_at_depth($terms, $source_tax, intval($condition['source_depth']));
                    if (empty($terms)) {
                        continue;
                    }
                }
                $tax_query[] = [
                    'taxonomy' => $target_tax,
                    'field'    => 'term_id',
                    'terms'    => wp_list_pluck($terms, 'term_id'),
                    'operator' => 'IN',
                ];
            }
        }

        if ($skip_segment || count($tax_query) <= 1) {
            return null;
        }

        // Pour chaque condition ancestors_or_equal sans tax_level_compare explicite,
        // auto-injecter un filtre de profondeur pour trier les candidats et éviter
        // les faux positifs (agence au mauvais niveau).
        // - chain=true  → opérateur '>'  : candidat strictement plus plat (parent réel)
        // - chain=false → opérateur '>=' : candidat au même niveau ou plus plat,
        //   le plus profond d'abord (segment intérieur qui requête depuis le post courant)
        $is_chained             = !empty($segment['chain']);
        $explicit_compare_taxes = [];
        foreach ($post_filters as $f) {
            if (($f['type'] ?? '') === 'tax_level_compare' && !empty($f['taxonomy'])) {
                $explicit_compare_taxes[] = $f['taxonomy'];
            }
        }
        foreach ($conditions as $condition) {
            $ct = $condition['type'] ?? 'tax_match';
            if ($ct === 'tax_cross') {
                $ct = 'tax_match';
            }
            if ($ct !== 'tax_match' || ($condition['match_mode'] ?? '') !== 'ancestors_or_equal') {
                continue;
            }
            $ttax = $condition['target_tax'] ?? '';
            if ($ttax && !in_array($ttax, $explicit_compare_taxes, true)) {
                $op                       = $is_chained ? '>' : '>=';
                $post_filters[]           = ['type' => 'tax_level_compare', 'taxonomy' => $ttax, 'operator' => $op];
                $explicit_compare_taxes[] = $ttax;
            }
        }

        // Exclure le post source ET le post WP courant (évite les boucles en mode chaîne)
        $exclude      = [$post->ID];
        $current_post = $this->context->get_post();
        if ($current_post && $current_post->ID !== $post->ID) {
            $exclude[] = $current_post->ID;
        }

        $query = new WP_Query([
            'post_type'              => $target_cpt,
            'post_status'            => 'publish',
            'posts_per_page'         => 10,
            'post__not_in'           => $exclude,
            'tax_query'              => $tax_query,
            'no_found_rows'          => true,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
        ]);

        if (!$query->have_posts()) {
            $this->debug_log[] = sprintf(
                'resolve(%s) depuis #%d "%s" — 0 résultat. tax_query=%s exclude=[%s]',
                $target_cpt,
                $post->ID,
                get_the_title($post->ID),
                wp_json_encode($tax_query),
                implode(',', $exclude)
            );
            return null;
        }

        $candidates = $query->posts;
        $names = array_map(fn($p) => sprintf('#%d "%s"', $p->ID, get_the_title($p->ID)), $candidates);
        $this->debug_log[] = sprintf(
            'resolve(%s) depuis #%d "%s" — %d candidat(s): %s | filters=%s',
            $target_cpt,
            $post->ID,
            get_the_title($post->ID),
            count($candidates),
            implode(', ', $names),
            wp_json_encode($post_filters)
        );

        if (!empty($post_filters)) {
            $level_info = [];
            foreach ($post_filters as $filter) {
                if ($filter['type'] !== 'tax_level_compare') continue;
                $tax = $filter['taxonomy'] ?? '';
                $op  = $filter['operator']  ?? '=';
                if (empty($tax)) continue;
                $cur_terms = get_the_terms($post->ID, $tax);
                if (empty($cur_terms) || is_wp_error($cur_terms)) continue;
                $level_info[] = [
                    'tax'         => $tax,
                    'op'          => $op,
                    'current_val' => $this->get_term_comparison_value(
                        $this->get_deepest_term($cur_terms)
                    ),
                ];
            }

            if (!empty($level_info) || !empty($sort_hints)) {
                usort($candidates, function ($a, $b) use ($level_info, $sort_hints) {
                    foreach ($sort_hints as $hint) {
                        $a_terms = get_the_terms($a->ID, $hint['taxonomy']);
                        $b_terms = get_the_terms($b->ID, $hint['taxonomy']);
                        $a_ids   = (!empty($a_terms) && !is_wp_error($a_terms))
                            ? array_map('intval', wp_list_pluck($a_terms, 'term_id')) : [];
                        $b_ids   = (!empty($b_terms) && !is_wp_error($b_terms))
                            ? array_map('intval', wp_list_pluck($b_terms, 'term_id')) : [];
                        $a_exact = in_array((int) $hint['term_id'], $a_ids, true) ? 1 : 0;
                        $b_exact = in_array((int) $hint['term_id'], $b_ids, true) ? 1 : 0;
                        if ($a_exact !== $b_exact) {
                            return $b_exact - $a_exact;
                        }
                    }
                    foreach ($level_info as $info) {
                        $a_terms = get_the_terms($a->ID, $info['tax']);
                        $b_terms = get_the_terms($b->ID, $info['tax']);
                        $a_val   = (!empty($a_terms) && !is_wp_error($a_terms))
                            ? $this->get_term_comparison_value($this->get_deepest_term($a_terms)) : 0;
                        $b_val   = (!empty($b_terms) && !is_wp_error($b_terms))
                            ? $this->get_term_comparison_value($this->get_deepest_term($b_terms)) : 0;
                        $desc = in_array($info['op'], ['>', '>='], true);
                        $diff = $desc ? ($b_val - $a_val) : ($a_val - $b_val);
                        if ($diff !== 0) return $diff;
                    }
                    return 0;
                });
            }
        }

        $found = null;
        foreach ($candidates as $candidate) {
            $candidate_ok = true;
            foreach ($post_filters as $filter) {
                if ($filter['type'] !== 'tax_level_compare') continue;
                $taxonomy = $filter['taxonomy'] ?? '';
                $operator = $filter['operator'] ?? '=';
                if (empty($taxonomy)) continue;

                $current_terms = get_the_terms($post->ID, $taxonomy);
                $target_terms  = get_the_terms($candidate->ID, $taxonomy);

                if (empty($current_terms) || is_wp_error($current_terms) ||
                    empty($target_terms)   || is_wp_error($target_terms)) {
                    $candidate_ok = false;
                    break;
                }

                $current_val = $this->get_term_comparison_value(
                    $this->get_deepest_term($current_terms)
                );
                $target_val  = $this->get_term_comparison_value(
                    $this->get_deepest_term($target_terms)
                );

                if (!$this->compare_values($current_val, $operator, $target_val)) {
                    $this->debug_log[] = sprintf(
                        '  candidat #%d "%s" REJETÉ: src=%d %s cand=%d (taxo=%s)',
                        $candidate->ID, get_the_title($candidate->ID),
                        $current_val, $operator, $target_val, $taxonomy
                    );
                    $candidate_ok = false;
                    break;
                }
                $this->debug_log[] = sprintf(
                    '  candidat #%d "%s" OK: src=%d %s cand=%d (taxo=%s)',
                    $candidate->ID, get_the_title($candidate->ID),
                    $current_val, $operator, $target_val, $taxonomy
                );
            }
            if ($candidate_ok) {
                $found = $candidate;
                break;
            }
        }

        return $found;
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

    /**
     * Retourne les termes au niveau de profondeur voulu depuis la racine.
     * Niveau 0 = terme racine (top-level), 1 = enfant direct de la racine, etc.
     * Si le terme courant est moins profond que le niveau demandé, retourne le tableau original.
     */
    private function get_terms_at_depth(array $terms, string $taxonomy, int $target_depth): array
    {
        $term = $this->get_deepest_term($terms);

        // ancestors : du plus proche au plus éloigné (parent, grand-parent, …, racine)
        $ancestors = get_ancestors($term->term_id, $taxonomy);
        $current_depth = count($ancestors); // profondeur du terme lui-même (0 = racine)

        if ($target_depth === $current_depth) {
            return [$term];
        }

        if ($target_depth < $current_depth) {
            // L'ancêtre au niveau voulu : dans le tableau ancestors (du plus proche),
            // l'index de l'ancêtre à profondeur $target_depth est ($current_depth - $target_depth - 1)
            $ancestor_index = $current_depth - $target_depth - 1;
            if (isset($ancestors[$ancestor_index])) {
                $ancestor = get_term($ancestors[$ancestor_index], $taxonomy);
                if ($ancestor && !is_wp_error($ancestor)) {
                    return [$ancestor];
                }
            }
        }

        // Niveau demandé plus profond que le terme courant : retourner les termes tels quels
        return $terms;
    }

    /**
     * Retourne la valeur numérique d'un terme pour les comparaisons tax_level_compare.
     *
     * - Taxonomie hiérarchique : profondeur (nb d'ancêtres). "level 2" enfant de "level 1" → 1.
     * - Taxonomie plate avec noms/slugs numériques (ex. "level 3", "niveau-2") :
     *   extrait le premier entier trouvé dans le nom puis le slug.
     * - Sinon : 0.
     */
    private function get_term_comparison_value(\WP_Term $term): int
    {
        $depth = count(get_ancestors($term->term_id, $term->taxonomy));
        if ($depth > 0) {
            return $depth; // taxonomie hiérarchique
        }
        // Taxonomie plate : extraire un entier du nom ou du slug
        foreach ([$term->name, $term->slug] as $str) {
            if (preg_match('/(\d+)/', $str, $m)) {
                return intval($m[1]);
            }
        }
        return 0;
    }

    /**
     * Compare deux entiers avec un opérateur textuel.
     */
    private function compare_values(int $a, string $operator, int $b): bool
    {
        switch ($operator) {
            case '=':  return $a === $b;
            case '>':  return $a > $b;
            case '>=': return $a >= $b;
            case '<':  return $a < $b;
            case '<=': return $a <= $b;
            default:   return false;
        }
    }
}
