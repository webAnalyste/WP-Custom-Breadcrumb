# 🐛 Debug condition `tax_similarity`

## Problème : La condition ne fonctionne pas

### ✅ Checklist de diagnostic

#### 1. **Vérifier les noms de taxonomies**

❌ **Erreur fréquente** : Utiliser des noms incorrects

```json
{
  "source_tax": "categorie",      // ❌ FAUX
  "target_tax": "expertise_categorie"  // ❌ FAUX
}
```

✅ **Correction** : Utiliser les noms exacts WordPress

```json
{
  "source_tax": "category",       // ✅ BON (taxonomie WP par défaut)
  "target_tax": "expertise_category"  // ✅ BON (si c'est le nom réel)
}
```

**Comment vérifier les taxonomies disponibles ?**

Dans WordPress, ajouter temporairement ce code dans `functions.php` :

```php
add_action('init', function() {
    if (is_admin() && current_user_can('manage_options')) {
        $taxonomies = get_taxonomies(['public' => true], 'objects');
        error_log('=== TAXONOMIES DISPONIBLES ===');
        foreach ($taxonomies as $tax) {
            error_log(sprintf('- %s (label: %s)', $tax->name, $tax->label));
        }
    }
});
```

Puis consulter `wp-content/debug.log` (si `WP_DEBUG_LOG = true`).

---

#### 2. **Vérifier que les posts ont des termes**

**Post courant** (`expertise_complement`) doit avoir au moins un terme dans `source_tax`  
**Post candidat** (`expertise`) doit avoir au moins un terme dans `target_tax`

**Comment vérifier ?**

Dans l'admin WordPress :
1. Éditer le post `expertise_complement` courant
2. Vérifier qu'il a bien une **catégorie** assignée
3. Éditer un post `expertise` candidat
4. Vérifier qu'il a bien un terme dans la taxonomie `expertise_category`

---

#### 3. **Consulter les logs de debug**

Les logs détaillés sont visibles dans le **code source HTML** du breadcrumb.

**Comment voir les logs ?**

1. Visiter la page `expertise_complement` sur le frontend
2. Faire **Ctrl+U** (ou clic droit → "Afficher le code source")
3. Chercher `<!-- CB-DEBUG`

**Logs possibles** :

```html
<!-- CB-DEBUG:
  candidat #42 "Mon expertise" REJETÉ: taxonomie source "categorie" invalide sur post #123 (WP_Error: Invalid taxonomy.)
-->
```
→ **Problème** : Taxonomie `categorie` n'existe pas, utiliser `category`

```html
<!-- CB-DEBUG:
  candidat #42 "Mon expertise" REJETÉ: post courant #123 n'a aucun terme dans taxonomie "category"
-->
```
→ **Problème** : Le post courant n'a pas de catégorie assignée

```html
<!-- CB-DEBUG:
  candidat #42 "Mon expertise" REJETÉ: candidat n'a aucun terme dans taxonomie "expertise_category"
-->
```
→ **Problème** : Le candidat n'a pas de terme dans `expertise_category`

```html
<!-- CB-DEBUG:
  candidat #42 "Mon expertise" OK: similarité 100.0% >= 80% ("Google Analytics" vs "Google Analytics", taxo category/expertise_category)
-->
```
→ **Succès** : Candidat retenu, similarité 100%

---

#### 4. **Vérifier la configuration JSON**

**Ta configuration actuelle** :

```json
{
  "type": "tax_similarity",
  "source_tax": "categorie",           // ❌ Probablement faux
  "target_tax": "expertise_categorie", // ❌ Probablement faux
  "threshold": 80
}
```

**Configuration corrigée probable** :

```json
{
  "type": "tax_similarity",
  "source_tax": "category",            // ✅ Taxonomie WP par défaut
  "target_tax": "expertise_category",  // ✅ À vérifier selon ton CPT
  "threshold": 80
}
```

---

## 🔧 Correction pas à pas

### Étape 1 : Identifier les taxonomies réelles

**Méthode 1** : Via l'admin WordPress

1. Aller dans **Réglages → Custom Breadcrumb**
2. Créer/modifier une règle pour `expertise_complement`
3. Ajouter un segment **Personnalisé (dynamic_cpt)**
4. Ajouter une condition **Similarité termes taxo**
5. Les **sélecteurs** affichent les taxonomies disponibles

**Méthode 2** : Via le code source de la page admin

1. Aller dans **Réglages → Custom Breadcrumb**
2. Ouvrir la console développeur (F12)
3. Taper dans la console :
   ```javascript
   jQuery('.dyn-sim-source-tax option').each(function() {
       console.log(this.value, this.text);
   });
   ```
4. Noter les noms exacts des taxonomies

---

### Étape 2 : Vérifier les termes assignés

**Pour le post courant** (`expertise_complement`) :

