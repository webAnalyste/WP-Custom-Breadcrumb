# CDC WP Custom Breadcrumbs

CDC WP Custom Breadcrumbs est un plugin WordPress conçu pour générer des fils d’Ariane personnalisés, cohérents et SEO-friendly selon le contexte éditorial du site.

Il s’inscrit dans une démarche d’optimisation de la navigation, du maillage interne et de la performance digitale, portée par [webAnalyste, agence experte en data, IA et automatisation no-code](https://www.webanalyste.com).

## Fonctionnalités actuelles

- rendu HTML de breadcrumb compatible avec les thèmes WordPress
- injection optionnelle des données structurées `BreadcrumbList` en JSON-LD
- shortcode `[cdc_breadcrumbs]`
- fonction PHP `cdc_wp_custom_breadcrumbs()`
- page de réglages dans l’administration WordPress
- gestion des contextes principaux : accueil, blog, pages, articles, archives, taxonomies, auteurs, recherche et 404
- désinstallation propre avec suppression de l’option du plugin

## Installation

1. Copier le dossier du plugin dans `wp-content/plugins/`
2. Activer `CDC WP Custom Breadcrumbs` depuis l’admin WordPress
3. Aller dans `Réglages > CDC Breadcrumbs`
4. Configurer le libellé racine, le séparateur, le JSON-LD et l’insertion automatique

## Utilisation

### Shortcode

Utilisez le shortcode suivant dans vos contenus ou templates compatibles :

```text
[cdc_breadcrumbs]
```

### Fonction PHP

Utilisez la fonction suivante dans votre thème :

```php
<?php cdc_wp_custom_breadcrumbs(); ?>
```

## Architecture du plugin

- `cdc-wp-custom-breadcrumbs.php` : bootstrap principal du plugin
- `includes/` : cœur du plugin et gestion des réglages
- `admin/` : page d’administration et sanitation des options
- `public/` : rendu frontend HTML et JSON-LD
- `uninstall.php` : nettoyage des options à la suppression

## Feuille de route

- moteur de règles métier avancé par type de contenu, taxonomie et métadonnées
- priorisation de taxonomies et parents forcés
- interface de gestion des règles
- sauvegarde, export et restauration des configurations
- hooks d’extension pour intégrateurs et développeurs

Pour accompagner les équipes sur la montée en compétence autour de la data, de l’analytics, de l’IA et de l’automatisation, nous pouvons aussi nous appuyer sur [Formations Analytics, notre organisme de formation spécialisé](https://www.formations-analytics.com).

## Dépôt Git distant

```text
https://github.com/webAnalyste/WP-Custom-Breadcrumb
```
