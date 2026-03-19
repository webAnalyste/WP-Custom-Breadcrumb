<?php
if (! defined('ABSPATH')) exit;

$post_types = get_post_types(['public' => true], 'objects');
$taxonomies = get_taxonomies(['public' => true], 'objects');
$pages = get_pages(['number' => 100]);
?>

<div class="wrap cb-advanced">
    <h1>🧭 Custom Breadcrumb - Configuration avancée</h1>

    <div class="cb-tabs">
        <button class="cb-tab active" data-tab="rules">📋 Règles de breadcrumb</button>
        <button class="cb-tab" data-tab="global">⚙️ Réglages globaux</button>
        <button class="cb-tab" data-tab="preview">👁️ Aperçu</button>
        <button class="cb-tab" data-tab="code">💻 Intégration</button>
    </div>

    <!-- ONGLET RÈGLES -->
    <div class="cb-tab-content active" data-content="rules">
        <div class="cb-rules-header">
            <h2>Règles de personnalisation du breadcrumb</h2>
            <button class="button button-primary cb-add-rule">➕ Nouvelle règle</button>
        </div>

        <div class="cb-rules-list" id="rules-list">
            <!-- Les règles seront ajoutées ici dynamiquement -->
        </div>

        <div class="cb-no-rules" style="display: none;">
            <p>Aucune règle configurée. Créez votre première règle pour personnaliser vos breadcrumbs.</p>
        </div>
    </div>

    <!-- ONGLET RÉGLAGES GLOBAUX -->
    <div class="cb-tab-content" data-content="global">
        <div class="cb-settings-section">
            <h2>⚙️ Réglages globaux</h2>

            <table class="form-table">
                <tr>
                    <th>Texte "Accueil"</th>
                    <td>
                        <input type="text" class="regular-text" id="home-label" value="<?php echo esc_attr($settings['global']['home_label'] ?? 'Accueil'); ?>">
                        <p class="description">Texte affiché pour le lien vers la page d'accueil</p>
                    </td>
                </tr>

                <tr>
                    <th>Séparateur</th>
                    <td>
                        <select id="separator">
                            <option value="/" <?php selected($settings['global']['separator'] ?? '/', '/'); ?>>/</option>
                            <option value=">" <?php selected($settings['global']['separator'] ?? '/', '>'); ?>>></option>
                            <option value="»" <?php selected($settings['global']['separator'] ?? '/', '»'); ?>>»</option>
                            <option value="→" <?php selected($settings['global']['separator'] ?? '/', '→'); ?>>→</option>
                            <option value="|" <?php selected($settings['global']['separator'] ?? '/', '|'); ?>>|</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>JSON-LD (SEO)</th>
                    <td>
                        <label>
                            <input type="checkbox" id="enable-jsonld" <?php checked($settings['global']['enable_jsonld'] ?? true); ?>>
                            Activer les données structurées Schema.org
                        </label>
                        <p class="description">Améliore le référencement en fournissant des données structurées à Google</p>
                    </td>
                </tr>

                <tr>
                    <th>Insertion automatique</th>
                    <td>
                        <label>
                            <input type="checkbox" id="auto-insert" <?php checked($settings['global']['auto_insert'] ?? false); ?>>
                            Insérer automatiquement le breadcrumb avant le contenu
                        </label>
                        <p class="description">Le breadcrumb sera ajouté automatiquement en haut de chaque page</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ONGLET APERÇU -->
    <div class="cb-tab-content" data-content="preview">
        <div class="cb-preview-section">
            <h2>👁️ Aperçu du breadcrumb</h2>
            
            <div class="cb-preview-selector">
                <label>
                    <strong>Tester sur :</strong>
                    <select id="preview-context">
                        <option value="post">Un article</option>
                        <option value="page">Une page</option>
                        <?php foreach ($post_types as $pt): ?>
                            <?php if (!in_array($pt->name, ['post', 'page', 'attachment'])): ?>
                                <option value="<?php echo esc_attr($pt->name); ?>"><?php echo esc_html($pt->labels->singular_name); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <option value="category">Archive catégorie</option>
                        <option value="tag">Archive étiquette</option>
                    </select>
                </label>
            </div>

            <div class="cb-preview-box">
                <div id="breadcrumb-preview" class="custom-breadcrumb">
                    <ol class="custom-breadcrumb__list">
                        <li class="custom-breadcrumb__item">
                            <a href="#">Accueil</a>
                            <span class="custom-breadcrumb__separator">/</span>
                        </li>
                        <li class="custom-breadcrumb__item custom-breadcrumb__item--current">
                            <span>Page actuelle</span>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="cb-preview-code">
                <h3>Code HTML généré :</h3>
                <pre id="html-preview"><code></code></pre>
            </div>
        </div>
    </div>

    <!-- ONGLET INTÉGRATION -->
    <div class="cb-tab-content" data-content="code">
        <div class="cb-code-section">
            <h2>💻 Comment utiliser le breadcrumb</h2>

            <div class="cb-code-methods">
                <div class="cb-code-method">
                    <h3>📝 Méthode 1 : Shortcode</h3>
                    <p>Ajoutez ce shortcode dans n'importe quel contenu (article, page, widget...) :</p>
                    <div class="cb-code-box">
                        <code>[custom_breadcrumb]</code>
                        <button class="button cb-copy" data-copy="[custom_breadcrumb]">📋 Copier</button>
                    </div>
                </div>

                <div class="cb-code-method">
                    <h3>🔧 Méthode 2 : Code PHP</h3>
                    <p>Ajoutez ce code dans vos fichiers de thème (header.php, single.php, page.php...) :</p>
                    <div class="cb-code-box">
                        <code>&lt;?php custom_breadcrumb(); ?&gt;</code>
                        <button class="button cb-copy" data-copy="<?php custom_breadcrumb(); ?>">📋 Copier</button>
                    </div>
                </div>

                <div class="cb-code-method">
                    <h3>🎨 Méthode 3 : Personnalisation CSS</h3>
                    <p>Classes CSS disponibles pour personnaliser l'apparence :</p>
                    <div class="cb-code-box">
