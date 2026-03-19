<?php if (! defined('ABSPATH')) exit; ?>

<div class="wrap cb-simple">
    <h1>🧭 Breadcrumb - Fil d'Ariane</h1>

    <div class="cb-hero">
        <h2>Voici comment apparaît votre fil d'Ariane actuellement</h2>
        <p>Cliquez sur n'importe quel élément pour le personnaliser</p>
    </div>

    <!-- SECTION 1 : ARTICLES DE BLOG -->
    <div class="cb-section">
        <div class="cb-section-header">
            <h3>📝 Sur vos articles de blog</h3>
            <span class="cb-live-badge">En direct sur votre site</span>
        </div>

        <div class="cb-preview-box">
            <div class="cb-breadcrumb-live" data-type="post">
                <span class="cb-crumb cb-crumb-home" data-edit="home">
                    <span class="cb-text">Accueil</span>
                    <span class="cb-edit-icon">✏️</span>
                </span>
                <span class="cb-sep">/</span>
                
                <span class="cb-crumb cb-crumb-editable" data-edit="blog-label">
                    <span class="cb-text">Blog</span>
                    <span class="cb-edit-icon">✏️</span>
                </span>
                <span class="cb-sep">/</span>
                
                <span class="cb-crumb cb-crumb-dynamic">
                    <span class="cb-text">Catégorie de l'article</span>
                    <span class="cb-info">🔄 Automatique</span>
                </span>
                <span class="cb-sep">/</span>
                
                <span class="cb-crumb cb-crumb-current">
                    <span class="cb-text">Titre de l'article</span>
                </span>
            </div>

            <div class="cb-explanation">
                <strong>Ce qui s'affiche :</strong>
                <ul>
                    <li><strong>Accueil</strong> : lien vers la page d'accueil</li>
                    <li><strong>Blog</strong> : vous pouvez changer ce texte (ex: "Le Mag", "Actualités"...)</li>
                    <li><strong>Catégorie</strong> : s'adapte automatiquement (IA, Data, Marketing...)</li>
                    <li><strong>Titre</strong> : le titre de l'article en cours</li>
                </ul>
            </div>

            <div class="cb-options">
                <label class="cb-checkbox">
                    <input type="checkbox" checked>
                    <strong>Afficher la catégorie de l'article</strong>
                </label>
                <label class="cb-checkbox">
                    <input type="checkbox">
                    <strong>Afficher aussi les catégories parentes</strong>
                    <span class="cb-hint">Ex: Technologie > IA</span>
                </label>
            </div>
        </div>
    </div>

    <!-- SECTION 2 : PAGES -->
    <div class="cb-section">
        <div class="cb-section-header">
            <h3>📄 Sur vos pages</h3>
            <span class="cb-live-badge">En direct sur votre site</span>
        </div>

        <div class="cb-preview-box">
            <div class="cb-breadcrumb-live" data-type="page">
                <span class="cb-crumb cb-crumb-home">
                    <span class="cb-text">Accueil</span>
                </span>
                <span class="cb-sep">/</span>
                
                <span class="cb-crumb cb-crumb-dynamic">
                    <span class="cb-text">À propos</span>
                    <span class="cb-info">🔄 Page parente</span>
                </span>
                <span class="cb-sep">/</span>
                
                <span class="cb-crumb cb-crumb-dynamic">
                    <span class="cb-text">L'équipe</span>
                    <span class="cb-info">🔄 Page parente</span>
                </span>
                <span class="cb-sep">/</span>
                
                <span class="cb-crumb cb-crumb-current">
                    <span class="cb-text">Contact</span>
                </span>
            </div>

            <div class="cb-explanation">
                <strong>Ce qui s'affiche :</strong>
                <p>WordPress affiche automatiquement toute la hiérarchie des pages parentes jusqu'à la page actuelle.</p>
            </div>

            <div class="cb-options">
                <label class="cb-checkbox">
                    <input type="checkbox" checked>
                    <strong>Afficher toutes les pages parentes</strong>
                </label>
            </div>
        </div>
    </div>

    <!-- SECTION 3 : CUSTOM POST TYPE (exemple Formations) -->
    <div class="cb-section">
        <div class="cb-section-header">
            <h3>🎓 Sur vos formations (ou autre type de contenu personnalisé)</h3>
            <span class="cb-live-badge">En direct sur votre site</span>
        </div>

        <div class="cb-preview-box">
            <div class="cb-breadcrumb-live" data-type="formation">
                <span class="cb-crumb cb-crumb-home">
                    <span class="cb-text">Accueil</span>
                </span>
                <span class="cb-sep">/</span>
                
                <span class="cb-crumb cb-crumb-editable" data-edit="formation-label">
                    <span class="cb-text">Formations</span>
                    <span class="cb-edit-icon">✏️</span>
                </span>
                <span class="cb-sep">/</span>
                
                <span class="cb-crumb cb-crumb-dynamic">
                    <span class="cb-text">Data Analytics</span>
                    <span class="cb-info">🔄 Parcours</span>
                </span>
                <span class="cb-sep">/</span>
                
                <span class="cb-crumb cb-crumb-current">
                    <span class="cb-text">Titre de la formation</span>
                </span>
            </div>

            <div class="cb-explanation">
                <strong>Ce qui s'affiche :</strong>
                <ul>
                    <li><strong>Formations</strong> : vous pouvez changer ce texte</li>
                    <li><strong>Parcours</strong> : s'adapte automatiquement selon la taxonomie "parcours"</li>
                    <li><strong>Titre</strong> : le titre de la formation</li>
                </ul>
            </div>

            <div class="cb-options">
                <label class="cb-select-wrapper">
                    <strong>Afficher la taxonomie :</strong>
                    <select>
                        <option value="parcours">Parcours</option>
                        <option value="none">Aucune</option>
                        <option value="category">Catégorie</option>
                    </select>
                </label>
            </div>
        </div>
    </div>

    <!-- SECTION 4 : RÉGLAGES GLOBAUX -->
    <div class="cb-section cb-section-settings">
        <div class="cb-section-header">
            <h3>⚙️ Réglages généraux</h3>
        </div>

        <div class="cb-settings-simple">
            <div class="cb-setting-row">
                <label>
                    <strong>Texte du lien "Accueil"</strong>
                    <input type="text" class="cb-input" value="Accueil" placeholder="Accueil">
                </label>
            </div>

            <div class="cb-setting-row">
                <label>
                    <strong>Séparateur entre les éléments</strong>
                    <div class="cb-separator-choice">
                        <label><input type="radio" name="sep" value="/" checked> <code>/</code></label>
                        <label><input type="radio" name="sep" value=">"> <code>></code></label>
                        <label><input type="radio" name="sep" value="»"> <code>»</code></label>
                        <label><input type="radio" name="sep" value="→"> <code>→</code></label>
                    </div>
                </label>
            </div>

            <div class="cb-setting-row">
                <label class="cb-checkbox">
                    <input type="checkbox" checked>
                    <strong>Activer les données structurées pour Google (JSON-LD)</strong>
                    <span class="cb-hint">Aide au référencement SEO</span>
                </label>
            </div>
        </div>
    </div>

    <!-- SECTION 5 : COMMENT L'UTILISER -->
    <div class="cb-section cb-section-usage">
        <div class="cb-section-header">
            <h3>💻 Comment afficher le breadcrumb sur votre site</h3>
        </div>

        <div class="cb-usage-grid">
            <div class="cb-usage-card">
                <h4>🔌 Méthode 1 : Insertion automatique</h4>
                <label class="cb-checkbox-big">
                    <input type="checkbox">
                    <strong>Insérer automatiquement avant le contenu</strong>
                </label>
                <p class="cb-hint">Le breadcrumb apparaîtra automatiquement en haut de chaque page</p>
            </div>

            <div class="cb-usage-card">
                <h4>📝 Méthode 2 : Shortcode</h4>
                <p>Copiez ce code dans n'importe quel contenu :</p>
                <div class="cb-code-box">
                    <code>[custom_breadcrumb]</code>
                    <button class="cb-copy-btn" data-copy="[custom_breadcrumb]">📋 Copier</button>
                </div>
            </div>

            <div class="cb-usage-card">
                <h4>🔧 Méthode 3 : Code PHP (pour développeurs)</h4>
                <p>Ajoutez ce code dans votre thème :</p>
                <div class="cb-code-box">
                    <code>&lt;?php custom_breadcrumb(); ?&gt;</code>
                    <button class="cb-copy-btn" data-copy="<?php custom_breadcrumb(); ?>">📋 Copier</button>
                </div>
                <p class="cb-hint">À placer dans header.php, single.php, page.php, etc.</p>
            </div>
        </div>
    </div>

    <!-- BOUTON ENREGISTRER -->
    <div class="cb-save-section">
        <button class="button button-primary button-hero cb-save-btn">
            💾 Enregistrer les modifications
        </button>
        <p class="cb-hint">Les changements seront appliqués immédiatement sur votre site</p>
    </div>

    <!-- FOOTER -->
    <div class="cb-footer">
        <p>Plugin développé par <a href="https://www.webanalyste.com" target="_blank"><strong>webAnalyste</strong></a> - Agence data, IA & automatisation</p>
        <p><a href="https://www.formations-analytics.com" target="_blank">Formations Analytics</a> - Organisme de formation spécialisé</p>
    </div>
</div>

<!-- MODAL D'ÉDITION -->
<div class="cb-modal" id="cb-edit-modal">
    <div class="cb-modal-content">
        <div class="cb-modal-header">
            <h3>✏️ Modifier le texte</h3>
            <button class="cb-modal-close">&times;</button>
        </div>
        <div class="cb-modal-body">
            <label>
                <strong>Nouveau texte :</strong>
                <input type="text" class="cb-modal-input" placeholder="Entrez le nouveau texte...">
            </label>
        </div>
        <div class="cb-modal-footer">
            <button class="button button-primary cb-modal-save">Valider</button>
            <button class="button cb-modal-cancel">Annuler</button>
        </div>
    </div>
</div>
