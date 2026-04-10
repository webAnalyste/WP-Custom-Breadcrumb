# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Release workflow

Use the release script for versioned releases — it handles version bumps, zip creation, git tag, push, and GitHub release in one step:

```bash
./release.sh 2.1.16 "Description des changements"
```

For manual releases (as done in this session):
1. Bump `* Version:` and `CUSTOM_BREADCRUMB_VERSION` in `custom-breadcrumb.php`
2. Add entry to `CHANGELOG.md`
3. Create zip: `zip -r custom-breadcrumb-X.X.X.zip . --exclude ".git/*" --exclude "*.zip" --exclude ".DS_Store"`
4. Commit, push, then: `gh release create vX.X.X custom-breadcrumb-X.X.X.zip --title "..." --notes "..."`

The GitHub Release (not just the git tag) is required for WordPress auto-update to work — the updater calls `api.github.com/repos/.../releases/latest` and downloads the zip attached to that release.

## Architecture

The plugin follows a strict pipeline: **Context → Builder → Renderer**.

### Data flow

```
custom-breadcrumb.php          Singleton entry point, shortcode + hook registration
  └─ Custom_Breadcrumb_Config  Reads/writes wp_options ('custom_breadcrumb_settings')
  └─ Custom_Breadcrumb_Renderer
       ├─ render()             Called per request (shortcode, auto-insert, or PHP)
       │    ├─ new Context()   Detects WP context (singular/taxonomy/archive/404…)
       │    └─ new Builder()   Builds items array from rules or fallback logic
       └─ render_jsonld()      Independent call on wp_head (separate Builder instance)
```

### Rules system (key concept)

Settings are stored as a JSON blob in `wp_options`. The `rules` array is the core:

```json
{
  "rules": [
    {
      "postType": "agence",
      "enabled": true,
      "insertMode": "auto",
      "segments": [
        { "type": "page", "value": "42", "label": "" },
        { "type": "dynamic_cpt", "cpt": "expertise", "conditions": [
          { "source_tax": "secteur", "target_tax": "secteur" }
        ]}
      ],
      "showTaxonomy": false,
      "showParents": false
    }
  ],
  "global": { "home_label": "Accueil", "separator": "/", ... }
}
```

`Builder::find_applicable_rule()` matches the current WP context against rules. If a rule matches, `build_from_rule()` runs. If no rules exist at all, fallback auto-detection runs. If rules exist but none match → empty breadcrumb (no render).

### Segment types in `Builder::add_segment()`

| Type | Resolves to |
|---|---|
| `text` | Static label, no link |
| `page` | WordPress page by ID |
| `archive` | CPT archive URL |
| `taxonomy` | Current post's term(s) in given taxonomy, with ancestors |
| `dynamic_cpt` | Finds a post of target CPT via `WP_Query` + `tax_query` (conditions AND logic) |

`dynamic_cpt` is the only type without a `value` field — it uses `cpt` + `conditions[]`. The early return guard `if (empty($value)) return;` is bypassed for this type.

### Admin JS ↔ PHP data contract

`class-admin.php` passes to JS via `wp_localize_script('customBreadcrumb', {...})`:
- `settings` — full settings array (rules + global)
- `postTypes` — `[{name, label}]` for all public post types (used to populate CPT selectors)
- `taxonomies` — `[{name, label}]` for all public taxonomies (used in dynamic_cpt conditions)

The JS saves back via AJAX (`action: custom_breadcrumb_save`) as `JSON.stringify(settings)`. `class-config.php` sanitizes recursively with `sanitize_text_field` on all leaf values.

### Auto-update

`class-updater.php` hooks into `pre_set_site_transient_update_plugins`. It fetches `releases/latest` from GitHub API and compares with `CUSTOM_BREADCRUMB_VERSION`. The download URL is constructed as:
```
https://github.com/webAnalyste/WP-Custom-Breadcrumb/releases/download/v{version}/custom-breadcrumb-{version}.zip
```
The zip must be attached as a release asset (not just committed to the repo).

## Segment `insertMode` behaviour

- `auto` — respects the global `insert_position` setting (hook or filter)
- `shortcode_only` — ignored by auto-insert hooks, only renders via `[custom_breadcrumb]` or `custom_breadcrumb()`

Rules with `shortcode_only` are skipped in `find_applicable_rule()` when `$mode === 'auto'`. The renderer passes `mode = 'shortcode'` when called from the shortcode/PHP function.