<code>.custom-breadcrumb { }
.custom-breadcrumb__list { }
.custom-breadcrumb__item { }
.custom-breadcrumb__item a { }
.custom-breadcrumb__item--current { }
.custom-breadcrumb__separator { }</code>
                        <button class="button cb-copy" data-copy=".custom-breadcrumb { }\n.custom-breadcrumb__list { }\n.custom-breadcrumb__item { }\n.custom-breadcrumb__item a { }\n.custom-breadcrumb__item--current { }\n.custom-breadcrumb__separator { }">📋 Copier</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BOUTON ENREGISTRER -->
    <div class="cb-save-bar">
        <button class="button button-primary button-large" id="save-settings">
            💾 Enregistrer toutes les modifications
        </button>
        <span class="cb-save-status"></span>
    </div>
</div>

<!-- MODAL CRÉATION/ÉDITION RÈGLE -->
<div class="cb-modal" id="rule-modal">
    <div class="cb-modal-overlay"></div>
    <div class="cb-modal-container">
        <div class="cb-modal-header">
            <h2 id="modal-title">Nouvelle règle de breadcrumb</h2>
            <button class="cb-modal-close">&times;</button>
        </div>

        <div class="cb-modal-body">
            <div class="cb-rule-form">
                <!-- SECTION 1 : CONTEXTE -->
                <div class="cb-form-section">
                    <h3>1️⃣ Quand appliquer cette règle ?</h3>
                    
                    <div class="cb-field">
                        <label>
                            <strong>Type de contenu</strong>
                            <select id="rule-post-type" class="widefat">
                                <option value="post">Articles (post)</option>
                                <option value="page">Pages</option>
                                <?php foreach ($post_types as $pt): ?>
                                    <?php if (!in_array($pt->name, ['post', 'page', 'attachment'])): ?>
                                        <option value="<?php echo esc_attr($pt->name); ?>"><?php echo esc_html($pt->labels->name); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <option value="category">Archives catégories</option>
                                <option value="tag">Archives étiquettes</option>
                                <?php foreach ($taxonomies as $tax): ?>
                                    <?php if (!in_array($tax->name, ['category', 'post_tag', 'post_format'])): ?>
                                        <option value="tax_<?php echo esc_attr($tax->name); ?>">Archives <?php echo esc_html($tax->labels->name); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>

                    <div class="cb-field">
                        <label>
                            <input type="checkbox" id="rule-enabled">
                            <strong>Règle activée</strong>
                        </label>
                    </div>
                </div>

                <!-- SECTION 2 : STRUCTURE DU BREADCRUMB -->
                <div class="cb-form-section">
                    <h3>2️⃣ Structure du breadcrumb</h3>
                    
                    <div class="cb-breadcrumb-builder">
                        <div class="cb-builder-preview">
                            <div class="cb-crumb cb-crumb-home">
                                <span>🏠 Accueil</span>
                            </div>
                            <span class="cb-sep">/</span>
                            <div id="segments-container">
                                <!-- Les segments seront ajoutés ici -->
                            </div>
                            <div class="cb-crumb cb-crumb-current">
                                <span>Page actuelle</span>
                            </div>
                        </div>

                        <button type="button" class="button" id="add-segment">➕ Ajouter un segment</button>
                    </div>
                </div>

                <!-- SECTION 3 : OPTIONS -->
                <div class="cb-form-section">
                    <h3>3️⃣ Options avancées</h3>
                    
                    <div class="cb-field">
                        <label>
                            <input type="checkbox" id="rule-show-parents">
                            <strong>Afficher les parents hiérarchiques</strong>
                        </label>
                        <p class="description">Pour les pages et taxonomies avec hiérarchie</p>
                    </div>

                    <div class="cb-field">
                        <label>
                            <input type="checkbox" id="rule-show-taxonomy">
                            <strong>Afficher la taxonomie principale</strong>
                        </label>
                        <p class="description">Catégorie, étiquette ou taxonomie personnalisée</p>
                    </div>

                    <div class="cb-field" id="taxonomy-selector" style="display:none;">
                        <label>
                            <strong>Quelle taxonomie ?</strong>
                            <select id="rule-taxonomy" class="widefat">
                                <option value="category">Catégorie</option>
                                <option value="post_tag">Étiquette</option>
                                <?php foreach ($taxonomies as $tax): ?>
                                    <?php if (!in_array($tax->name, ['category', 'post_tag', 'post_format'])): ?>
                                        <option value="<?php echo esc_attr($tax->name); ?>"><?php echo esc_html($tax->labels->singular_name); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="cb-modal-footer">
            <button class="button button-primary button-large" id="save-rule">💾 Enregistrer la règle</button>
            <button class="button button-large cb-modal-close">Annuler</button>
        </div>
    </div>
