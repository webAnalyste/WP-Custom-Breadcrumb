<?php
if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap cdc-breadcrumbs-prototype">
    <div class="cdc-header">
        <h1>Créer une nouvelle règle</h1>
        <a href="?page=cdc-breadcrumbs-rules" class="button">Retour aux règles</a>
    </div>

    <div class="cdc-alert cdc-alert--info">
        <strong>Aide :</strong> Une règle définit comment le breadcrumb doit être construit pour un contexte spécifique (post, page, CPT, taxonomie, etc.).
    </div>

    <form class="cdc-form">
        <div class="cdc-card">
            <div class="cdc-card__header">
                <h2>Informations générales</h2>
            </div>
            <div class="cdc-card__body">
                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Nom de la règle <span class="required">*</span>
                        <input type="text" class="cdc-input" placeholder="Ex: Articles de blog" required>
                    </label>
                    <p class="description">Nom interne pour identifier cette règle dans l'administration.</p>
                </div>

                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Description (optionnelle)
                        <textarea class="cdc-textarea" rows="3" placeholder="Description de la règle et de son usage..."></textarea>
                    </label>
                </div>

                <div class="cdc-form-row cdc-form-row--inline">
                    <label class="cdc-label">
                        <input type="checkbox" checked>
                        Règle active
                    </label>
                    <p class="description">Décochez pour désactiver temporairement cette règle sans la supprimer.</p>
                </div>

                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Priorité <span class="required">*</span>
                        <input type="number" class="cdc-input cdc-input--small" value="100" min="0" max="999">
                    </label>
                    <p class="description">Plus la priorité est élevée, plus la règle sera appliquée en premier. Valeur entre 0 et 999.</p>
                </div>
            </div>
        </div>

        <div class="cdc-card">
            <div class="cdc-card__header">
                <h2>Contexte d'application</h2>
            </div>
            <div class="cdc-card__body">
                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Type de contexte <span class="required">*</span>
                        <select class="cdc-select" required>
                            <option value="">Sélectionner un type...</option>
                            <option value="post">Post (article de blog)</option>
                            <option value="page">Page</option>
                            <option value="cpt">Custom Post Type</option>
                            <option value="archive">Archive</option>
                            <option value="taxonomy">Taxonomie</option>
                            <option value="author">Auteur</option>
                            <option value="search">Recherche</option>
                            <option value="404">Page 404</option>
                            <option value="home">Page d'accueil</option>
                        </select>
                    </label>
                </div>

                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Post Type (si applicable)
                        <select class="cdc-select">
                            <option value="">Tous les post types</option>
                            <option value="post">Post</option>
                            <option value="page">Page</option>
                            <option value="formations">Formations</option>
                            <option value="agence_locale">Agence locale</option>
                        </select>
                    </label>
                </div>

                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Taxonomie (si applicable)
                        <select class="cdc-select">
                            <option value="">Aucune taxonomie spécifique</option>
                            <option value="category">Catégorie</option>
                            <option value="post_tag">Étiquette</option>
                            <option value="parcours">Parcours</option>
                        </select>
                    </label>
                </div>

                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Conditions supplémentaires
                        <div class="cdc-conditions">
                            <div class="cdc-condition">
                                <select class="cdc-select">
                                    <option>Sélectionner une condition...</option>
                                    <option>Template de page</option>
                                    <option>Métadonnée existe</option>
                                    <option>Métadonnée = valeur</option>
                                    <option>Slug spécifique</option>
                                    <option>ID spécifique</option>
                                </select>
                                <input type="text" class="cdc-input" placeholder="Valeur...">
                                <button type="button" class="button button-small">Supprimer</button>
                            </div>
                        </div>
                        <button type="button" class="button button-small">
                            <span class="dashicons dashicons-plus-alt"></span>
                            Ajouter une condition
                        </button>
                    </label>
                </div>
            </div>
        </div>

        <div class="cdc-card">
            <div class="cdc-card__header">
                <h2>Structure du breadcrumb</h2>
            </div>
            <div class="cdc-card__body">
                <div class="cdc-breadcrumb-builder">
                    <div class="cdc-breadcrumb-item">
                        <div class="cdc-breadcrumb-item__header">
                            <span class="cdc-breadcrumb-item__number">1</span>
                            <strong>Accueil</strong>
                            <button type="button" class="button button-small">Supprimer</button>
                        </div>
                        <div class="cdc-breadcrumb-item__body">
                            <label class="cdc-label">
                                Type d'élément
                                <select class="cdc-select">
                                    <option value="home">Accueil</option>
                                    <option value="fixed">Lien fixe</option>
                                    <option value="archive">Archive CPT</option>
                                    <option value="page">Page spécifique</option>
                                    <option value="parent">Parent natif</option>
                                    <option value="meta_parent">Parent forcé (meta)</option>
                                    <option value="taxonomy">Terme de taxonomie</option>
                                    <option value="current">Titre courant</option>
                                </select>
                            </label>
                            <label class="cdc-label">
                                Libellé
                                <input type="text" class="cdc-input" value="{root_label}" placeholder="Libellé...">
                            </label>
                        </div>
                    </div>

                    <div class="cdc-breadcrumb-item">
                        <div class="cdc-breadcrumb-item__header">
                            <span class="cdc-breadcrumb-item__number">2</span>
                            <strong>Le Mag</strong>
                            <button type="button" class="button button-small">Supprimer</button>
                        </div>
                        <div class="cdc-breadcrumb-item__body">
                            <label class="cdc-label">
                                Type d'élément
                                <select class="cdc-select">
                                    <option value="fixed">Lien fixe</option>
                                    <option value="home">Accueil</option>
                                    <option value="archive">Archive CPT</option>
                                    <option value="page">Page spécifique</option>
                                </select>
                            </label>
                            <label class="cdc-label">
                                Libellé
                                <input type="text" class="cdc-input" value="Le Mag">
                            </label>
                            <label class="cdc-label">
                                URL
                                <input type="text" class="cdc-input" value="/le-mag/">
                            </label>
                        </div>
                    </div>

                    <div class="cdc-breadcrumb-item">
                        <div class="cdc-breadcrumb-item__header">
                            <span class="cdc-breadcrumb-item__number">3</span>
                            <strong>Catégorie</strong>
                            <button type="button" class="button button-small">Supprimer</button>
                        </div>
                        <div class="cdc-breadcrumb-item__body">
                            <label class="cdc-label">
                                Type d'élément
                                <select class="cdc-select">
                                    <option value="taxonomy">Terme de taxonomie</option>
                                    <option value="fixed">Lien fixe</option>
                                    <option value="current">Titre courant</option>
                                </select>
                            </label>
                            <label class="cdc-label">
                                Taxonomie
                                <select class="cdc-select">
                                    <option value="category">Catégorie</option>
                                    <option value="parcours">Parcours</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="cdc-breadcrumb-item">
                        <div class="cdc-breadcrumb-item__header">
                            <span class="cdc-breadcrumb-item__number">4</span>
                            <strong>Titre de l'article</strong>
                            <button type="button" class="button button-small">Supprimer</button>
                        </div>
                        <div class="cdc-breadcrumb-item__body">
                            <label class="cdc-label">
                                Type d'élément
                                <select class="cdc-select">
                                    <option value="current">Titre courant</option>
                                    <option value="fixed">Lien fixe</option>
                                </select>
                            </label>
                        </div>
                    </div>
                </div>

                <button type="button" class="button">
                    <span class="dashicons dashicons-plus-alt"></span>
                    Ajouter un élément
                </button>

                <div class="cdc-placeholders">
                    <h4>Placeholders disponibles</h4>
                    <div class="cdc-placeholders__grid">
                        <code>{site_name}</code>
                        <code>{post_title}</code>
                        <code>{post_type_label}</code>
                        <code>{taxonomy:nom}</code>
                        <code>{meta:nom}</code>
                        <code>{archive_label}</code>
                        <code>{term_name}</code>
                        <code>{author_name}</code>
                        <code>{search_query}</code>
                    </div>
                </div>
            </div>
        </div>

        <div class="cdc-card">
            <div class="cdc-card__header">
                <h2>Options d'affichage</h2>
            </div>
            <div class="cdc-card__body">
                <div class="cdc-form-row cdc-form-row--inline">
                    <label class="cdc-label">
                        <input type="checkbox" checked>
                        Activer le rendu HTML
                    </label>
                </div>

                <div class="cdc-form-row cdc-form-row--inline">
                    <label class="cdc-label">
                        <input type="checkbox" checked>
                        Activer le JSON-LD (BreadcrumbList)
                    </label>
                </div>

                <div class="cdc-form-row cdc-form-row--inline">
                    <label class="cdc-label">
                        <input type="checkbox">
                        Masquer l'élément courant
                    </label>
                </div>
            </div>
        </div>

        <div class="cdc-card cdc-card--preview">
            <div class="cdc-card__header">
                <h2>Prévisualisation</h2>
            </div>
            <div class="cdc-card__body">
                <div class="cdc-preview">
                    <h4>Rendu HTML</h4>
                    <div class="cdc-preview__html">
                        <nav class="cdc-wpcb" aria-label="Fil d'Ariane">
                            <ol class="cdc-wpcb__list">
                                <li class="cdc-wpcb__item"><a href="/">Accueil</a><span class="cdc-wpcb__separator">/</span></li>
                                <li class="cdc-wpcb__item"><a href="/le-mag/">Le Mag</a><span class="cdc-wpcb__separator">/</span></li>
                                <li class="cdc-wpcb__item"><a href="/le-mag/ia/">IA</a><span class="cdc-wpcb__separator">/</span></li>
                                <li class="cdc-wpcb__item cdc-wpcb__item--current"><span aria-current="page">Titre de l'article</span></li>
                            </ol>
                        </nav>
                    </div>

                    <h4>Structure logique</h4>
                    <div class="cdc-preview__structure">
                        <code>Accueil > Le Mag > IA > Titre de l'article</code>
                    </div>
                </div>
            </div>
        </div>

        <div class="cdc-form-actions">
            <button type="submit" class="button button-primary button-large">
                <span class="dashicons dashicons-saved"></span>
                Enregistrer la règle
            </button>
            <button type="button" class="button button-large">
                <span class="dashicons dashicons-visibility"></span>
                Tester la règle
            </button>
            <a href="?page=cdc-breadcrumbs-rules" class="button button-large">Annuler</a>
        </div>
    </form>
</div>
