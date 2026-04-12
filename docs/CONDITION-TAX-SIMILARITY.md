# Condition `tax_similarity` — Similarité entre termes de taxonomies

## 📋 Vue d'ensemble

La condition **`tax_similarity`** permet de comparer la **similarité textuelle** entre les termes de deux taxonomies (identiques ou différentes) et de ne retenir que les posts candidats dont le terme est suffisamment similaire selon un seuil configurable.

**Type de condition** : 🔍 **FILTRE** (appliqué après la WP_Query)

---

## 🎯 Cas d'usage

### Exemple concret

**Contexte** : Vous avez deux taxonomies :
- `solution_category` sur le CPT `solution`
- `article_category` sur le CPT `article`

**Problème** : Les termes ne sont pas exactement identiques mais très proches :
- Solution : "Google Analytics"
- Article : "Google Analytics 4"

**Solution** : Utiliser `tax_similarity` avec un seuil de **80%** pour matcher ces termes malgré la différence.

---

## ⚙️ Configuration

### Interface admin

Dans l'éditeur de segment **Personnalisé (dynamic_cpt)**, ajouter une condition :

1. **Type de condition** : `Similarité termes taxo`
2. **Taxonomie source** : Taxonomie du post courant (ex: `solution_category`)
3. **Taxonomie cible** : Taxonomie du post candidat (ex: `article_category`)
4. **Seuil de similarité** : Pourcentage minimum (0-100%, défaut: **80%**)

### Structure JSON

```json
{
  "type": "tax_similarity",
  "source_tax": "solution_category",
  "target_tax": "article_category",
  "threshold": 80
}
```

---

## 🔬 Algorithme

### Distance de Levenshtein normalisée

La similarité est calculée via la **distance de Levenshtein** (nombre minimum d'opérations pour transformer une chaîne en une autre), normalisée sur 0-100%.

**Formule** :
```
similarité (%) = (1 - distance / max_longueur) × 100
```

### Traitement

1. **Normalisation** : Les chaînes sont converties en minuscules et trimées
2. **Calcul** : Distance de Levenshtein entre les deux noms de termes
3. **Normalisation** : Conversion en pourcentage (0-100%)
4. **Filtrage** : Candidat rejeté si `similarité < threshold`

### Exemples de similarité

| Terme 1 | Terme 2 | Similarité |
|---------|---------|------------|
| "Google Analytics" | "Google Analytics 4" | ~85% |
| "WordPress" | "WordPress SEO" | ~75% |
| "Data Science" | "Data Analysis" | ~60% |
| "Marketing" | "Marketing Digital" | ~70% |
| "SEO" | "SEM" | ~33% |

---

## 🚀 Utilisation

### Segment simple

**Objectif** : Trouver un article dont la catégorie est similaire à celle de la solution courante.

**Configuration** :
- **Type** : Personnalisé (dynamic_cpt)
- **CPT cible** : `article`
- **Conditions** :
  - Type : `tax_similarity`
  - Source : `solution_category`
  - Cible : `article_category`
  - Seuil : `80%`

### Segment chaîné (chain=true)

**Objectif** : Trouver une solution parent dont la catégorie est similaire à celle de l'article courant, puis trouver un guide dont la catégorie est similaire à celle de la solution trouvée.

**Segment 1** (externe) :
- CPT : `guide`
- Condition : `tax_similarity` entre `guide_category` et `solution_category` ≥ 75%
- **Chain** : ✅ activé

**Segment 2** (interne) :
- CPT : `solution`
- Condition : `tax_similarity` entre `solution_category` et `article_category` ≥ 80%

**Résolution** : Segment 2 → trouve solution → Segment 1 utilise cette solution pour trouver guide.

---

## 🎛️ Seuils recommandés

| Seuil | Usage | Exemple |
|-------|-------|---------|
| **90-100%** | Quasi-identique | Variations mineures (pluriel, accents) |
| **80-90%** | Très similaire | Ajout de version, suffixe court |
| **70-80%** | Similaire | Termes proches mais distincts |
| **60-70%** | Partiellement similaire | Même domaine, termes différents |
| **< 60%** | Peu fiable | Risque de faux positifs |

**Recommandation générale** : **75-85%** pour un bon équilibre précision/rappel.

---

## 🐛 Debug

### Logs HTML

Les logs de debug sont visibles dans les commentaires HTML du breadcrumb :

```html
<!-- CB-DEBUG:
  candidat #42 "Article GA4" OK: similarité 85.7% >= 80% ("Google Analytics" vs "Google Analytics 4", taxo solution_category/article_category)
-->
```

### Logs WP

Si `WP_DEBUG_LOG` est activé :

```
[Custom Breadcrumb] candidat #42 "Article GA4" OK: similarité 85.7% >= 80% ("Google Analytics" vs "Google Analytics 4", taxo solution_category/article_category)
```

---

## ⚠️ Limitations

### Fonction PHP `levenshtein()`

- **Limite** : Chaînes de **255 caractères maximum**
- **Comportement** : Retourne `-1` si dépassement → similarité = 0%
- **Solution** : Tronquer les noms de termes si nécessaire (rare en pratique)

### Performance

- **Complexité** : O(n×m) où n et m sont les longueurs des chaînes
- **Impact** : Négligeable pour des termes courts (< 50 caractères)
- **Optimisation** : Appliqué après WP_Query (sur candidats filtrés uniquement)

### Sensibilité

- **Casse** : Ignorée (normalisation en minuscules)
- **Espaces** : Trimés en début/fin
- **Accents** : **Pris en compte** (différence entre "café" et "cafe")
- **Ordre des mots** : **Important** ("Data Science" ≠ "Science Data")

---

## 🔗 Combinaison avec autres conditions

### Logique ET

Toutes les conditions d'un segment doivent être vraies :

```json
{
  "conditions": [
    {
      "type": "tax_match",
      "source_tax": "post_tag",
      "target_tax": "article_tag",
      "match_mode": "exact"
    },
    {
      "type": "tax_similarity",
      "source_tax": "category",
      "target_tax": "article_category",
      "threshold": 80
    }
  ]
}
```

**Résultat** : Candidat retenu si **tag identique ET catégorie similaire à 80%+**.

---

## 📚 Références

- **Algorithme** : [Distance de Levenshtein (Wikipedia)](https://fr.wikipedia.org/wiki/Distance_de_Levenshtein)
- **Fonction PHP** : [`levenshtein()`](https://www.php.net/manual/fr/function.levenshtein.php)
- **Normalisation** : Formule standard de similarité textuelle

---

## 🆕 Historique

- **v2.2.0** (2026-04-12) : Ajout de la condition `tax_similarity`
  - Interface admin avec sélecteurs taxonomies + seuil
  - Algorithme Levenshtein normalisé
  - Debug logs détaillés
  - Aide contextuelle avec exemples

---

**Développé par [webAnalyste](https://www.webanalyste.com)** — Agence experte en Analytics, Data, Automatisation et IA  
**Formation** : [formations-analytics.com](https://www.formations-analytics.com) — Organisme de formation Data, Analytics, IA et Automatisation