</div>

<!-- TEMPLATE SEGMENT -->
<template id="segment-template">
    <div class="cb-segment" data-segment-id="">
        <div class="cb-segment-content">
            <div class="cb-segment-type">
                <select class="segment-type">
                    <option value="text">📝 Texte fixe</option>
                    <option value="page">📄 Page WordPress</option>
                    <option value="archive">📚 Archive</option>
                    <option value="taxonomy">🏷️ Taxonomie</option>
                    <option value="custom">🔧 Personnalisé</option>
                </select>
            </div>

            <div class="cb-segment-value">
                <!-- Le contenu change selon le type -->
                <input type="text" class="segment-text widefat" placeholder="Texte à afficher...">

                <select class="segment-page widefat" style="display:none;">
                    <option value="">Sélectionner une page...</option>
                    <?php foreach ($pages as $page): ?>
                        <option value="<?php echo esc_attr($page->ID); ?>"><?php echo esc_html($page->post_title); ?></option>
                    <?php endforeach; ?>
                </select>

                <select class="segment-archive widefat" style="display:none;">
                    <option value="">Sélectionner une archive...</option>
                    <option value="blog">Blog</option>
                    <?php foreach ($post_types as $pt): ?>
                        <?php if ($pt->has_archive && !in_array($pt->name, ['post', 'attachment'])): ?>
                            <option value="<?php echo esc_attr($pt->name); ?>"><?php echo esc_html($pt->labels->name); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>

                <select class="segment-taxonomy widefat" style="display:none;">
                    <option value="">Sélectionner une taxonomie...</option>
                    <?php foreach ($taxonomies as $tax): ?>
                        <option value="<?php echo esc_attr($tax->name); ?>"><?php echo esc_html($tax->labels->singular_name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="cb-segment-label">
                <input type="text" class="segment-label widefat" placeholder="Label personnalisé (optionnel — remplace le texte auto)">
            </div>

            <div class="cb-segment-actions">
                <button type="button" class="button segment-up" title="Monter">↑</button>
                <button type="button" class="button segment-down" title="Descendre">↓</button>
                <button type="button" class="button segment-delete" title="Supprimer">🗑️</button>
            </div>
        </div>
        <span class="cb-sep">/</span>
    </div>
</template>
