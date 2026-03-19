<?php
if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap cdc-breadcrumbs-prototype">
    <div class="cdc-header">
        <h1>Sauvegardes & Configurations</h1>
        <button class="button button-primary">
            <span class="dashicons dashicons-backup"></span>
            Créer un backup
        </button>
    </div>

    <div class="cdc-alert cdc-alert--info">
        <strong>Sécurité :</strong> Les backups permettent de restaurer une configuration précédente en cas de problème. Un backup automatique est créé avant chaque modification majeure.
    </div>

    <div class="cdc-card">
        <div class="cdc-card__header">
            <h2>Actions rapides</h2>
        </div>
        <div class="cdc-card__body">
            <div class="cdc-backup-actions">
                <div class="cdc-backup-action">
                    <h3><span class="dashicons dashicons-download"></span> Exporter la configuration</h3>
                    <p>Télécharger un fichier JSON contenant toutes les règles et réglages actuels.</p>
                    <button class="button button-large">Exporter maintenant</button>
                </div>

                <div class="cdc-backup-action">
                    <h3><span class="dashicons dashicons-upload"></span> Importer une configuration</h3>
                    <p>Restaurer une configuration depuis un fichier JSON exporté précédemment.</p>
                    <input type="file" accept=".json" style="margin-bottom: 10px;">
                    <button class="button button-large">Importer</button>
                </div>

                <div class="cdc-backup-action">
                    <h3><span class="dashicons dashicons-backup"></span> Créer un backup manuel</h3>
                    <p>Sauvegarder l'état actuel de la configuration avec un nom personnalisé.</p>
                    <input type="text" class="cdc-input" placeholder="Nom du backup..." style="margin-bottom: 10px;">
                    <button class="button button-large button-primary">Créer le backup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="cdc-card">
        <div class="cdc-card__header">
            <h2>Historique des backups</h2>
        </div>
        <div class="cdc-card__body">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Nom du backup</th>
                        <th>Date de création</th>
                        <th>Type</th>
                        <th>Règles</th>
                        <th>Taille</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>Configuration production v2</strong>
                            <div class="row-actions">
                                <span><a href="#">Détails</a> | </span>
                                <span><a href="#">Télécharger</a> | </span>
                                <span class="trash"><a href="#">Supprimer</a></span>
                            </div>
                        </td>
                        <td>19 mars 2026 à 08:30</td>
                        <td><span class="cdc-badge cdc-badge--manual">Manuel</span></td>
                        <td>8 règles</td>
                        <td>12 Ko</td>
                        <td>
                            <button class="button button-primary button-small">Restaurer</button>
                            <button class="button button-small">Télécharger</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Avant modification règle formations</strong>
                            <div class="row-actions">
                                <span><a href="#">Détails</a> | </span>
                                <span><a href="#">Télécharger</a> | </span>
                                <span class="trash"><a href="#">Supprimer</a></span>
                            </div>
                        </td>
                        <td>18 mars 2026 à 14:22</td>
                        <td><span class="cdc-badge cdc-badge--auto">Automatique</span></td>
                        <td>8 règles</td>
                        <td>11 Ko</td>
                        <td>
                            <button class="button button-primary button-small">Restaurer</button>
                            <button class="button button-small">Télécharger</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Configuration initiale</strong>
                            <div class="row-actions">
                                <span><a href="#">Détails</a> | </span>
                                <span><a href="#">Télécharger</a> | </span>
                                <span class="trash"><a href="#">Supprimer</a></span>
                            </div>
                        </td>
                        <td>15 mars 2026 à 10:00</td>
                        <td><span class="cdc-badge cdc-badge--manual">Manuel</span></td>
                        <td>5 règles</td>
                        <td>8 Ko</td>
                        <td>
                            <button class="button button-primary button-small">Restaurer</button>
                            <button class="button button-small">Télécharger</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Backup automatique activation</strong>
                            <div class="row-actions">
                                <span><a href="#">Détails</a> | </span>
                                <span><a href="#">Télécharger</a> | </span>
                                <span class="trash"><a href="#">Supprimer</a></span>
                            </div>
                        </td>
                        <td>10 mars 2026 à 09:15</td>
                        <td><span class="cdc-badge cdc-badge--auto">Automatique</span></td>
                        <td>0 règles</td>
                        <td>2 Ko</td>
                        <td>
                            <button class="button button-primary button-small">Restaurer</button>
                            <button class="button button-small">Télécharger</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="cdc-card">
        <div class="cdc-card__header">
            <h2>Paramètres de sauvegarde</h2>
        </div>
        <div class="cdc-card__body">
            <form class="cdc-form">
                <div class="cdc-form-row cdc-form-row--inline">
                    <label class="cdc-label">
                        <input type="checkbox" checked>
                        Créer un backup automatique avant chaque modification majeure
                    </label>
                </div>

                <div class="cdc-form-row">
                    <label class="cdc-label">
                        Nombre maximum de backups à conserver
                        <input type="number" class="cdc-input cdc-input--small" value="10" min="1" max="50">
                    </label>
                    <p class="description">Les backups les plus anciens seront supprimés automatiquement.</p>
                </div>

                <div class="cdc-form-row cdc-form-row--inline">
                    <label class="cdc-label">
                        <input type="checkbox">
                        Activer les backups automatiques quotidiens
                    </label>
                </div>

                <button type="submit" class="button button-primary">Enregistrer les paramètres</button>
            </form>
        </div>
    </div>

    <div class="cdc-alert cdc-alert--warning">
        <strong>Attention :</strong> La restauration d'un backup remplacera toutes les règles et réglages actuels. Cette action est irréversible (sauf si vous créez un backup avant).
    </div>
</div>
