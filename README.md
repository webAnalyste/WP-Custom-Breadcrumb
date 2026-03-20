# Custom Breadcrumb

Plugin WordPress pour générer des fils d'Ariane personnalisés, SEO-friendly et cohérents selon le contexte éditorial du site.

Développé par [webAnalyste, agence experte en data, IA et automatisation](https://www.webanalyste.com).

## Fonctionnalités

- **Règles personnalisées** par type de contenu (articles, pages, CPT, archives, taxonomies)
- **Segments libres** : texte libre, lien vers une page, archive de CPT, ou taxonomie réelle du post
- **Mode d'insertion par règle** : automatique (position globale) ou shortcode uniquement
- Rendu HTML sémantique `<nav>` + `<ol>` + attributs `aria`
- Données structurées **JSON-LD BreadcrumbList** pour le SEO
- Alignement horizontal : gauche, centre, droite
- Position d'insertion : avant l'article, avant le contenu, après le contenu
- Mise à jour automatique depuis GitHub
- Désinstallation propre

## Installation

1. Télécharger la dernière release depuis [GitHub Releases](https://github.com/webAnalyste/WP-Custom-Breadcrumb/releases)
2. Copier le dossier dans `wp-content/plugins/`
3. Activer **Custom Breadcrumb** depuis l'admin WordPress
4. Aller dans **Breadcrumb** dans le menu admin
5. Créer vos règles et configurer les réglages globaux

## Utilisation

### Shortcode

```
[custom_breadcrumb]
```

### Fonction PHP (dans le thème)

```php
<?php custom_breadcrumb(); ?>
```

### Récupérer le HTML sans l'afficher

```php
$html = custom_breadcrumb_get();
```

## Règles de breadcrumb

Chaque règle définit :
- **Type de contenu** : article, page, archive catégorie, archive étiquette, CPT…
- **Segments** : éléments entre Accueil et la page actuelle (texte, page, archive, taxonomie)
- **Mode d'insertion** :
  - *Automatique* — inséré à la position globale choisie
  - *Shortcode uniquement* — rendu uniquement via `[custom_breadcrumb]`
- **Afficher la taxonomie** : insère le terme (et ses ancêtres) avant la page actuelle
- **Afficher les parents** : insère la hiérarchie de pages parentes

## Architecture

```
custom-breadcrumb.php          ← entrée plugin
includes/
  class-config.php             ← gestion des réglages (wp_options)
  class-context.php            ← détection du contexte WP courant
  class-builder.php            ← construction des items du breadcrumb
  class-renderer.php           ← rendu HTML et JSON-LD
  class-updater.php            ← mises à jour automatiques depuis GitHub
admin/
  class-admin.php              ← menu, AJAX, liens plugins
  views/page-advanced.php      ← interface d'administration
  assets/script-advanced.js    ← JS admin
  assets/style-advanced.css    ← CSS admin
assets/
  breadcrumb.css               ← styles frontend
uninstall.php                  ← nettoyage à la désinstallation
```

## Dépôt

```
https://github.com/webAnalyste/WP-Custom-Breadcrumb
```
