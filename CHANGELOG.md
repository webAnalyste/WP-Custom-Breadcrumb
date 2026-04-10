# Changelog

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Versioning Sémantique](https://semver.org/lang/fr/).

## [2.1.18] - 2026-04-10

### Corrigé
- **Mise à jour automatique** : ajout du header `User-Agent` requis par l'API GitHub (cause principale du dysfonctionnement — retournait 403 silencieusement)
- Vérification du code HTTP avant de parser la réponse GitHub (évite de parser des erreurs JSON)
- `after_install` : suppression de `activate_plugin()` superflu pendant les updates ; le move n'est effectué que si la destination diffère réellement
- Vidage du transient `update_plugins` à chaque sauvegarde pour forcer une vérification fraîche

## [2.1.17] - 2026-04-10

### Modifié
- Segment "CPT dynamique" renommé "Personnalisé" dans l'interface (valeur interne `dynamic_cpt` inchangée)

## [2.1.16] - 2026-04-10

### Ajouté
- Onglet **Sauvegarde** dans l'admin : export JSON, import JSON, bouton reset
- Option **Conserver les réglages lors de la désinstallation** dans les réglages globaux
- `uninstall.php` respecte cette option avant d'effacer les données
- Nouvel endpoint AJAX `custom_breadcrumb_reset` pour réinitialiser sans désinstaller

## [2.1.15] - 2026-04-10

### Ajouté
- Nouveau type de segment **CPT dynamique** (`dynamic_cpt`) : résolution d'un post CPT parent via conditions taxonomiques croisées
- Système de conditions multiple (logique ET) : chaque condition lie la taxonomie du post courant à celle du CPT cible
- Interface de configuration dans l'admin : sélecteur CPT cible + constructeur de conditions ajout/suppression
- Le segment "Personnalisé" (non fonctionnel) remplacé par ce nouveau type opérationnel
- Les listes de CPTs et taxonomies sont désormais passées dynamiquement au JS depuis PHP

## [2.1.2] - 2026-03-19

### Ajouté
- Correction affichage frontend: une règle enregistrée est maintenant réellement sauvegardée et visible sur le site

## [2.1.1] - 2026-03-19

### Ajouté
- Correction: les règles configurées s'appliquent maintenant sur le site

## [2.1.0] - 2026-03-19

### Ajouté
- Interface avancée avec système de règles personnalisables

## [2.0.1] - 2026-03-19

### Ajouté
- Première release automatique - test du système de mise à jour

## [1.3.0] - 2026-03-19 ✅ VALIDÉ

### Ajouté
- **Interface WYSIWYG ultra-claire** : approche UX designer
- Breadcrumb affiché en grand comme sur le site réel
- Modification directe par clic sur les éléments
- Modal d'édition simple et rapide
- Section "Comment l'utiliser" avec shortcode et code PHP très visibles
- Boutons "Copier" pour shortcode `[custom_breadcrumb]` et code PHP
- Codes couleur évidents : 🟡 Accueil, 🔵 Éditable, 🟣 Automatique, ⚪ Actuel
- Badges 🔄 pour éléments automatiques
- Animation de confirmation après modification
- Archive `custom-breadcrumb-v3.zip` (8.8 Ko)

### Interface
- **1 page scrollable** avec 5 sections claires
- Section 1 : Articles de blog (breadcrumb cliquable)
- Section 2 : Pages (hiérarchie automatique)
- Section 3 : Formations/CPT (avec choix taxonomie)
- Section 4 : Réglages globaux (texte, séparateur, JSON-LD)
- Section 5 : Comment l'utiliser (3 méthodes avec boutons copier)

### UX
- Langage simple sans jargon technique
- Explications concrètes sous chaque breadcrumb
- Aperçu en temps réel des modifications
- Interactions intuitives (hover, clic, modal)
- Feedback visuel immédiat

### Validation
- ✅ Approuvé par l'utilisateur
- Interface claire pour clients finaux
- Shortcode et code PHP facilement accessibles
- Compréhension immédiate de ce qu'on peut personnaliser

## [1.2.1] - 2026-03-19

### Ajouté
- Documentation complète du système de versioning
- Fichier `VERSIONING.md` avec guide d'utilisation des tags

## [1.2.0] - 2026-03-19

### Ajouté
- Documentation comparative entre prototype v1 et v2
- Fichier CHANGELOG.md pour suivi des versions
- Tags Git pour versioning sémantique

### Documentation
- `COMPARAISON-PROTOTYPES.md` : analyse détaillée des deux approches UX

## [1.1.0] - 2026-03-19

### Ajouté
- **Prototype v2 simplifié** : refonte complète de l'UX
- Interface en une seule page avec 3 onglets
- Onglet "Exemples concrets" avec 6 cas d'usage visuels avant/après
- Onglet "Personnaliser" avec configuration directe par type de contenu
- Onglet "Réglages" avec options globales simplifiées
- Aperçu en temps réel du breadcrumb
- Archive `custom-breadcrumb-v2.zip` (7.6 Ko)

### Modifié
- Renommage du plugin : "CDC WP Custom Breadcrumbs" → "Custom Breadcrumb"
- Suppression du concept abstrait de "règles"
- Langage simplifié sans jargon technique

### Exemples visuels
- Articles de blog avec catégories
- Formations avec parcours
- Pages hiérarchiques
- Agences locales avec région/ville
- Produits WooCommerce avec catégories
- Archives de taxonomies

## [1.0.0] - 2026-03-19

### Ajouté
- **Prototype UX complet** (version 1)
- Interface admin sur 5 pages distinctes
- Tableau de bord avec statistiques
- Gestion des règles avec filtres et recherche
- Formulaire de création de règle avec builder
- Page de réglages globaux
- Système de backups et exports
- CSS moderne et responsive
- JavaScript interactif
- Archive `cdc-breadcrumbs-prototype.zip` (15 Ko)
- Documentation `GUIDE-TEST-PROTOTYPE.md`

### Documentation
- Guide d'installation et test du prototype
- README prototype avec instructions détaillées

## [0.2.0] - 2026-03-19

### Ajouté
- **Scaffold du plugin WordPress fonctionnel**
- Fichier principal `cdc-wp-custom-breadcrumbs.php`
- Architecture MVC (includes, admin, public)
- Classe `CDC_WP_Custom_Breadcrumbs_Plugin` (singleton)
- Classe `CDC_WP_Custom_Breadcrumbs_Admin` (interface admin)
- Classe `CDC_WP_Custom_Breadcrumbs_Public` (rendu frontend)
- Rendu HTML du breadcrumb avec navigation sémantique
- Génération JSON-LD BreadcrumbList pour SEO
- Support des contextes : posts, pages, CPT, taxonomies, archives, auteurs, recherche, 404
- Shortcode `[cdc_breadcrumbs]`
- Fonction PHP `cdc_wp_custom_breadcrumbs()`
- Page de réglages dans l'admin WordPress
- Fichier `uninstall.php` pour désinstallation propre
- README.md avec liens vers webAnalyste et Formations Analytics

### Sécurité
- Protection ABSPATH sur tous les fichiers
- Vérification `manage_options` pour l'admin
- Sanitation des entrées utilisateur
- Échappement des sorties HTML

## [0.1.0] - 2026-03-19

### Ajouté
- Initialisation du dépôt Git
- Configuration du remote GitHub
- Cahier des charges complet (894 lignes)
- Commit initial de sauvegarde

### Infrastructure
- Dépôt Git local initialisé
- Remote configuré : https://github.com/webAnalyste/WP-Custom-Breadcrumb
- Branche principale : `main`

---

## Légende des types de changements

- **Ajouté** : nouvelles fonctionnalités
- **Modifié** : changements dans des fonctionnalités existantes
- **Déprécié** : fonctionnalités bientôt supprimées
- **Supprimé** : fonctionnalités supprimées
- **Corrigé** : corrections de bugs
- **Sécurité** : corrections de vulnérabilités

---

**Plugin développé par [webAnalyste](https://www.webanalyste.com)** - Agence experte en data, IA et automatisation no-code  
**Formation : [Formations Analytics](https://www.formations-analytics.com)**
