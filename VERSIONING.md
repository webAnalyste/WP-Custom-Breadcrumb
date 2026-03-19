# Système de versioning

## 📌 Versioning sémantique

Ce projet utilise le [Versioning Sémantique 2.0.0](https://semver.org/lang/fr/)

Format : `MAJEUR.MINEUR.CORRECTIF`

- **MAJEUR** : changements incompatibles avec les versions précédentes
- **MINEUR** : ajout de fonctionnalités rétrocompatibles
- **CORRECTIF** : corrections de bugs rétrocompatibles

## 🏷️ Tags Git disponibles

```bash
# Lister tous les tags
git tag -l

# Voir les détails d'un tag
git show v1.1.0

# Checkout d'une version spécifique
git checkout v1.1.0
```

### Versions publiées

| Tag | Date | Description |
|-----|------|-------------|
| **v1.2.0** | 2026-03-19 | Documentation comparaison prototypes |
| **v1.1.0** | 2026-03-19 | Prototype v2 simplifié avec exemples visuels |
| **v1.0.0** | 2026-03-19 | Prototype UX complet (5 sections) |
| **v0.2.0** | 2026-03-19 | Scaffold plugin WordPress fonctionnel |
| **v0.1.0** | 2026-03-19 | Initialisation du projet |

## 📦 Releases GitHub

Chaque tag correspond à une release sur GitHub :
https://github.com/webAnalyste/WP-Custom-Breadcrumb/releases

### Télécharger une version spécifique

```bash
# Via Git
git clone --branch v1.1.0 https://github.com/webAnalyste/WP-Custom-Breadcrumb.git

# Via GitHub
https://github.com/webAnalyste/WP-Custom-Breadcrumb/archive/refs/tags/v1.1.0.zip
```

## 🔄 Workflow de versioning

### Créer une nouvelle version

```bash
# 1. Faire les modifications
git add .
git commit -m "feat: nouvelle fonctionnalité"

# 2. Créer le tag
git tag -a v1.3.0 -m "v1.3.0 - Description de la version"

# 3. Pousser le commit et le tag
git push origin main
git push origin v1.3.0

# 4. Mettre à jour CHANGELOG.md
# Ajouter la section [1.3.0] avec les changements
git add CHANGELOG.md
git commit -m "docs: mise à jour CHANGELOG pour v1.3.0"
git push origin main
```

### Convention de commits

Format : `type(scope): description`

**Types** :
- `feat` : nouvelle fonctionnalité
- `fix` : correction de bug
- `docs` : documentation uniquement
- `style` : formatage, point-virgules manquants, etc.
- `refactor` : refactorisation du code
- `test` : ajout ou modification de tests
- `chore` : maintenance, build, etc.
- `build` : changements du système de build
- `ci` : changements de CI/CD

**Exemples** :
```bash
git commit -m "feat: ajout du support des taxonomies personnalisées"
git commit -m "fix: correction du breadcrumb sur les archives"
git commit -m "docs: mise à jour du README avec exemples"
git commit -m "refactor: simplification de la classe Admin"
```

## 📋 Historique complet

Voir `CHANGELOG.md` pour l'historique détaillé de toutes les versions.

## 🚀 Prochaines versions prévues

### v1.3.0 (à venir)
- Validation finale du prototype v2
- Ajustements UX selon retours utilisateur

### v2.0.0 (à venir)
- Développement métier complet
- Implémentation du moteur de règles
- Interface admin fonctionnelle
- Système de sauvegarde/restauration

### v2.1.0 (à venir)
- Hooks et filtres pour développeurs
- Documentation technique complète
- Tests unitaires

## 📊 Compatibilité

| Version plugin | WordPress min | PHP min |
|---------------|---------------|---------|
| 1.x.x | 6.4+ | 7.4+ |
| 2.x.x | 6.4+ | 7.4+ |

---

**Plugin développé par [webAnalyste](https://www.webanalyste.com)**  
**Formation : [Formations Analytics](https://www.formations-analytics.com)**
