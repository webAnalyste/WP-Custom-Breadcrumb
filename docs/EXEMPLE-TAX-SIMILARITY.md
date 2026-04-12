# Exemple pratique : Condition `tax_similarity`

## 🎯 Scénario réel

Vous gérez un site avec plusieurs CPT interconnectés :

### Structure du site

```
📦 Site WordPress
├── 🔧 CPT "solution" (ex: Google Analytics, WordPress SEO, etc.)
│   └── Taxonomie: solution_category
├── 📝 CPT "article" (blog posts sur les solutions)
│   └── Taxonomie: article_category
└── 📚 CPT "guide" (tutoriels détaillés)
    └── Taxonomie: guide_category
```

### Problème

Les termes de taxonomies ne sont **pas exactement identiques** entre CPT :

| Solution | Article | Guide |
|----------|---------|-------|
| "Google Analytics" | "Google Analytics 4" | "GA4" |
| "WordPress" | "WordPress SEO" | "WP SEO" |
| "Data Science" | "Data Analysis" | "Analyse de données" |

❌ **Condition `tax_match` (exact)** → Aucun match  
✅ **Condition `tax_similarity`** → Match si similarité ≥ seuil

---

## 🛠️ Configuration

### Breadcrumb souhaité

Sur une page **Article** :

```
🏠 Accueil / 🔧 Solution parente / 📝 Article courant
```

### Règle dans l'admin

**Type de contenu** : `article`

**Segments** :
1. 🏠 Texte : "Accueil"
2. 🔧 **Personnalisé (dynamic_cpt)** :
   - CPT cible : `solution`
   - **Condition** :
     - Type : `Similarité termes taxo`
     - Taxonomie source : `article_category` (post courant)
     - Taxonomie cible : `solution_category` (post candidat)
     - Seuil : **80%**
3. 📝 Texte dynamique : Titre de l'article

---

## 📊 Résultat

### Exemple 1 : Match réussi

**Article courant** :
- Titre : "Tout savoir sur GA4"
- Catégorie : `article_category` → "Google Analytics 4"

**Solutions disponibles** :
- Solution #42 : "Google Analytics" → catégorie "Google Analytics"
- Solution #58 : "WordPress SEO" → catégorie "WordPress"

**Calcul de similarité** :
```
"Google Analytics 4" vs "Google Analytics" → 85.7% ✅ (≥ 80%)
"Google Analytics 4" vs "WordPress"        → 25.0% ❌ (< 80%)
```

**Breadcrumb généré** :
```
🏠 Accueil / 🔧 Google Analytics / 📝 Tout savoir sur GA4
```

**Debug log** :
```html
<!-- CB-DEBUG:
  candidat #42 "Google Analytics" OK: similarité 85.7% >= 80% ("Google Analytics 4" vs "Google Analytics", taxo article_category/solution_category)
  candidat #58 "WordPress SEO" REJETÉ: similarité 25.0% < 80% ("Google Analytics 4" vs "WordPress", taxo article_category/solution_category)
-->
```

---

### Exemple 2 : Aucun match

**Article courant** :
- Titre : "Stratégie marketing digital"
- Catégorie : `article_category` → "Marketing Digital"

**Solutions disponibles** :
- Solution #42 : "Google Analytics" → catégorie "Google Analytics"
- Solution #58 : "WordPress SEO" → catégorie "WordPress"

**Calcul de similarité** :
```
"Marketing Digital" vs "Google Analytics" → 30.0% ❌ (< 80%)
"Marketing Digital" vs "WordPress"        → 15.0% ❌ (< 80%)
```

**Breadcrumb généré** :
```
🏠 Accueil / 📝 Stratégie marketing digital
```

**Debug log** :
```html
<!-- CB-DEBUG:
  candidat #42 "Google Analytics" REJETÉ: similarité 30.0% < 80% ("Marketing Digital" vs "Google Analytics", taxo article_category/solution_category)
  candidat #58 "WordPress SEO" REJETÉ: similarité 15.0% < 80% ("Marketing Digital" vs "WordPress", taxo article_category/solution_category)
  Segment dynamic_cpt ignoré: aucun candidat trouvé
-->
```

---

## 🔗 Mode chaîne (chain=true)

### Breadcrumb complexe

Sur une page **Article** :

```
🏠 Accueil / 📚 Guide / 🔧 Solution / 📝 Article courant
```

### Configuration

**Segment 1** (externe) — **Chain activé** :
- CPT : `guide`
- Condition : `tax_similarity` entre `guide_category` et `solution_category` ≥ **75%**

