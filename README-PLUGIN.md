# Custom Breadcrumb v2.0.0

Plugin WordPress pour personnaliser vos fils d'Ariane en quelques clics.

## 🚀 Installation

1. Télécharger le plugin
2. Aller dans WordPress : Extensions > Ajouter > Téléverser
3. Activer le plugin
4. Aller dans le menu **Breadcrumb** dans l'admin

## 💻 Utilisation

### Méthode 1 : Insertion automatique

Dans l'admin, cocher **"Insérer automatiquement avant le contenu"**

### Méthode 2 : Shortcode

Ajouter dans n'importe quel contenu :
```
[custom_breadcrumb]
```

### Méthode 3 : Code PHP

Ajouter dans votre thème (header.php, single.php, page.php...) :
```php
<?php custom_breadcrumb(); ?>
```

Ou pour récupérer le HTML :
```php
<?php echo custom_breadcrumb_get(); ?>
```

## ⚙️ Configuration

### Articles de blog
- Personnaliser le nom de la section (ex: "Le Mag" au lieu de "Blog")
- Afficher/masquer la catégorie
- Afficher les catégories parentes

### Pages
- Afficher automatiquement la hiérarchie des pages parentes

### Custom Post Types
- Personnaliser le nom de la section
- Choisir quelle taxonomie afficher

### Réglages globaux
- Texte du lien "Accueil"
- Séparateur (/, >, », →)
- Activer/désactiver le JSON-LD pour le SEO

## 🎨 Personnalisation CSS

Le breadcrumb utilise les classes CSS suivantes :

```css
.custom-breadcrumb { }
.custom-breadcrumb__list { }
.custom-breadcrumb__item { }
.custom-breadcrumb__item a { }
.custom-breadcrumb__item--current { }
.custom-breadcrumb__separator { }
```

Vous pouvez les surcharger dans votre thème.

## 🔧 Hooks pour développeurs

### Filtres

**Modifier les éléments du breadcrumb :**
```php
add_filter('custom_breadcrumb_items', function($items, $context) {
    // Modifier $items
    return $items;
}, 10, 2);
```

**Modifier le HTML final :**
```php
add_filter('custom_breadcrumb_html', function($html, $items) {
    // Modifier $html
    return $html;
}, 10, 2);
```

## 📋 Fonctionnalités

- ✅ Interface WYSIWYG claire et intuitive
- ✅ Support des articles, pages, CPT, taxonomies, archives
- ✅ Génération automatique du JSON-LD (Schema.org)
- ✅ Shortcode et fonction PHP
- ✅ Insertion automatique optionnelle
- ✅ Hiérarchie des pages parentes
- ✅ Catégories et taxonomies personnalisées
- ✅ Responsive
- ✅ Compatible WordPress 6.4+
- ✅ Compatible PHP 7.4+

## 🔒 Sécurité

- Protection ABSPATH sur tous les fichiers
- Nonces pour les requêtes AJAX
- Sanitization de toutes les entrées
- Échappement de toutes les sorties
- Vérification des capabilities utilisateur

## 📊 Compatibilité

- **WordPress** : 6.4+
- **PHP** : 7.4+
- **Navigateurs** : Tous navigateurs modernes

## 🐛 Support

- **GitHub** : https://github.com/webAnalyste/WP-Custom-Breadcrumb
- **Issues** : https://github.com/webAnalyste/WP-Custom-Breadcrumb/issues

## 📝 Changelog

Voir `CHANGELOG.md` pour l'historique complet des versions.

## 👨‍💻 Développé par

**[webAnalyste](https://www.webanalyste.com)** - Agence experte en data, IA et automatisation no-code

**Formation** : [Formations Analytics](https://www.formations-analytics.com) - Organisme de formation spécialisé

## 📄 Licence

GPL v2 or later
