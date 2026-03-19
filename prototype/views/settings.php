<?php
if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap cdc-breadcrumbs-prototype">
    <div class="cdc-header">
        <h1>Réglages globaux</h1>
    </div>

    <form class="cdc-form">
        <div class="cdc-card">
            <div class="cdc-card__header">
                <h2>Paramètres généraux</h2>
            </div>
            <div class="cdc-card__body">
                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Libellé racine par défaut
                        <input type="text" class="cdc-input" value="Accueil" placeholder="Accueil">
                    </label>
                    <p class="description">Texte affiché pour le lien vers la page d'accueil.</p>
                </div>

                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Séparateur visuel
                        <input type="text" class="cdc-input cdc-input--small" value="/" placeholder="/">
                    </label>
                    <p class="description">Caractère(s) utilisé(s) pour séparer les éléments du breadcrumb. Exemples : / > » ›</p>
                </div>

                <div class="cdc-form-row cdc-form-row--inline">
                    <label class="cdc-label">
                        <input type="checkbox" checked>
                        Afficher sur la page d'accueil
                    </label>
                    <p class="description">Afficher un breadcrumb minimal sur la page d'accueil.</p>
                </div>
            </div>
        </div>

        <div class="cdc-card">
            <div class="cdc-card__header">
                <h2>Rendu HTML</h2>
            </div>
            <div class="cdc-card__body">
                <div class="cdc-form-row cdc-form-row--inline">
                    <label class="cdc-label">
                        <input type="checkbox" checked>
                        Activer le rendu HTML
                    </label>
                    <p class="description">Générer le breadcrumb HTML sur le frontend.</p>
                </div>

                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Classes CSS personnalisées
                        <input type="text" class="cdc-input" placeholder="ma-classe custom-breadcrumb">
                    </label>
                    <p class="description">Classes CSS additionnelles à ajouter au conteneur du breadcrumb.</p>
                </div>

                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Mode d'insertion automatique
                        <select class="cdc-select">
                            <option value="none">Désactivé (manuel uniquement)</option>
                            <option value="before_content">Avant le contenu principal</option>
                            <option value="after_title">Après le titre</option>
                            <option value="hook">Via hook thème</option>
                        </select>
                    </label>
                    <p class="description">Comment le breadcrumb doit être inséré automatiquement.</p>
                </div>
            </div>
        </div>

        <div class="cdc-card">
            <div class="cdc-card__header">
                <h2>Données structurées (JSON-LD)</h2>
            </div>
            <div class="cdc-card__body">
                <div class="cdc-form-row cdc-form-row--inline">
                    <label class="cdc-label">
                        <input type="checkbox" checked>
                        Activer le JSON-LD BreadcrumbList
                    </label>
                    <p class="description">Injecter les données structurées Schema.org dans le <code>&lt;head&gt;</code> pour le SEO.</p>
                </div>

                <div class="cdc-alert cdc-alert--info">
                    <strong>SEO :</strong> Les données structurées BreadcrumbList aident les moteurs de recherche à comprendre la hiérarchie de votre site.
                </div>
            </div>
        </div>

        <div class="cdc-card">
            <div class="cdc-card__header">
                <h2>Comportement fallback</h2>
            </div>
            <div class="cdc-card__body">
                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Stratégie si aucune règle ne correspond
                        <select class="cdc-select">
                            <option value="minimal">Breadcrumb minimal (Accueil > Titre)</option>
                            <option value="none">Ne rien afficher</option>
                            <option value="custom">Règle fallback personnalisée</option>
                        </select>
                    </label>
                </div>

                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Gestion des taxonomies multiples
                        <select class="cdc-select">
                            <option value="first">Premier terme WordPress</option>
                            <option value="primary">Terme principal (si défini)</option>
                            <option value="deepest">Terme le plus profond</option>
                        </select>
                    </label>
                    <p class="description">Comment choisir le terme de taxonomie si plusieurs sont assignés.</p>
                </div>
            </div>
        </div>

        <div class="cdc-card">
            <div class="cdc-card__header">
                <h2>Compatibilité & Performance</h2>
            </div>
            <div class="cdc-card__body">
                <div class="cdc-form-row cdc-form-row--inline">
                    <label class="cdc-label">
                        <input type="checkbox" checked>
                        Activer le cache des règles
                    </label>
                    <p class="description">Mettre en cache la résolution des règles pour améliorer les performances.</p>
                </div>

                <div class="cdc-form-row cdc-form-row--inline">
                    <label class="cdc-label">
                        <input type="checkbox">
                        Mode debug
                    </label>
                    <p class="description">Afficher des informations de débogage dans les commentaires HTML (désactiver en production).</p>
                </div>

                <div class="cdc-form-row cdc-form-row--inline">
                    <label class="cdc-label">
                        <input type="checkbox" checked>
                        Support multisite
                    </label>
                    <p class="description">Activer la compatibilité avec WordPress multisite.</p>
                </div>
            </div>
        </div>

        <div class="cdc-card">
            <div class="cdc-card__header">
                <h2>Métadonnées personnalisées</h2>
            </div>
            <div class="cdc-card__body">
                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Clés de métadonnées autorisées
                        <textarea class="cdc-textarea" rows="4" placeholder="_wa_breadcrumb_parent_id&#10;_wa_breadcrumb_label&#10;_wa_city">_wa_breadcrumb_parent_id
_wa_breadcrumb_label
_wa_city</textarea>
                    </label>
                    <p class="description">Liste des clés de métadonnées que le plugin peut utiliser (une par ligne). Sécurité : seules ces clés seront autorisées.</p>
                </div>
            </div>
        </div>

        <div class="cdc-form-actions">
            <button type="submit" class="button button-primary button-large">
                <span class="dashicons dashicons-saved"></span>
                Enregistrer les réglages
            </button>
            <button type="button" class="button button-large">Réinitialiser aux valeurs par défaut</button>
        </div>
    </form>
</div>
