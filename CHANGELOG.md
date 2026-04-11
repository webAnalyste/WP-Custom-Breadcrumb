# Changelog

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Versioning Sémantique](https://semver.org/lang/fr/).

## [2.1.32] - 2026-04-11

### Corrigé
- fix: mode chaîne — segment externe trouvait une agence au même niveau taxo (même terme) au lieu du vrai parent ; auto-injection d'un filtre `tax_level_compare >` en mode chaîne pour n'accepter que des candidats taxonomiquement plus plats que la source

## [2.1.31] - 2026-04-11

### Corrigé
- ux: option 🔗 chaîne illisible — ajout du CSS manquant (fond ambré, texte brun, bordure) + description raccourcie à une ligne

## [2.1.30] - 2026-04-11

### Ajouté
- feat: mode **chaîne** (`chain`) sur les segments Personnalisé — le segment externe requête depuis le post trouvé par le segment interne, et non depuis la page courante. Résout le problème du 3ᵉ niveau où deux `dynamic_cpt` empilés renvoyaient des candidats incohérents car ils requêtaient tous deux depuis le même post WP.
- La logique `dynamic_cpt` est extraite dans `resolve_dynamic_cpt_post()` (réutilisable) et `pre_resolve_chained_segments()` (résolution en ordre inverse pour construire la chaîne).

## [2.1.29] - 2026-04-11

### Modifié
- ux: suppression du pavé de texte statique sous les conditions dynamic_cpt — remplacé par l'aide contextuelle par condition
- ux: aide contextuelle `tax_match` mode exact + taxonomie hiérarchique : explication du champ "niv." (vide = auto, 0 = racine, 1 = 2ᵉ niveau…)

## [2.1.28] - 2026-04-11

### Corrigé
- **fix critique** : les segments de breadcrumb n'étaient plus sauvegardés si leur configuration était incomplète au moment de l'enregistrement (page non sélectionnée, CPT sans conditions…). Désormais tous les segments sont conservés même partiels — le Builder PHP ignore les segments vides côté frontend, mais l'UI les affiche pour que l'utilisateur puisse les corriger.

## [2.1.27] - 2026-04-11

### Ajouté
- feat: aide contextuelle par condition dans l'interface dynamic_cpt — chaque condition affiche son rôle (QUERY / FILTRE / GARDE), sa logique, et un avertissement si les taxonomies sont incompatibles (cross-taxonomy mismatch)
- fix: masquage de `source_depth` corrigé pour le mode `ancestors_or_equal` (était visible à tort)

## [2.1.26] - 2026-04-11

### Corrigé
- fix: mode `ancestors_or_equal` — tri primaire par qualité de correspondance (terme exact > ancêtre) avant le tri par page_level, évitant qu'un post non-lié mais au même page_level gagne par ordre WP_Query

## [2.1.25] - 2026-04-10

### Ajouté
- feat: nouveau mode `= terme identique OU ancêtre` (`ancestors_or_equal`) dans les conditions `tax_match` — permet de trouver le post parent hiérarchique le plus proche quelle que soit la profondeur taxonomique, combiné avec `tax_level_compare`
- fix: avec `= terme ancêtre` seul, les posts au même niveau taxonomique n'étaient pas candidats (ex : "Agence Web Analytics" non trouvé depuis "Agence Google Analytics" car même terme, pas ancêtre)
- ux: tooltips et exemple concret ajoutés dans les conditions dynamic_cpt (description avec cas page_level + solution_category)

## [2.1.24] - 2026-04-10

### Ajouté
- fix: segment dynamic_cpt accepte page_level/tax_level_compare sans condition tax_match obligatoire

## [2.1.23] - 2026-04-10

### Ajouté
- fix: tax_level_compare — taxo plate (level 1/2/3) + tri par proximité + UX tooltips

## [2.1.22] - 2026-04-10

### Ajouté
- fix: option ancestors — guillemet parasite empêchait la sélection à la restauration

## [2.1.21] - 2026-04-10

### Ajouté
- fix: mode ancêtre taxo dans dynamic_cpt + exclusion post courant de la query

## [2.1.20] - 2026-04-10

### Ajouté
- feat: condition tax_level_compare — compare profondeurs taxo post courant vs post cible

## [2.1.19] - 2026-04-10

### Ajouté
- feat: conditions dynamic_cpt — niveau de page (page_level) et profondeur taxo hiérarchique (source_depth)

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
