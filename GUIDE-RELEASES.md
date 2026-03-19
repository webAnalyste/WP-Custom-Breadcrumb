# Guide des releases GitHub pour mise à jour automatique WordPress

## 🎯 Système de mise à jour automatique

Le plugin Custom Breadcrumb utilise un système de mise à jour automatique depuis GitHub.

WordPress vérifie automatiquement les nouvelles versions sur GitHub et propose la mise à jour dans l'admin.

## 📋 Comment créer une nouvelle version

### 1. Modifier le numéro de version

**Dans `custom-breadcrumb.php` :**
```php
* Version: 2.1.0  // Changer ici
```

**Et dans la constante :**
```php
define('CUSTOM_BREADCRUMB_VERSION', '2.1.0');  // Et ici
```

### 2. Mettre à jour le CHANGELOG.md

Ajouter la nouvelle version en haut du fichier :
```markdown
## [2.1.0] - 2026-03-19

### Ajouté
- Nouvelle fonctionnalité X
- Amélioration Y

### Corrigé
- Bug Z
```

### 3. Commiter les changements

```bash
git add custom-breadcrumb.php CHANGELOG.md
git commit -m "chore: bump version to 2.1.0"
git push origin main
```

### 4. Créer le tag Git

```bash
git tag -a v2.1.0 -m "v2.1.0 - Description des changements"
git push origin v2.1.0
```

### 5. Créer la release GitHub

**Option A : Via l'interface GitHub**
1. Aller sur https://github.com/webAnalyste/WP-Custom-Breadcrumb/releases
2. Cliquer sur "Draft a new release"
3. Choisir le tag `v2.1.0`
4. Titre : `v2.1.0 - Description courte`
5. Description : Copier le contenu du CHANGELOG pour cette version
6. Publier la release

**Option B : Via GitHub CLI**
```bash
gh release create v2.1.0 \
  --title "v2.1.0 - Description courte" \
  --notes "$(cat CHANGELOG.md | sed -n '/## \[2.1.0\]/,/## \[2.0.0\]/p' | head -n -1)"
```

## 🔄 Comment WordPress détecte les mises à jour

1. WordPress interroge l'API GitHub : `https://api.github.com/repos/webAnalyste/WP-Custom-Breadcrumb/releases/latest`
2. Compare la version installée avec la dernière release
3. Si nouvelle version disponible → affiche la notification de mise à jour
4. L'utilisateur clique sur "Mettre à jour"
5. WordPress télécharge : `https://github.com/webAnalyste/WP-Custom-Breadcrumb/archive/refs/tags/v2.1.0.zip`
6. Installe automatiquement la nouvelle version

## 📊 Versioning sémantique

**Format : MAJEUR.MINEUR.CORRECTIF**

- **MAJEUR** (2.0.0 → 3.0.0) : Changements incompatibles
- **MINEUR** (2.0.0 → 2.1.0) : Nouvelles fonctionnalités compatibles
- **CORRECTIF** (2.0.0 → 2.0.1) : Corrections de bugs

### Exemples

**Correction de bug :**
```
2.0.0 → 2.0.1
```

**Nouvelle fonctionnalité :**
```
2.0.0 → 2.1.0
```

**Refonte majeure :**
```
2.0.0 → 3.0.0
```

## ✅ Checklist pour une nouvelle version

- [ ] Modifier le numéro de version dans `custom-breadcrumb.php` (2 endroits)
- [ ] Mettre à jour `CHANGELOG.md`
- [ ] Tester le plugin en local
- [ ] Commiter les changements
- [ ] Créer le tag Git `v2.x.x`
- [ ] Pousser le tag sur GitHub
- [ ] Créer la release GitHub avec notes
- [ ] Vérifier que la mise à jour apparaît dans WordPress (peut prendre quelques minutes)

## 🧪 Tester la mise à jour

1. Installer la version actuelle du plugin dans WordPress
2. Créer une nouvelle release sur GitHub
3. Dans WordPress : Extensions > Mises à jour
4. Vérifier que la nouvelle version apparaît
5. Cliquer sur "Mettre à jour maintenant"
6. Vérifier que la mise à jour s'installe correctement

## 🔧 Dépannage

**La mise à jour n'apparaît pas dans WordPress :**
- Vider le cache WordPress : `wp transient delete --all`
- Vérifier que le tag existe sur GitHub
- Vérifier que la release est publiée (pas en draft)
- Attendre quelques minutes (cache WordPress)

**Erreur lors de la mise à jour :**
- Vérifier les permissions fichiers
- Vérifier que l'URL du ZIP est accessible
- Consulter les logs WordPress

## 📝 Exemple complet

```bash
# 1. Modifier les versions dans le code
# 2. Mettre à jour CHANGELOG.md

# 3. Commiter
git add .
git commit -m "chore: bump version to 2.1.0"

# 4. Créer le tag
git tag -a v2.1.0 -m "v2.1.0 - Support WooCommerce + corrections bugs"

# 5. Pousser
git push origin main
git push origin v2.1.0

# 6. Créer la release sur GitHub
# (via interface web ou CLI)
```

---

**Plugin développé par [webAnalyste](https://www.webanalyste.com)**  
**Formation : [Formations Analytics](https://www.formations-analytics.com)**
