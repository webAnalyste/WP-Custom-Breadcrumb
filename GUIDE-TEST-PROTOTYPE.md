# Guide de test du prototype UX

## Installation rapide dans WordPress

### Méthode 1 : Lien symbolique (recommandé pour développement)

```bash
# Depuis le dossier du projet
ln -s "$(pwd)/prototype" /chemin/vers/wordpress/wp-content/plugins/cdc-breadcrumbs-prototype

# Exemple concret
ln -s "$(pwd)/prototype" ~/Local\ Sites/monsite/app/public/wp-content/plugins/cdc-breadcrumbs-prototype
```

### Méthode 2 : Copie directe

```bash
# Copier le dossier prototype dans WordPress
cp -r prototype /chemin/vers/wordpress/wp-content/plugins/cdc-breadcrumbs-prototype
```

### Méthode 3 : Via l'interface WordPress

1. Compresser le dossier `prototype` en ZIP
2. Renommer le ZIP en `cdc-breadcrumbs-prototype.zip`
3. Dans WordPress : Extensions > Ajouter > Téléverser une extension
4. Sélectionner le fichier ZIP et installer

## Activation

1. Aller dans **Extensions > Extensions installées**
2. Chercher **CDC Breadcrumbs [PROTOTYPE UX]**
3. Cliquer sur **Activer**
4. Un nouveau menu **CDC Breadcrumbs** apparaît dans la barre latérale admin

## Navigation dans le prototype

### 📊 Tableau de bord
- Vue d'ensemble avec statistiques clés
- Règles récentes
- Actions rapides
- Alertes et notifications

### 📋 Règles
- Liste complète des 8 règles d'exemple
- Filtres par statut et contexte
- Recherche
- Actions groupées

### ➕ Nouvelle règle
- Formulaire complet de création
- Builder de breadcrumb interactif
- Système de conditions
- Prévisualisation en temps réel
- Placeholders documentés

### ⚙️ Réglages
- Paramètres généraux
- Options de rendu HTML
- Configuration JSON-LD
- Comportement fallback
- Métadonnées autorisées

### 💾 Backups
- Historique des sauvegardes
- Export/Import de configuration
- Création de backup manuel
- Paramètres de rétention

## Points à tester

### ✅ Ergonomie
- [ ] La navigation est-elle intuitive ?
- [ ] Les libellés sont-ils clairs ?
- [ ] L'organisation des sections est-elle logique ?
- [ ] Les actions sont-elles facilement accessibles ?

### ✅ Création de règle
- [ ] Le formulaire est-il compréhensible ?
- [ ] Le builder de breadcrumb est-il utilisable ?
- [ ] Les placeholders sont-ils bien documentés ?
- [ ] La prévisualisation est-elle utile ?

### ✅ Gestion des règles
- [ ] Le tableau est-il lisible ?
- [ ] Les filtres sont-ils pertinents ?
- [ ] Les actions rapides sont-elles accessibles ?
- [ ] La structure du breadcrumb est-elle visible ?

### ✅ Réglages
- [ ] Les options sont-elles bien organisées ?
- [ ] Les descriptions sont-elles suffisantes ?
- [ ] Les valeurs par défaut sont-elles cohérentes ?

### ✅ Backups
- [ ] Le système de sauvegarde est-il clair ?
- [ ] Les actions export/import sont-elles évidentes ?
- [ ] L'historique est-il exploitable ?

## Retours attendus

Documenter vos observations sur :

1. **Fluidité du parcours utilisateur**
2. **Clarté des informations affichées**
3. **Facilité de compréhension des concepts**
4. **Éléments manquants ou superflus**
5. **Suggestions d'amélioration**

## Limitations du prototype

⚠️ **Attention** : Ce prototype est **non fonctionnel**

- Les boutons affichent une simulation visuelle mais ne sauvegardent rien
- Les formulaires ne sont pas validés côté serveur
- Aucune donnée n'est persistée en base
- Les filtres et recherches sont simulés en JavaScript
- Les règles affichées sont des exemples statiques

**Objectif** : Valider l'UX et l'ergonomie avant le développement métier complet.

## Après validation UX

Une fois le prototype testé et validé :

1. Documenter les retours et ajustements nécessaires
2. Désactiver et supprimer le plugin prototype
3. Lancer le développement métier du plugin final
4. Intégrer les retours UX dans le développement

## Support

- **Dépôt GitHub** : https://github.com/webAnalyste/WP-Custom-Breadcrumb
- **Documentation complète** : Voir `CDC WP Custom Breadcrumbs` (cahier des charges)
- **Développé par** : [webAnalyste](https://www.webanalyste.com)
- **Formation** : [Formations Analytics](https://www.formations-analytics.com)