```php
// Ajouter temporairement dans functions.php
add_action('wp', function() {
    if (is_singular('expertise_complement')) {
        $post_id = get_the_ID();
        $terms = get_the_terms($post_id, 'category'); // Remplacer par ta taxonomie
        error_log('=== POST COURANT #' . $post_id . ' ===');
        if (is_wp_error($terms)) {
            error_log('ERREUR: ' . $terms->get_error_message());
        } elseif (empty($terms)) {
            error_log('Aucun terme dans "category"');
        } else {
            foreach ($terms as $term) {
                error_log('- Terme: ' . $term->name . ' (ID: ' . $term->term_id . ')');
            }
        }
    }
});
```

Consulter `wp-content/debug.log`.

---

### Étape 3 : Tester avec des termes identiques

**Pour simplifier le debug**, commence avec des termes **exactement identiques** :

1. Post courant (`expertise_complement`) : catégorie = **"Google Analytics"**
2. Post candidat (`expertise`) : terme `expertise_category` = **"Google Analytics"**

**Résultat attendu** : Similarité = **100%** → candidat retenu

Si ça ne fonctionne toujours pas, le problème vient des **noms de taxonomies**.

---

### Étape 4 : Corriger la configuration

**Dans l'admin WordPress** :

1. Aller dans **Réglages → Custom Breadcrumb**
2. Modifier la règle pour `expertise_complement`
3. Modifier le segment **Personnalisé**
4. Modifier la condition **Similarité termes taxo**
5. **Sélectionner** les bonnes taxonomies dans les listes déroulantes
6. **Enregistrer**

**Ou via JSON** (si tu modifies directement la BDD) :

```json
{
  "type": "tax_similarity",
  "source_tax": "category",            // ✅ Nom exact
  "target_tax": "expertise_category",  // ✅ Nom exact
  "threshold": 80
}
```

---

## 🧪 Test de validation

### Configuration de test minimale

```json
{
  "rules": [
    {
      "postType": "expertise_complement",
      "enabled": true,
      "insertMode": "shortcode_only",
      "segments": [
        {
          "type": "dynamic_cpt",
          "cpt": "expertise",
          "conditions": [
            {
              "type": "tax_similarity",
              "source_tax": "category",
              "target_tax": "expertise_category",
              "threshold": 80
            }
          ]
        }
      ]
    }
  ]
}
```

### Données de test

**Post courant** :
- Type : `expertise_complement`
- ID : 123
- Catégorie (`category`) : "Google Analytics"

**Post candidat** :
- Type : `expertise`
- ID : 42
- Terme (`expertise_category`) : "Google Analytics"

### Résultat attendu

**Breadcrumb** :
```
🏠 Accueil / 🔧 [Titre du post expertise #42]
```

**Debug log** :
```html
<!-- CB-DEBUG:
  candidat #42 "Titre expertise" OK: similarité 100.0% >= 80% ("Google Analytics" vs "Google Analytics", taxo category/expertise_category)
-->
```

---

## ❓ Questions fréquentes

### Q1 : Pourquoi "categorie" ne fonctionne pas ?

**R** : WordPress utilise `"category"` (en anglais) par défaut, pas `"categorie"`. Les noms de taxonomies sont **sensibles à la casse et à l'orthographe**.

---

### Q2 : Comment savoir si ma taxonomie personnalisée existe ?

**R** : Utiliser ce snippet dans `functions.php` :

```php
add_action('init', function() {
    if (taxonomy_exists('expertise_category')) {
        error_log('✅ Taxonomie "expertise_category" existe');
    } else {
        error_log('❌ Taxonomie "expertise_category" N\'EXISTE PAS');
    }
}, 999);
```

---

### Q3 : Les termes sont identiques mais la similarité est < 100% ?

**R** : Vérifier :
- **Espaces** : "Google Analytics" ≠ "Google Analytics " (espace en fin)
- **Casse** : Normalisée automatiquement (OK)
- **Accents** : "café" ≠ "cafe" (similarité ~80%)
- **Caractères invisibles** : Copier-coller peut introduire des caractères cachés

---

### Q4 : Aucun log de debug visible ?

**R** : Vérifier que :
1. Le breadcrumb s'affiche (même vide)
2. Le segment `dynamic_cpt` est bien configuré
3. La règle est **activée** (toggle ON)
4. Le `insertMode` n'est pas `disabled`

---

## 🚀 Solution rapide

**Si tu es pressé**, utilise cette configuration **garantie** :

```json
{
  "type": "tax_similarity",
  "source_tax": "category",
  "target_tax": "category",
  "threshold": 100
}
```

Cela compare la **même taxonomie** (`category`) entre le post courant et le candidat, avec un seuil de **100%** (termes identiques).

Si ça fonctionne → le problème vient des noms de taxonomies personnalisées.  
Si ça ne fonctionne pas → le problème est ailleurs (règle désactivée, post sans catégorie, etc.).

---

**Développé par [webAnalyste](https://www.webanalyste.com)** — Agence experte en Analytics, Data, Automatisation et IA  
**Formation** : [formations-analytics.com](https://www.formations-analytics.com)