**Segment 2** (interne) :
- CPT : `solution`
- Condition : `tax_similarity` entre `solution_category` et `article_category` ≥ **80%**

### Résolution

1. **Segment 2** s'exécute en premier :
   - Article courant : catégorie "Google Analytics 4"
   - Trouve Solution #42 : catégorie "Google Analytics" (similarité 85.7%)

2. **Segment 1** s'exécute avec la solution trouvée :
   - Solution #42 : catégorie "Google Analytics"
   - Trouve Guide #15 : catégorie "GA4 Guide" (similarité 76.0%)

**Breadcrumb final** :
```
🏠 Accueil / 📚 GA4 Guide / 🔧 Google Analytics / 📝 Tout savoir sur GA4
```

---

## 🎛️ Ajustement du seuil

### Seuil 90% (strict)

**Avantages** :
- Très haute précision
- Évite les faux positifs

**Inconvénients** :
- Peut rater des matches légitimes
- Breadcrumb incomplet plus fréquent

**Usage** : Termes quasi-identiques (pluriel, accents, casse)

---

### Seuil 80% (recommandé)

**Avantages** :
- Bon équilibre précision/rappel
- Tolère variations mineures (version, suffixe)

**Inconvénients** :
- Quelques faux positifs possibles

**Usage** : Termes très similaires avec variations courtes

---

### Seuil 70% (permissif)

**Avantages** :
- Maximise les matches
- Tolère variations importantes

**Inconvénients** :
- Risque de faux positifs
- Peut matcher des termes trop différents

**Usage** : Termes dans le même domaine mais formulation différente

---

## 🧪 Test de similarité

### Outil de calcul

Vous pouvez tester la similarité entre deux termes avec ce snippet PHP :

```php
function test_similarity($str1, $str2) {
    $str1 = mb_strtolower(trim($str1), 'UTF-8');
    $str2 = mb_strtolower(trim($str2), 'UTF-8');
    
    $max_len = max(mb_strlen($str1, 'UTF-8'), mb_strlen($str2, 'UTF-8'));
    $distance = levenshtein($str1, $str2);
    
    $similarity = (1 - ($distance / $max_len)) * 100;
    
    return round($similarity, 1);
}

// Tests
echo test_similarity('Google Analytics', 'Google Analytics 4');     // 85.7%
echo test_similarity('WordPress', 'WordPress SEO');                  // 75.0%
echo test_similarity('Data Science', 'Data Analysis');               // 60.0%
echo test_similarity('SEO', 'SEM');                                  // 33.3%
```

---

## 💡 Bonnes pratiques

### ✅ À faire

- **Tester différents seuils** sur vos données réelles
- **Vérifier les debug logs** pour comprendre les rejets
- **Combiner avec `tax_match`** pour des conditions hybrides :
  ```json
  {
    "conditions": [
      {
        "type": "tax_match",
        "source_tax": "post_tag",
        "target_tax": "solution_tag",
        "match_mode": "exact"
      },
      {
        "type": "tax_similarity",
        "source_tax": "category",
        "target_tax": "solution_category",
        "threshold": 80
      }
    ]
  }
  ```
  → Candidat retenu si **tag exact ET catégorie similaire**

### ❌ À éviter

- **Seuil trop bas (< 60%)** → Trop de faux positifs
- **Seuil trop haut (> 95%)** → Peu de matches, autant utiliser `tax_match` exact
- **Termes très courts (< 3 caractères)** → Similarité peu fiable (ex: "SEO" vs "SEM" = 33%)
- **Ordre des mots important** → "Data Science" ≠ "Science Data" (similarité ~70%)

---

## 📈 Cas d'usage avancés

### 1. Multilingue

**Problème** : Termes traduits non identiques

```
FR: "Analyse de données"
EN: "Data Analysis"
```

**Solution** : Créer des règles par langue avec `tax_similarity` ajusté

---

### 2. Variations orthographiques

**Problème** : Termes avec/sans accents, tirets, etc.

```
"E-commerce"
"Ecommerce"
"E commerce"
```

**Solution** : Seuil 85-90% pour tolérer ces variations

---

### 3. Versions de produits

**Problème** : Produits avec numéros de version

```
"WordPress 6.4"
"WordPress 6.5"
"WordPress 6.6"
```

**Solution** : Seuil 90-95% pour matcher toutes les versions

---

**Développé par [webAnalyste](https://www.webanalyste.com)** — Agence experte en Analytics, Data, Automatisation et IA  
**Formation** : [formations-analytics.com](https://www.formations-analytics.com) — Organisme de formation Data, Analytics, IA et Automatisation
