<?php
/**
 * Détection du contexte WordPress
 */

if (! defined('ABSPATH')) {
    exit;
}

class Custom_Breadcrumb_Context
{
    private string $type;
    private ?WP_Post $post = null;
    private ?WP_Term $term = null;
    private ?WP_Post_Type $post_type_obj = null;

    public function __construct()
    {
        $this->detect_context();
    }

    private function detect_context(): void
    {
        if (is_front_page()) {
            $this->type = 'front_page';
        } elseif (is_home()) {
            $this->type = 'blog_home';
        } elseif (is_singular()) {
            $this->type = 'singular';
            $this->post = get_queried_object();
        } elseif (is_category() || is_tag() || is_tax()) {
            $this->type = 'taxonomy';
            $this->term = get_queried_object();
        } elseif (is_post_type_archive()) {
            $this->type = 'post_type_archive';
            $this->post_type_obj = get_queried_object();
        } elseif (is_author()) {
            $this->type = 'author';
        } elseif (is_search()) {
            $this->type = 'search';
        } elseif (is_404()) {
            $this->type = '404';
        } else {
            $this->type = 'unknown';
        }
    }

    public function get_type(): string
    {
        return $this->type;
    }

    public function get_post(): ?WP_Post
    {
        return $this->post;
    }

    public function get_post_type(): string
    {
        if ($this->post) {
            return $this->post->post_type;
        }
        
        if ($this->post_type_obj) {
            return $this->post_type_obj->name;
        }

        return 'post';
    }

    public function get_term(): ?WP_Term
    {
        return $this->term;
    }

    public function is_singular(): bool
    {
        return $this->type === 'singular';
    }

    public function is_taxonomy(): bool
    {
        return $this->type === 'taxonomy';
    }

    public function is_archive(): bool
    {
        return in_array($this->type, ['post_type_archive', 'taxonomy', 'author'], true);
    }
}
