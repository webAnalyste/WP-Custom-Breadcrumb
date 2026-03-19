# Custom Breadcrumb - Prototype v2 Simplifié

## 🎯 Objectif

Interface **ultra-simple et visuelle** qui montre concrètement ce qu'on peut personnaliser avec des exemples avant/après.

## ✨ Changements majeurs

### ❌ Supprimé (trop complexe)
- Notion abstraite de "règles"
- Builder technique avec conditions
- Priorités et conflits
- Système de backup complexe
- Interface multi-pages

### ✅ Ajouté (simple et clair)
- **Onglet "Exemples concrets"** : 6 cas d'usage visuels avec avant/après
- **Onglet "Personnaliser"** : Configuration directe par type de contenu
- **Onglet "Réglages"** : Options globales simples
- Aperçu en temps réel du breadcrumb
- Interface en une seule page

## 📋 Exemples concrets montrés

1. **Articles de blog** : Accueil > Le Mag > [Catégorie] > Article
2. **Formations** : Accueil > Formations > [Parcours] > Formation
3. **Pages** : Hiérarchie complète des parents
4. **Agences locales** : Avec région et ville
5. **Produits WooCommerce** : Avec catégories
6. **Archives** : Avec hiérarchie de taxonomies

## 🚀 Installation

```bash
# Créer le ZIP
cd prototype-v2
zip -r ../custom-breadcrumb-v2.zip . -x "*.DS_Store"

# Installer dans WordPress
# Extensions > Ajouter > Téléverser
# Activer "Custom Breadcrumb"
```

## 💡 Philosophie UX

**Montrer plutôt qu'expliquer** :
- Exemples visuels avant/après
- Aperçu en temps réel
- Options avec explications concrètes
- Pas de jargon technique

**Simplicité** :
- Une page, 3 onglets
- Configuration par type de contenu
- Pas de concepts abstraits
- Actions claires

## 🎨 Interface

### Onglet 1 : Exemples concrets
Grille de 6 cartes montrant des cas d'usage réels avec comparaison avant/après

### Onglet 2 : Personnaliser
- Liste des types de contenu (articles, formations, pages...)
- Configuration visuelle avec aperçu en direct
- Options simples avec explications

### Onglet 3 : Réglages
- Texte "Accueil"
- Choix du séparateur (/, >, », →)
- Activation JSON-LD
- Mode d'insertion

## 📝 Retours attendus

- L'interface est-elle plus claire ?
- Les exemples aident-ils à comprendre ?
- La configuration est-elle intuitive ?
- Manque-t-il des cas d'usage ?

---

**Développé par [webAnalyste](https://www.webanalyste.com)** - Agence data, IA & automatisation no-code  
**Formation : [Formations Analytics](https://www.formations-analytics.com)**
