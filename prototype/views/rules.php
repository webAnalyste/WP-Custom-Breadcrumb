<?php
if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap cdc-breadcrumbs-prototype">
    <div class="cdc-header">
        <h1>Gestion des règles</h1>
        <a href="?page=cdc-breadcrumbs-new-rule" class="button button-primary">
            <span class="dashicons dashicons-plus-alt"></span>
            Nouvelle règle
        </a>
    </div>

    <div class="cdc-filters">
        <div class="cdc-filters__group">
            <label>Filtrer par statut :</label>
            <select class="cdc-select">
                <option>Toutes les règles</option>
                <option>Actives uniquement</option>
                <option>Inactives uniquement</option>
            </select>
        </div>

        <div class="cdc-filters__group">
            <label>Filtrer par contexte :</label>
            <select class="cdc-select">
                <option>Tous les contextes</option>
                <option>Post</option>
                <option>Page</option>
                <option>CPT</option>
                <option>Taxonomie</option>
                <option>Archive</option>
            </select>
        </div>

        <div class="cdc-filters__group">
            <label>Rechercher :</label>
            <input type="search" class="cdc-input" placeholder="Nom de la règle...">
        </div>
    </div>

    <div class="cdc-card">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="check-column"><input type="checkbox"></th>
                    <th>Règle</th>
                    <th>Contexte</th>
                    <th>Structure du breadcrumb</th>
                    <th>Priorité</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th class="check-column"><input type="checkbox"></th>
                    <td>
                        <strong>Articles de blog</strong>
                        <div class="row-actions">
                            <span><a href="#">Éditer</a> | </span>
                            <span><a href="#">Dupliquer</a> | </span>
                            <span><a href="#">Prévisualiser</a> | </span>
                            <span class="trash"><a href="#">Supprimer</a></span>
                        </div>
                    </td>
                    <td>
                        <span class="cdc-badge">Post</span>
                    </td>
                    <td>
                        <code>Accueil > Le Mag > {category} > {title}</code>
                    </td>
                    <td><span class="cdc-priority cdc-priority--high">100</span></td>
                    <td><span class="cdc-status cdc-status--active">Actif</span></td>
                    <td>
                        <button class="button button-small">Éditer</button>
                        <button class="button button-small">Tester</button>
                    </td>
                </tr>
                <tr>
                    <th class="check-column"><input type="checkbox"></th>
                    <td>
                        <strong>Formations</strong>
                        <div class="row-actions">
                            <span><a href="#">Éditer</a> | </span>
                            <span><a href="#">Dupliquer</a> | </span>
                            <span><a href="#">Prévisualiser</a> | </span>
                            <span class="trash"><a href="#">Supprimer</a></span>
                        </div>
                    </td>
                    <td>
                        <span class="cdc-badge">CPT formations</span>
                    </td>
                    <td>
                        <code>Accueil > Formations > {parcours} > {title}</code>
                    </td>
                    <td><span class="cdc-priority cdc-priority--high">90</span></td>
                    <td><span class="cdc-status cdc-status--active">Actif</span></td>
                    <td>
                        <button class="button button-small">Éditer</button>
                        <button class="button button-small">Tester</button>
                    </td>
                </tr>
                <tr>
                    <th class="check-column"><input type="checkbox"></th>
                    <td>
                        <strong>Pages hiérarchiques</strong>
                        <div class="row-actions">
                            <span><a href="#">Éditer</a> | </span>
                            <span><a href="#">Dupliquer</a> | </span>
                            <span><a href="#">Prévisualiser</a> | </span>
                            <span class="trash"><a href="#">Supprimer</a></span>
                        </div>
                    </td>
                    <td>
                        <span class="cdc-badge">Page</span>
                    </td>
                    <td>
                        <code>Accueil > {ancestors} > {title}</code>
                    </td>
                    <td><span class="cdc-priority cdc-priority--medium">80</span></td>
                    <td><span class="cdc-status cdc-status--active">Actif</span></td>
                    <td>
                        <button class="button button-small">Éditer</button>
                        <button class="button button-small">Tester</button>
                    </td>
                </tr>
                <tr>
                    <th class="check-column"><input type="checkbox"></th>
                    <td>
                        <strong>Agences locales</strong>
                        <div class="row-actions">
                            <span><a href="#">Éditer</a> | </span>
                            <span><a href="#">Dupliquer</a> | </span>
                            <span><a href="#">Prévisualiser</a> | </span>
                            <span class="trash"><a href="#">Supprimer</a></span>
                        </div>
                    </td>
                    <td>
                        <span class="cdc-badge">CPT agence_locale</span>
                    </td>
                    <td>
                        <code>Accueil > Agence Data & IA > {meta:_wa_city} > {title}</code>
                    </td>
                    <td><span class="cdc-priority cdc-priority--medium">70</span></td>
                    <td><span class="cdc-status cdc-status--inactive">Inactif</span></td>
                    <td>
                        <button class="button button-small">Éditer</button>
                        <button class="button button-small">Tester</button>
                    </td>
                </tr>
                <tr>
                    <th class="check-column"><input type="checkbox"></th>
                    <td>
                        <strong>Archive formations</strong>
                        <div class="row-actions">
                            <span><a href="#">Éditer</a> | </span>
                            <span><a href="#">Dupliquer</a> | </span>
                            <span><a href="#">Prévisualiser</a> | </span>
                            <span class="trash"><a href="#">Supprimer</a></span>
                        </div>
                    </td>
                    <td>
                        <span class="cdc-badge">Archive CPT</span>
                    </td>
                    <td>
                        <code>Accueil > Formations</code>
                    </td>
                    <td><span class="cdc-priority cdc-priority--medium">60</span></td>
                    <td><span class="cdc-status cdc-status--active">Actif</span></td>
                    <td>
                        <button class="button button-small">Éditer</button>
                        <button class="button button-small">Tester</button>
                    </td>
                </tr>
                <tr>
                    <th class="check-column"><input type="checkbox"></th>
                    <td>
                        <strong>Taxonomie parcours</strong>
                        <div class="row-actions">
                            <span><a href="#">Éditer</a> | </span>
                            <span><a href="#">Dupliquer</a> | </span>
                            <span><a href="#">Prévisualiser</a> | </span>
                            <span class="trash"><a href="#">Supprimer</a></span>
                        </div>
                    </td>
                    <td>
                        <span class="cdc-badge">Taxonomie</span>
                    </td>
                    <td>
                        <code>Accueil > Formations > {term_name}</code>
                    </td>
                    <td><span class="cdc-priority cdc-priority--low">50</span></td>
                    <td><span class="cdc-status cdc-status--active">Actif</span></td>
                    <td>
                        <button class="button button-small">Éditer</button>
                        <button class="button button-small">Tester</button>
                    </td>
                </tr>
                <tr>
                    <th class="check-column"><input type="checkbox"></th>
                    <td>
                        <strong>Recherche</strong>
                        <div class="row-actions">
                            <span><a href="#">Éditer</a> | </span>
                            <span><a href="#">Dupliquer</a> | </span>
                            <span><a href="#">Prévisualiser</a> | </span>
                            <span class="trash"><a href="#">Supprimer</a></span>
                        </div>
                    </td>
                    <td>
                        <span class="cdc-badge">Recherche</span>
                    </td>
                    <td>
                        <code>Accueil > Résultats : {search_query}</code>
                    </td>
                    <td><span class="cdc-priority cdc-priority--low">40</span></td>
                    <td><span class="cdc-status cdc-status--inactive">Inactif</span></td>
                    <td>
                        <button class="button button-small">Éditer</button>
                        <button class="button button-small">Tester</button>
                    </td>
                </tr>
                <tr>
                    <th class="check-column"><input type="checkbox"></th>
                    <td>
                        <strong>Fallback global</strong>
                        <div class="row-actions">
                            <span><a href="#">Éditer</a> | </span>
                            <span class="trash"><a href="#">Supprimer</a></span>
                        </div>
                    </td>
                    <td>
                        <span class="cdc-badge">Fallback</span>
                    </td>
                    <td>
                        <code>Accueil > {title}</code>
                    </td>
                    <td><span class="cdc-priority cdc-priority--low">10</span></td>
                    <td><span class="cdc-status cdc-status--active">Actif</span></td>
                    <td>
                        <button class="button button-small">Éditer</button>
                        <button class="button button-small">Tester</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="cdc-bulk-actions">
        <select>
            <option>Actions groupées</option>
            <option>Activer</option>
            <option>Désactiver</option>
            <option>Dupliquer</option>
            <option>Supprimer</option>
        </select>
        <button class="button">Appliquer</button>
    </div>
</div>
