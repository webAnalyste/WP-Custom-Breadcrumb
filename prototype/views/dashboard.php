<?php
if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap cdc-breadcrumbs-prototype">
    <div class="cdc-header">
        <h1>CDC WP Custom Breadcrumbs</h1>
        <span class="cdc-badge cdc-badge--prototype">PROTOTYPE UX</span>
    </div>

    <div class="cdc-alert cdc-alert--info">
        <strong>Mode prototype :</strong> Cette interface est un prototype UX non fonctionnel destiné à valider l'ergonomie avant développement métier.
    </div>

    <div class="cdc-dashboard">
        <div class="cdc-dashboard__grid">
            <div class="cdc-card cdc-card--stat">
                <div class="cdc-card__header">
                    <h3>Règles actives</h3>
                    <span class="dashicons dashicons-yes-alt"></span>
                </div>
                <div class="cdc-card__body">
                    <div class="cdc-stat">
                        <span class="cdc-stat__value">5</span>
                        <span class="cdc-stat__label">sur 8 règles</span>
                    </div>
                </div>
            </div>

            <div class="cdc-card cdc-card--stat">
                <div class="cdc-card__header">
                    <h3>Dernier backup</h3>
                    <span class="dashicons dashicons-backup"></span>
                </div>
                <div class="cdc-card__body">
                    <div class="cdc-stat">
                        <span class="cdc-stat__value">Il y a 2h</span>
                        <span class="cdc-stat__label">Automatique</span>
                    </div>
                </div>
            </div>

            <div class="cdc-card cdc-card--stat">
                <div class="cdc-card__header">
                    <h3>Contextes couverts</h3>
                    <span class="dashicons dashicons-admin-site-alt3"></span>
                </div>
                <div class="cdc-card__body">
                    <div class="cdc-stat">
                        <span class="cdc-stat__value">12</span>
                        <span class="cdc-stat__label">types de pages</span>
                    </div>
                </div>
            </div>

            <div class="cdc-card cdc-card--stat">
                <div class="cdc-card__header">
                    <h3>Statut global</h3>
                    <span class="dashicons dashicons-admin-generic"></span>
                </div>
                <div class="cdc-card__body">
                    <div class="cdc-stat">
                        <span class="cdc-stat__value cdc-stat__value--success">Opérationnel</span>
                        <span class="cdc-stat__label">Aucune alerte</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="cdc-dashboard__main">
            <div class="cdc-card">
                <div class="cdc-card__header">
                    <h2>Règles récentes</h2>
                    <a href="?page=cdc-breadcrumbs-rules" class="button">Voir toutes les règles</a>
                </div>
                <div class="cdc-card__body">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Règle</th>
                                <th>Contexte</th>
                                <th>Priorité</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Articles de blog</strong></td>
                                <td><span class="cdc-badge">Post</span></td>
                                <td>100</td>
                                <td><span class="cdc-status cdc-status--active">Actif</span></td>
                                <td>
                                    <button class="button button-small">Éditer</button>
                                    <button class="button button-small">Dupliquer</button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Formations</strong></td>
                                <td><span class="cdc-badge">CPT formations</span></td>
                                <td>90</td>
                                <td><span class="cdc-status cdc-status--active">Actif</span></td>
                                <td>
                                    <button class="button button-small">Éditer</button>
                                    <button class="button button-small">Dupliquer</button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Pages hiérarchiques</strong></td>
                                <td><span class="cdc-badge">Page</span></td>
                                <td>80</td>
                                <td><span class="cdc-status cdc-status--active">Actif</span></td>
                                <td>
                                    <button class="button button-small">Éditer</button>
                                    <button class="button button-small">Dupliquer</button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Agences locales</strong></td>
                                <td><span class="cdc-badge">CPT agence_locale</span></td>
                                <td>70</td>
                                <td><span class="cdc-status cdc-status--inactive">Inactif</span></td>
                                <td>
                                    <button class="button button-small">Éditer</button>
                                    <button class="button button-small">Dupliquer</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="cdc-card">
                <div class="cdc-card__header">
                    <h2>Actions rapides</h2>
                </div>
                <div class="cdc-card__body">
                    <div class="cdc-actions">
                        <a href="?page=cdc-breadcrumbs-new-rule" class="button button-primary button-hero">
                            <span class="dashicons dashicons-plus-alt"></span>
                            Créer une nouvelle règle
                        </a>
                        <button class="button button-hero">
                            <span class="dashicons dashicons-backup"></span>
                            Créer un backup
                        </button>
                        <button class="button button-hero">
                            <span class="dashicons dashicons-download"></span>
                            Exporter la configuration
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="cdc-dashboard__sidebar">
            <div class="cdc-card">
                <div class="cdc-card__header">
                    <h3>Alertes & Notifications</h3>
                </div>
                <div class="cdc-card__body">
                    <div class="cdc-alert cdc-alert--success">
                        <strong>Configuration valide</strong><br>
                        Toutes les règles actives sont cohérentes.
                    </div>
                </div>
            </div>

            <div class="cdc-card">
                <div class="cdc-card__header">
                    <h3>Aide & Documentation</h3>
                </div>
                <div class="cdc-card__body">
                    <ul class="cdc-help-links">
                        <li><a href="#" target="_blank">Guide de démarrage</a></li>
                        <li><a href="#" target="_blank">Exemples de règles</a></li>
                        <li><a href="#" target="_blank">Hooks & Filtres</a></li>
                        <li><a href="#" target="_blank">FAQ</a></li>
                    </ul>
                </div>
            </div>

            <div class="cdc-card">
                <div class="cdc-card__header">
                    <h3>À propos</h3>
                </div>
                <div class="cdc-card__body">
                    <p><strong>Version :</strong> 0.1.0 (Prototype)</p>
                    <p><strong>Développé par :</strong><br>
                    <a href="https://www.webanalyste.com" target="_blank">webAnalyste</a><br>
                    Agence experte en data, IA et automatisation no-code</p>
                    <p><strong>Formation :</strong><br>
                    <a href="https://www.formations-analytics.com" target="_blank">Formations Analytics</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
