# Release automatique - Mode d'emploi

## 🎯 Système 100% automatique

**Une seule commande pour tout faire :**

```bash
./release.sh 2.1.0 "Support WooCommerce + corrections bugs"
```

## ✨ Ce que fait le script automatiquement

1. ✅ Vérifie que vous êtes sur `main`
2. ✅ Vérifie qu'il n'y a pas de modifications non commitées
3. ✅ Met à jour la version dans `custom-breadcrumb.php` (2 endroits)
4. ✅ Met à jour `CHANGELOG.md` avec la nouvelle version
5. ✅ Commit les changements
6. ✅ Crée le tag Git `v2.1.0`
7. ✅ **Génère le ZIP `custom-breadcrumb-2.1.0.zip`**
8. ✅ Pousse sur GitHub (main + tag)
9. ✅ **Crée automatiquement la release GitHub avec le ZIP**
10. ✅ WordPress détecte la mise à jour automatiquement

## 📦 Fichiers générés automatiquement

Après `./release.sh 2.1.0 "Description"` :

```
custom-breadcrumb-2.1.0.zip  ← ZIP prêt à installer dans WordPress
```

Le ZIP contient :
- `custom-breadcrumb/custom-breadcrumb.php`
- `custom-breadcrumb/includes/`
- `custom-breadcrumb/admin/`
- `custom-breadcrumb/assets/`
- `custom-breadcrumb/README.md`

## 🚀 Exemples d'utilisation

### Correction de bug (2.0.0 → 2.0.1)
```bash
./release.sh 2.0.1 "Correction affichage catégories"
```

### Nouvelle fonctionnalité (2.0.0 → 2.1.0)
```bash
./release.sh 2.1.0 "Support WooCommerce"
```

### Version majeure (2.0.0 → 3.0.0)
```bash
./release.sh 3.0.0 "Refonte complète interface"
```

## 📋 Workflow complet

```bash
# 1. Développer les modifications
# 2. Tester en local
# 3. Commiter les changements de code
git add .
git commit -m "feat: nouvelle fonctionnalité"

# 4. Lancer le script de release
./release.sh 2.1.0 "Nouvelle fonctionnalité"

# ✅ C'est tout ! Le reste est automatique
```

## 🎯 Résultat dans WordPress

**Automatiquement, sans rien faire :**

1. WordPress vérifie les mises à jour (toutes les 12h)
2. Détecte la nouvelle version sur GitHub
3. Affiche "Mise à jour disponible : 2.1.0"
4. L'utilisateur clique sur "Mettre à jour"
5. WordPress télécharge `custom-breadcrumb-2.1.0.zip` depuis GitHub
6. Installe automatiquement la nouvelle version

## 🔧 Prérequis

### Installation GitHub CLI (optionnel mais recommandé)

**macOS :**
```bash
brew install gh
gh auth login
```

**Linux :**
```bash
curl -fsSL https://cli.github.com/packages/githubcli-archive-keyring.gpg | sudo dd of=/usr/share/keyrings/githubcli-archive-keyring.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | sudo tee /etc/apt/sources.list.d/github-cli.list > /dev/null
sudo apt update
sudo apt install gh
gh auth login
```

**Sans GitHub CLI :**
Le script fonctionne quand même, mais vous devrez créer manuellement la release sur GitHub et y attacher le ZIP.

## ⚠️ Important

- Le script doit être exécuté depuis la racine du projet
- Vous devez être sur la branche `main`
- Toutes les modifications doivent être commitées avant
- Le numéro de version doit suivre le format `X.Y.Z`

## 🐛 Dépannage

**Erreur : "command not found: gh"**
→ GitHub CLI non installé, le script fonctionne quand même mais vous devrez créer la release manuellement

**Erreur : "Il y a des modifications non commitées"**
→ Commiter d'abord vos changements avec `git add . && git commit -m "message"`

**Erreur : "Vous devez être sur la branche main"**
→ `git checkout main`

## 📊 Versioning sémantique

- **2.0.0 → 2.0.1** : Correction de bug
- **2.0.0 → 2.1.0** : Nouvelle fonctionnalité
- **2.0.0 → 3.0.0** : Changement majeur

---

**Plugin développé par [webAnalyste](https://www.webanalyste.com)**  
**Formation : [Formations Analytics](https://www.formations-analytics.com)**
