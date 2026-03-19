# Roadmap - Custom Breadcrumb

## ✅ Phase 1 : Prototypage UX (TERMINÉE)

### v1.0.0 - Prototype initial
- ❌ Interface trop complexe (5 pages, concept de "règles")
- ❌ Jargon technique incompréhensible
- ❌ Shortcode non visible

### v1.1.0 - Simplification
- ⚠️ Exemples avant/après utiles
- ❌ Encore trop abstrait
- ❌ Shortcode toujours caché

### v1.3.0 - Interface WYSIWYG ✅ VALIDÉE
- ✅ Breadcrumb cliquable en grand
- ✅ Modification directe par clic
- ✅ Shortcode et code PHP très visibles
- ✅ Langage simple et clair
- ✅ Client serait content de cette interface

---

## 🚀 Phase 2 : Développement métier (À VENIR)

### v2.0.0 - Plugin fonctionnel complet

**Objectif** : Transformer le prototype v1.3.0 en plugin réellement fonctionnel

#### Backend à développer

**1. Système de configuration**
```php
// Structure de données pour stocker les configurations
[
    'post' => [
        'home_label' => 'Accueil',
        'section_label' => 'Le Mag',
        'show_category' => true,
        'show_parent_categories' => false,
    ],
    'page' => [
        'show_parents' => true,
    ],
    'formation' => [
        'section_label' => 'Formations',
        'taxonomy' => 'parcours',
    ],
    'global' => [
        'separator' => '/',
        'enable_jsonld' => true,
        'auto_insert' => false,
    ]
]
```

**2. Moteur de rendu du breadcrumb**
- Détection du contexte (post, page, CPT, taxonomie, archive...)
- Construction du tableau d'éléments selon la config
- Génération HTML avec classes CSS appropriées
- Génération JSON-LD pour SEO

**3. Hooks et filtres**
```php
// Permettre aux développeurs de personnaliser
apply_filters('custom_breadcrumb_items', $items, $context);
apply_filters('custom_breadcrumb_html', $html, $items);
do_action('custom_breadcrumb_before_render', $items);
```

**4. AJAX pour sauvegarde**
- Endpoint REST API pour enregistrer la config
- Validation et sanitation des données
- Nonces pour sécurité

#### Frontend à développer

**1. Shortcode fonctionnel**
```php
[custom_breadcrumb]
```

**2. Fonction PHP**
```php
<?php custom_breadcrumb(); ?>
```

**3. Insertion automatique**
- Hook `the_content` si option activée
- Respect des exclusions (page d'accueil, etc.)

**4. Styles CSS**
- Classes de base pour le breadcrumb
- Responsive
- Personnalisable par le thème

#### Admin à développer

**1. Sauvegarde des modifications**
- Récupération des valeurs du formulaire
- Validation
- Stockage en option WordPress
- Feedback utilisateur

**2. Édition en temps réel**
- AJAX pour modifier les textes
- Mise à jour immédiate de l'aperçu
- Pas de rechargement de page

**3. Gestion des CPT**
- Détection automatique des CPT du site
- Interface pour configurer chaque CPT
- Choix des taxonomies disponibles

### Tâches de développement v2.0.0

- [ ] Créer la structure de données de configuration
- [ ] Développer le moteur de détection de contexte
- [ ] Implémenter le builder de breadcrumb
- [ ] Générer le HTML sémantique
- [ ] Générer le JSON-LD
- [ ] Créer le shortcode fonctionnel
- [ ] Créer la fonction PHP
- [ ] Implémenter l'insertion automatique
- [ ] Développer l'endpoint AJAX de sauvegarde
- [ ] Connecter l'interface admin au backend
- [ ] Ajouter la validation et sanitation
- [ ] Créer les hooks et filtres pour développeurs
- [ ] Écrire les tests unitaires
- [ ] Documentation technique complète

---

## 🎨 Phase 3 : Améliorations (FUTUR)

### v2.1.0 - Fonctionnalités avancées

**À définir selon retours utilisateurs :**
- Import/Export de configuration
- Présets de configuration (blog, e-commerce, etc.)
- Personnalisation CSS avancée
- Support multilingue (WPML, Polylang)
- Cache des breadcrumbs pour performance
- Mode debug avec logs

### v2.2.0 - Intégrations

**Compatibilité avec plugins populaires :**
- WooCommerce (produits, catégories)
- Easy Digital Downloads
- The Events Calendar
- Plugins SEO (Yoast, Rank Math, All in One SEO)

---

## 📊 Critères de succès

### Pour v2.0.0
- ✅ Interface admin identique au prototype v1.3.0 validé
- ✅ Breadcrumb s'affiche correctement sur tous les contextes
- ✅ Shortcode fonctionne partout
- ✅ Code PHP fonctionne dans les thèmes
- ✅ JSON-LD valide selon Schema.org
- ✅ Aucune erreur PHP
- ✅ Compatible WordPress 6.4+
- ✅ Compatible PHP 7.4+
- ✅ Sécurité : nonces, sanitization, escaping
- ✅ Performance : pas de ralentissement du site

### Pour la qualité du code
- ✅ Respect des WordPress Coding Standards
- ✅ Documentation PHPDoc complète
- ✅ Code commenté en français
- ✅ Architecture MVC claire
- ✅ Hooks documentés
- ✅ Tests unitaires

---

## 🎯 Prochaine étape immédiate

**Démarrer le développement de v2.0.0** en conservant l'interface du prototype v1.3.0 validé.

**Priorité 1** : Moteur de rendu fonctionnel
**Priorité 2** : Sauvegarde de configuration
**Priorité 3** : Shortcode et fonction PHP

---

**Plugin développé par [webAnalyste](https://www.webanalyste.com)**  
**Formation : [Formations Analytics](https://www.formations-analytics.com)**
