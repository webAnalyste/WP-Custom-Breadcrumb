#!/bin/bash

# Script de release automatique pour Custom Breadcrumb
# Usage: ./release.sh 2.1.0 "Description de la version"

set -e

if [ -z "$1" ]; then
    echo "❌ Erreur: Numéro de version requis"
    echo "Usage: ./release.sh 2.1.0 \"Description de la version\""
    exit 1
fi

VERSION=$1
DESCRIPTION=${2:-"Version $VERSION"}
TAG="v$VERSION"

echo "🚀 Création de la release $VERSION"
echo ""

# 1. Vérifier que nous sommes sur main
BRANCH=$(git rev-parse --abbrev-ref HEAD)
if [ "$BRANCH" != "main" ]; then
    echo "❌ Vous devez être sur la branche main"
    exit 1
fi

# 2. Vérifier qu'il n'y a pas de modifications non commitées
if [ -n "$(git status --porcelain)" ]; then
    echo "❌ Il y a des modifications non commitées"
    git status --short
    exit 1
fi

# 3. Mettre à jour la version dans custom-breadcrumb.php
echo "📝 Mise à jour de la version dans custom-breadcrumb.php..."
sed -i '' "s/\* Version: .*/\* Version: $VERSION/" custom-breadcrumb.php
sed -i '' "s/define('CUSTOM_BREADCRUMB_VERSION', '.*');/define('CUSTOM_BREADCRUMB_VERSION', '$VERSION');/" custom-breadcrumb.php

# 4. Mettre à jour CHANGELOG.md
echo "📝 Mise à jour du CHANGELOG.md..."
DATE=$(date +%Y-%m-%d)
TEMP_FILE=$(mktemp)

cat > "$TEMP_FILE" << EOF
# Changelog

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Versioning Sémantique](https://semver.org/lang/fr/).

## [$VERSION] - $DATE

### Ajouté
- $DESCRIPTION

EOF

# Ajouter le reste du changelog existant (en sautant les 7 premières lignes)
tail -n +8 CHANGELOG.md >> "$TEMP_FILE"
mv "$TEMP_FILE" CHANGELOG.md

# 5. Commiter les changements
echo "💾 Commit des changements..."
git add custom-breadcrumb.php CHANGELOG.md
git commit -m "chore: bump version to $VERSION"

# 6. Créer le tag
echo "🏷️  Création du tag $TAG..."
git tag -a "$TAG" -m "$TAG - $DESCRIPTION"

# 7. Créer le ZIP versionné
echo "📦 Création du ZIP custom-breadcrumb-$VERSION.zip..."
TEMP_DIR=$(mktemp -d)
PLUGIN_DIR="$TEMP_DIR/custom-breadcrumb"

mkdir -p "$PLUGIN_DIR"

# Copier les fichiers nécessaires
cp custom-breadcrumb.php "$PLUGIN_DIR/"
cp uninstall.php "$PLUGIN_DIR/"
cp -r includes "$PLUGIN_DIR/"
cp -r admin "$PLUGIN_DIR/"
cp -r assets "$PLUGIN_DIR/"
cp README-PLUGIN.md "$PLUGIN_DIR/README.md"

# Créer le ZIP
cd "$TEMP_DIR"
zip -r "custom-breadcrumb-$VERSION.zip" custom-breadcrumb -x "*.DS_Store"
mv "custom-breadcrumb-$VERSION.zip" "$OLDPWD/"
cd "$OLDPWD"

# Nettoyer
rm -rf "$TEMP_DIR"

echo "✅ ZIP créé: custom-breadcrumb-$VERSION.zip"

# 8. Pousser sur GitHub
echo "⬆️  Push sur GitHub..."
git push origin main
git push origin "$TAG"

# 9. Créer la release GitHub
echo "🎉 Création de la release GitHub..."

# Extraire le changelog de cette version
CHANGELOG_CONTENT=$(sed -n "/## \[$VERSION\]/,/## \[/p" CHANGELOG.md | sed '$d' | tail -n +3)

if command -v gh &> /dev/null; then
    gh release create "$TAG" \
        "custom-breadcrumb-$VERSION.zip" \
        --title "$TAG - $DESCRIPTION" \
        --notes "$CHANGELOG_CONTENT"
    echo "✅ Release GitHub créée avec succès"
else
    echo "⚠️  GitHub CLI (gh) non installé"
    echo "📋 Créez manuellement la release sur:"
    echo "   https://github.com/webAnalyste/WP-Custom-Breadcrumb/releases/new"
    echo "   Tag: $TAG"
    echo "   Fichier: custom-breadcrumb-$VERSION.zip"
fi

echo ""
echo "✅ Release $VERSION terminée !"
echo ""
echo "📦 Fichiers créés:"
echo "   - custom-breadcrumb-$VERSION.zip"
echo ""
echo "🔗 GitHub:"
echo "   - Tag: $TAG"
echo "   - Release: https://github.com/webAnalyste/WP-Custom-Breadcrumb/releases/tag/$TAG"
echo ""
echo "🎯 WordPress détectera automatiquement cette version"
