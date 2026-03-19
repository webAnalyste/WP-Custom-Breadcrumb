<?php
if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap cb-wrap">
    <h1>🧭 Custom Breadcrumb</h1>
    <p class="cb-intro">Personnalisez le fil d'Ariane affiché sur votre site selon le type de contenu.</p>

    <div class="cb-alert cb-alert--info">
        <strong>💡 Comment ça marche ?</strong><br>
        Choisissez comment afficher le fil d'Ariane pour chaque type de page : articles de blog, formations, pages, produits, etc.
    </div>

    <div class="cb-tabs">
        <button class="cb-tab cb-tab--active" data-tab="examples">📋 Exemples concrets</button>
        <button class="cb-tab" data-tab="configure">⚙️ Personnaliser</button>
        <button class="cb-tab" data-tab="settings">🎨 Réglages</button>
    </div>

    <div class="cb-tab-content cb-tab-content--active" id="tab-examples">
        <h2>Voici ce que vous pouvez personnaliser</h2>

        <div class="cb-example-grid">
            <div class="cb-example-card">
                <div class="cb-example-header">
                    <span class="cb-icon">📝</span>
                    <h3>Articles de blog</h3>
                </div>
                <div class="cb-example-body">
                    <div class="cb-before-after">
                        <div class="cb-before">
                            <strong>❌ Par défaut</strong>
                            <div class="cb-breadcrumb-demo">
                                Accueil > Blog > Article
                            </div>
                        </div>
                        <div class="cb-arrow">→</div>
                        <div class="cb-after">
                            <strong>✅ Personnalisé</strong>
                            <div class="cb-breadcrumb-demo cb-breadcrumb-demo--custom">
                                Accueil > Le Mag > IA > Mon article sur ChatGPT
                            </div>
                        </div>
                    </div>
                    <p class="cb-explanation">
                        Affichez la catégorie de l'article dans le fil d'Ariane au lieu d'un simple "Blog"
                    </p>
                    <button class="button button-primary">Personnaliser les articles</button>
                </div>
            </div>

            <div class="cb-example-card">
                <div class="cb-example-header">
                    <span class="cb-icon">🎓</span>
                    <h3>Formations</h3>
                </div>
                <div class="cb-example-body">
                    <div class="cb-before-after">
                        <div class="cb-before">
                            <strong>❌ Par défaut</strong>
                            <div class="cb-breadcrumb-demo">
                                Accueil > Formations > Ma formation
                            </div>
                        </div>
                        <div class="cb-arrow">→</div>
                        <div class="cb-after">
                            <strong>✅ Personnalisé</strong>
                            <div class="cb-breadcrumb-demo cb-breadcrumb-demo--custom">
                                Accueil > Formations > Data Analytics > Ma formation
                            </div>
                        </div>
                    </div>
                    <p class="cb-explanation">
                        Ajoutez le parcours de formation dans le fil d'Ariane
                    </p>
                    <button class="button button-primary">Personnaliser les formations</button>
                </div>
            </div>

            <div class="cb-example-card">
                <div class="cb-example-header">
                    <span class="cb-icon">📄</span>
                    <h3>Pages</h3>
                </div>
                <div class="cb-example-body">
                    <div class="cb-before-after">
                        <div class="cb-before">
                            <strong>❌ Par défaut</strong>
                            <div class="cb-breadcrumb-demo">
                                Accueil > Contact
                            </div>
                        </div>
                        <div class="cb-arrow">→</div>
                        <div class="cb-after">
                            <strong>✅ Personnalisé</strong>
                            <div class="cb-breadcrumb-demo cb-breadcrumb-demo--custom">
                                Accueil > À propos > L'équipe > Contact
                            </div>
                        </div>
                    </div>
                    <p class="cb-explanation">
                        Affichez la hiérarchie complète des pages parentes
                    </p>
                    <button class="button button-primary">Personnaliser les pages</button>
                </div>
            </div>

            <div class="cb-example-card">
                <div class="cb-example-header">
                    <span class="cb-icon">📍</span>
                    <h3>Agences locales</h3>
                </div>
                <div class="cb-example-body">
                    <div class="cb-before-after">
                        <div class="cb-before">
                            <strong>❌ Par défaut</strong>
                            <div class="cb-breadcrumb-demo">
                                Accueil > Agence Brive
                            </div>
                        </div>
                        <div class="cb-arrow">→</div>
                        <div class="cb-after">
                            <strong>✅ Personnalisé</strong>
                            <div class="cb-breadcrumb-demo cb-breadcrumb-demo--custom">
                                Accueil > Nos agences > Nouvelle-Aquitaine > Brive-la-Gaillarde
                            </div>
                        </div>
                    </div>
                    <p class="cb-explanation">
                        Utilisez des informations personnalisées (région, ville) dans le fil d'Ariane
                    </p>
                    <button class="button button-primary">Personnaliser les agences</button>
                </div>
            </div>

            <div class="cb-example-card">
                <div class="cb-example-header">
                    <span class="cb-icon">🛍️</span>
                    <h3>Produits WooCommerce</h3>
                </div>
                <div class="cb-example-body">
                    <div class="cb-before-after">
                        <div class="cb-before">
                            <strong>❌ Par défaut</strong>
                            <div class="cb-breadcrumb-demo">
                                Accueil > Boutique > Produit
                            </div>
                        </div>
                        <div class="cb-arrow">→</div>
                        <div class="cb-after">
                            <strong>✅ Personnalisé</strong>
                            <div class="cb-breadcrumb-demo cb-breadcrumb-demo--custom">
                                Accueil > Boutique > Électronique > Ordinateurs > Produit
                            </div>
                        </div>
                    </div>
                    <p class="cb-explanation">
                        Affichez les catégories de produits dans le fil d'Ariane
                    </p>
                    <button class="button button-primary">Personnaliser les produits</button>
                </div>
            </div>

            <div class="cb-example-card">
                <div class="cb-example-header">
                    <span class="cb-icon">🏷️</span>
                    <h3>Archives de catégories</h3>
                </div>
                <div class="cb-example-body">
                    <div class="cb-before-after">
                        <div class="cb-before">
                            <strong>❌ Par défaut</strong>
                            <div class="cb-breadcrumb-demo">
                                Accueil > IA
                            </div>
                        </div>
                        <div class="cb-arrow">→</div>
                        <div class="cb-after">
                            <strong>✅ Personnalisé</strong>
                            <div class="cb-breadcrumb-demo cb-breadcrumb-demo--custom">
                                Accueil > Le Mag > Technologie > IA
                            </div>
                        </div>
                    </div>
                    <p class="cb-explanation">
                        Affichez la hiérarchie complète des catégories parentes
                    </p>
                    <button class="button button-primary">Personnaliser les catégories</button>
                </div>
            </div>
        </div>
    </div>

    <div class="cb-tab-content" id="tab-configure">
        <h2>Personnaliser vos fils d'Ariane</h2>

        <div class="cb-config-section">
            <div class="cb-config-header">
                <h3>📝 Articles de blog</h3>
                <span class="cb-status cb-status--active">Actif</span>
            </div>
            <div class="cb-config-body">
                <div class="cb-visual-builder">
                    <div class="cb-breadcrumb-preview">
                        <strong>Aperçu :</strong>
                        <div class="cb-breadcrumb-live">
                            <span class="cb-crumb">Accueil</span>
                            <span class="cb-sep">/</span>
                            <span class="cb-crumb cb-crumb--editable">Le Mag</span>
                            <span class="cb-sep">/</span>
                            <span class="cb-crumb cb-crumb--dynamic">[Catégorie]</span>
                            <span class="cb-sep">/</span>
                            <span class="cb-crumb cb-crumb--current">[Titre de l'article]</span>
                        </div>
                    </div>

                    <div class="cb-builder-options">
                        <label class="cb-option">
                            <input type="checkbox" checked>
                            <strong>Afficher la catégorie de l'article</strong>
                            <p>Exemple : "IA", "Data", "Marketing"...</p>
                        </label>

                        <label class="cb-option">
                            <input type="text" class="cb-input" value="Le Mag" placeholder="Nom personnalisé">
                            <strong>Nom de la section blog</strong>
                            <p>Au lieu de "Blog" ou "Actualités"</p>
                        </label>

                        <label class="cb-option">
                            <input type="checkbox">
                            <strong>Afficher les catégories parentes</strong>
                            <p>Si votre catégorie a des parents : Technologie > IA</p>
                        </label>
                    </div>

                    <button class="button button-primary button-large">Enregistrer</button>
                </div>
            </div>
        </div>

        <div class="cb-config-section">
            <div class="cb-config-header">
                <h3>🎓 Formations (CPT)</h3>
                <span class="cb-status cb-status--inactive">Inactif</span>
            </div>
            <div class="cb-config-body cb-config-body--collapsed">
                <p>Cliquez pour personnaliser le fil d'Ariane des formations</p>
                <button class="button">Configurer</button>
            </div>
        </div>

        <div class="cb-config-section">
            <div class="cb-config-header">
                <h3>📄 Pages</h3>
                <span class="cb-status cb-status--active">Actif</span>
            </div>
            <div class="cb-config-body cb-config-body--collapsed">
                <p>Affiche automatiquement la hiérarchie des pages parentes</p>
                <button class="button">Modifier</button>
            </div>
        </div>

        <div class="cb-add-new">
            <button class="button button-secondary button-large">
                <span class="dashicons dashicons-plus-alt"></span>
                Ajouter un type de contenu personnalisé
            </button>
        </div>
    </div>

    <div class="cb-tab-content" id="tab-settings">
        <h2>Réglages généraux</h2>

        <div class="cb-settings-grid">
            <div class="cb-setting-card">
                <h3>🏠 Page d'accueil</h3>
                <label>
                    <strong>Texte du lien "Accueil"</strong>
                    <input type="text" class="cb-input" value="Accueil" placeholder="Accueil">
                </label>
            </div>

            <div class="cb-setting-card">
                <h3>➡️ Séparateur</h3>
                <div class="cb-separator-options">
                    <label><input type="radio" name="sep" value="/" checked> <code>/</code></label>
                    <label><input type="radio" name="sep" value=">"> <code>></code></label>
                    <label><input type="radio" name="sep" value="»"> <code>»</code></label>
                    <label><input type="radio" name="sep" value="→"> <code>→</code></label>
                    <label><input type="radio" name="sep" value="custom"> Personnalisé : <input type="text" class="cb-input cb-input--small" placeholder="|"></label>
                </div>
            </div>

            <div class="cb-setting-card">
                <h3>🔍 SEO</h3>
                <label class="cb-checkbox">
                    <input type="checkbox" checked>
                    <strong>Activer les données structurées (JSON-LD)</strong>
                    <p>Aide Google à comprendre la structure de votre site</p>
                </label>
            </div>

            <div class="cb-setting-card">
                <h3>📱 Affichage</h3>
                <label class="cb-checkbox">
                    <input type="checkbox">
                    <strong>Insertion automatique</strong>
                    <p>Ajoute automatiquement le fil d'Ariane avant le contenu</p>
                </label>
                <div class="cb-info-box">
                    <strong>💡 Utilisation manuelle</strong>
                    <p>Shortcode : <code>[custom_breadcrumb]</code></p>
                    <p>PHP : <code>&lt;?php custom_breadcrumb(); ?&gt;</code></p>
                </div>
            </div>
        </div>

        <button class="button button-primary button-large">Enregistrer les réglages</button>
    </div>

    <div class="cb-footer">
        <p>
            Plugin développé par <a href="https://www.webanalyste.com" target="_blank"><strong>webAnalyste</strong></a> - 
            Agence experte en data, IA et automatisation no-code
        </p>
        <p>
            <a href="https://www.formations-analytics.com" target="_blank">Formations Analytics</a> - 
            Organisme de formation spécialisé
        </p>
    </div>
</div>
