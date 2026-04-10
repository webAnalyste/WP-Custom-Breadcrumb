/**
 * Custom Breadcrumb - Interface avancée avec règles
 */

(function ($) {
    'use strict';

    let rules = [];
    let currentRuleId = null;
    let segmentCounter = 0;

    function esc(str) {
        return $('<span>').text(str).html();
    }

    function buildCptOptions(selectedValue) {
        const types = (typeof customBreadcrumb !== 'undefined' && customBreadcrumb.postTypes) ? customBreadcrumb.postTypes : [];
        let html = '<option value="">— Sélectionner un CPT cible —</option>';
        types.forEach(function (pt) {
            const sel = pt.name === selectedValue ? ' selected' : '';
            html += `<option value="${esc(pt.name)}"${sel}>${esc(pt.label)} <em>(${esc(pt.name)})</em></option>`;
        });
        return html;
    }

    function buildTaxonomyOptions(selectedValue) {
        const taxes = (typeof customBreadcrumb !== 'undefined' && customBreadcrumb.taxonomies) ? customBreadcrumb.taxonomies : [];
        let html = '<option value="">— Taxonomie —</option>';
        taxes.forEach(function (tax) {
            const sel = tax.name === selectedValue ? ' selected' : '';
            html += `<option value="${esc(tax.name)}"${sel}>${esc(tax.label)} <em>(${esc(tax.name)})</em></option>`;
        });
        return html;
    }

    function isTaxHierarchical(taxName) {
        const taxes = (typeof customBreadcrumb !== 'undefined' && customBreadcrumb.taxonomies) ? customBreadcrumb.taxonomies : [];
        const tax = taxes.find(t => t.name === taxName);
        return tax ? !!tax.hierarchical : false;
    }

    function buildOperatorOptions(selectedOp) {
        const ops = [
            { v: '=',  l: '=' },
            { v: '>',  l: '&gt;' },
            { v: '>=', l: '&gt;=' },
            { v: '<=', l: '&lt;=' },
            { v: '<',  l: '&lt;' },
        ];
        return ops.map(o => `<option value="${o.v}"${(selectedOp || '=') === o.v ? ' selected' : ''}>${o.l}</option>`).join('');
    }

    function addConditionRow($container, data) {
        data = data || {};
        const condType = data.type === 'page_level' ? 'page_level' : 'tax_cross';
        const showTax  = condType !== 'page_level';
        const showPage = condType === 'page_level';
        const srcHier  = isTaxHierarchical(data.source_tax || '');
        const srcDepth = (data.source_depth !== undefined && data.source_depth !== null) ? data.source_depth : '';

        const $row = $(`
            <div class="dyn-condition">
                <select class="dyn-cond-type">
                    <option value="tax_cross"${showTax ? ' selected' : ''}>Taxo croisée</option>
                    <option value="page_level"${showPage ? ' selected' : ''}>Niveau de page</option>
                </select>

                <span class="dyn-cond-tax-cross"${showTax ? '' : ' style="display:none"'}>
                    <span class="dyn-cond-label">Post courant dans</span>
                    <select class="dyn-source-tax">${buildTaxonomyOptions(data.source_tax || '')}</select>
                    <span class="dyn-source-depth-wrap"${srcHier ? '' : ' style="display:none"'}>
                        niv.&nbsp;<input type="number" class="dyn-source-depth small-text" min="0" max="20" placeholder="auto" value="${esc(String(srcDepth))}">
                    </span>
                    <span class="dyn-cond-label">=&nbsp;CPT cible dans</span>
                    <select class="dyn-target-tax">${buildTaxonomyOptions(data.target_tax || '')}</select>
                </span>

                <span class="dyn-cond-page-level"${showPage ? '' : ' style="display:none"'}>
                    <span class="dyn-cond-label">Profondeur page courante</span>
                    <select class="dyn-page-op">${buildOperatorOptions(data.operator || '=')}</select>
                    <input type="number" class="dyn-page-level-val small-text" min="0" max="20" value="${data.value !== undefined ? esc(String(data.value)) : '0'}">
                    <span class="description">(0&nbsp;= page racine)</span>
                </span>

                <button type="button" class="button-link dyn-remove-condition" title="Supprimer cette condition">🗑️</button>
            </div>
        `);
        $container.append($row);
    }

    $(document).ready(function () {

        // Charger les règles existantes
        loadRules();

        // Gestion des onglets
        $('.cb-tab').on('click', function () {
            const tab = $(this).data('tab');
            $('.cb-tab').removeClass('active');
            $(this).addClass('active');
            $('.cb-tab-content').removeClass('active');
            $(`.cb-tab-content[data-content="${tab}"]`).addClass('active');
        });

        // Ouvrir modal nouvelle règle
        $('.cb-add-rule').on('click', function () {
            currentRuleId = null;
            resetRuleForm();
            $('#modal-title').text('Nouvelle règle de breadcrumb');
            $('#rule-modal').addClass('active');
        });

        // Fermer modal
        $('.cb-modal-close, .cb-modal-overlay').on('click', function () {
            $('#rule-modal').removeClass('active');
        });

        // Ajouter un segment
        $('#add-segment').on('click', function () {
            addSegment();
        });

        // Changer type de segment
        $(document).on('change', '.segment-type', function () {
            updateSegmentFields($(this).closest('.cb-segment'));
        });

        // Ajouter une condition dynamique
        $(document).on('click', '.dyn-add-condition', function () {
            const $condList = $(this).closest('.segment-dynamic-cpt').find('.dyn-conditions-list');
            addConditionRow($condList, {});
        });

        // Supprimer une condition dynamique
        $(document).on('click', '.dyn-remove-condition', function () {
            $(this).closest('.dyn-condition').remove();
        });

        // Changer le type de condition (taxo croisée / niveau de page)
        $(document).on('change', '.dyn-cond-type', function () {
            const $row = $(this).closest('.dyn-condition');
            const type = $(this).val();
            $row.find('.dyn-cond-tax-cross').toggle(type === 'tax_cross');
            $row.find('.dyn-cond-page-level').toggle(type === 'page_level');
        });

        // Changer la taxo source → afficher/masquer le sélecteur de niveau
        $(document).on('change', '.dyn-source-tax', function () {
            const $row = $(this).closest('.dyn-condition');
            const taxName = $(this).val();
            $row.find('.dyn-source-depth-wrap').toggle(isTaxHierarchical(taxName));
            if (!isTaxHierarchical(taxName)) {
                $row.find('.dyn-source-depth').val('');
            }
        });

        // Actions segments
        $(document).on('click', '.segment-up', function () {
            const $segment = $(this).closest('.cb-segment');
            $segment.prev('.cb-segment').before($segment);
        });

        $(document).on('click', '.segment-down', function () {
            const $segment = $(this).closest('.cb-segment');
            $segment.next('.cb-segment').after($segment);
        });

        $(document).on('click', '.segment-delete', function () {
            $(this).closest('.cb-segment').remove();
        });

        // Toggle affichage taxonomie
        $('#rule-show-taxonomy').on('change', function () {
            if ($(this).is(':checked')) {
                $('#taxonomy-selector').show();
            } else {
                $('#taxonomy-selector').hide();
            }
        });

        // Sauvegarder règle
        $('#save-rule').on('click', function () {
            saveRule();
        });

        // Sauvegarder tous les réglages
        $('#save-settings').on('click', function () {
            saveAllSettings();
        });

        // Copier code
        $('.cb-copy').on('click', function () {
            const code = $(this).data('copy');
            navigator.clipboard.writeText(code).then(function () {
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.html('✅ Copié !');
                setTimeout(function () {
                    $btn.html(originalText);
                }, 2000);
            }.bind(this));
        });

        // Preview context change
        $('#preview-context').on('change', function () {
            updatePreview();
        });

        console.log('Custom Breadcrumb Advanced loaded');
    });

    function loadRules() {
        if (typeof customBreadcrumb !== 'undefined' && customBreadcrumb.settings) {
            const settings = customBreadcrumb.settings;
            rules = settings.rules || [];
            renderRules();
        }
    }

    function renderRules() {
        const $list = $('#rules-list');
        $list.empty();

        if (rules.length === 0) {
            $('.cb-no-rules').show();
            return;
        }

        $('.cb-no-rules').hide();

        rules.forEach(function (rule, index) {
            const $card = $(`
                <div class="cb-rule-card ${rule.enabled ? '' : 'disabled'}" data-rule-id="${index}">
                    <div class="cb-rule-header">
                        <div class="cb-rule-title">
                            ${getRuleTitle(rule)}
                        </div>
                        <div class="cb-rule-actions">
                            <label class="cb-rule-toggle">
                                <input type="checkbox" ${rule.enabled ? 'checked' : ''} class="rule-toggle">
                                <span class="cb-rule-toggle-slider"></span>
                            </label>
                            <button class="button rule-edit">✏️ Modifier</button>
                            <button class="button rule-delete">🗑️ Supprimer</button>
                        </div>
                    </div>
                    <div class="cb-rule-preview">
                        ${getRulePreview(rule)}
                    </div>
                </div>
            `);

            $card.find('.rule-toggle').on('change', function () {
                rules[index].enabled = $(this).is(':checked');
                renderRules();
                saveAllSettings();
            });

            $card.find('.rule-edit').on('click', function () {
                editRule(index);
            });

            $card.find('.rule-delete').on('click', function () {
                if (confirm('Supprimer cette règle ?')) {
                    rules.splice(index, 1);
                    renderRules();
                    saveAllSettings();
                }
            });

            $list.append($card);
        });
    }

    function getRuleTitle(rule) {
        const postType = rule.postType || 'post';
        const typeLabels = {
            'post': 'Articles',
            'page': 'Pages',
            'category': 'Archives catégories',
            'tag': 'Archives étiquettes'
        };
        const label = typeLabels[postType] || esc(postType);
        const badge = (rule.insertMode === 'shortcode_only')
            ? ' <span class="cb-badge-shortcode">[shortcode]</span>'
            : '';
        return label + badge;
    }

    function getRulePreview(rule) {
        let preview = '🏠 Accueil';

        if (rule.segments && rule.segments.length > 0) {
            rule.segments.forEach(function (segment) {
                preview += ' / ';
                const customLabel = segment.label ? `<em>${esc(segment.label)}</em>` : null;
                if (segment.type === 'text') {
                    preview += customLabel || esc(segment.value);
                } else if (segment.type === 'page') {
                    preview += customLabel || `📄 Page #${esc(segment.value)}`;
                } else if (segment.type === 'archive') {
                    preview += customLabel || `📚 ${esc(segment.value)}`;
                } else if (segment.type === 'taxonomy') {
                    preview += customLabel || `🏷️ ${esc(segment.value)}`;
                } else if (segment.type === 'dynamic_cpt') {
                    const condSummary = (segment.conditions || []).map(function (c) {
                        if (c.type === 'page_level') {
                            return `page_level${esc(c.operator || '=')}${c.value !== undefined ? c.value : 0}`;
                        }
                        const depth = c.source_depth !== undefined ? `[niv.${c.source_depth}]` : '';
                        return `${esc(c.source_tax || '?')}${depth}→${esc(c.target_tax || '?')}`;
                    }).join(', ');
                    preview += customLabel || `🔧 ${esc(segment.cpt || 'CPT')} [${condSummary}]`;
                } else {
                    preview += customLabel || '?';
                }
            });
        }

        if (rule.showTaxonomy) {
            preview += ` / 🏷️ ${esc(rule.taxonomy || 'Taxonomie')}`;
        }

        preview += ' / Page actuelle';

        return preview;
    }

    function resetRuleForm() {
        $('#rule-post-type').val('post');
        $('#rule-enabled').prop('checked', true);
        $('input[name="rule-insert-mode"][value="auto"]').prop('checked', true);
        $('#rule-show-parents').prop('checked', false);
        $('#rule-show-taxonomy').prop('checked', false);
        $('#taxonomy-selector').hide();
        $('#segments-container').empty();
        segmentCounter = 0;
    }

    function editRule(index) {
        currentRuleId = index;
        const rule = rules[index];

        $('#modal-title').text('Modifier la règle');
        $('#rule-post-type').val(rule.postType || 'post');
        $('#rule-enabled').prop('checked', rule.enabled !== false);
        $('input[name="rule-insert-mode"][value="' + (rule.insertMode || 'auto') + '"]').prop('checked', true);
        $('#rule-show-parents').prop('checked', rule.showParents || false);
        $('#rule-show-taxonomy').prop('checked', rule.showTaxonomy || false);

        if (rule.showTaxonomy) {
            $('#taxonomy-selector').show();
            $('#rule-taxonomy').val(rule.taxonomy || 'category');
        }

        $('#segments-container').empty();
        if (rule.segments && rule.segments.length > 0) {
            rule.segments.forEach(function (segment) {
                addSegment(segment);
            });
        }

        $('#rule-modal').addClass('active');
    }

    function addSegment(data) {
        const templateEl = document.getElementById('segment-template');
        const clone = templateEl.content.cloneNode(true);
        const $template = $(clone.querySelector('.cb-segment'));
        const segmentId = 'segment-' + (++segmentCounter);

        $template.attr('data-segment-id', segmentId);

        if (data) {
            $template.find('.segment-type').val(data.type || 'text');
            $template.find('.segment-label').val(data.label || '');

            if (data.type === 'text') {
                $template.find('.segment-text').val(data.value || '');
            } else if (data.type === 'page') {
                $template.find('.segment-page').val(data.value || '');
            } else if (data.type === 'archive') {
                $template.find('.segment-archive').val(data.value || '');
            } else if (data.type === 'taxonomy') {
                $template.find('.segment-taxonomy').val(data.value || '');
            } else if (data.type === 'dynamic_cpt') {
                const $dynPanel = $template.find('.segment-dynamic-cpt');
                $dynPanel.find('.segment-dyn-cpt').html(buildCptOptions(data.cpt || ''));
                const $condList = $dynPanel.find('.dyn-conditions-list');
                $condList.empty();
                if (data.conditions && data.conditions.length > 0) {
                    data.conditions.forEach(function (cond) {
                        // Compatibilité ascendante : les anciennes conditions n'ont pas de champ type
                        addConditionRow($condList, cond);
                    });
                }
            }
        }

        $('#segments-container').append($template);
        updateSegmentFields($template);
    }

    function updateSegmentFields($segment) {
        const type = $segment.find('.segment-type').val();

        $segment.find('.segment-text, .segment-page, .segment-archive, .segment-taxonomy, .segment-dynamic-cpt').hide();

        if (type === 'text') {
            $segment.find('.segment-text').show();
        } else if (type === 'page') {
            $segment.find('.segment-page').show();
        } else if (type === 'archive') {
            $segment.find('.segment-archive').show();
        } else if (type === 'taxonomy') {
            $segment.find('.segment-taxonomy').show();
        } else if (type === 'dynamic_cpt') {
            const $dynPanel = $segment.find('.segment-dynamic-cpt');
            // Peupler le select CPT s'il est vide
            if ($dynPanel.find('.segment-dyn-cpt option').length <= 1) {
                $dynPanel.find('.segment-dyn-cpt').html(buildCptOptions(''));
            }
            $dynPanel.show();
        }
    }

    function saveRule() {
        const rule = {
            postType: $('#rule-post-type').val(),
            enabled: $('#rule-enabled').is(':checked'),
            insertMode: $('input[name="rule-insert-mode"]:checked').val() || 'auto',
            showParents: $('#rule-show-parents').is(':checked'),
            showTaxonomy: $('#rule-show-taxonomy').is(':checked'),
            taxonomy: $('#rule-taxonomy').val(),
            segments: []
        };

        $('#segments-container .cb-segment').each(function () {
            const $segment = $(this);
            const type = $segment.find('.segment-type').val();
            const label = $segment.find('.segment-label').val().trim();
            let segment = null;

            if (type === 'text') {
                const value = $segment.find('.segment-text').val();
                if (value) segment = { type, value };
            } else if (type === 'page') {
                const value = $segment.find('.segment-page').val();
                if (value) segment = { type, value };
            } else if (type === 'archive') {
                const value = $segment.find('.segment-archive').val();
                if (value) segment = { type, value };
            } else if (type === 'taxonomy') {
                const value = $segment.find('.segment-taxonomy').val();
                if (value) segment = { type, value };
            } else if (type === 'dynamic_cpt') {
                const cpt = $segment.find('.segment-dyn-cpt').val();
                const conditions = [];
                $segment.find('.dyn-condition').each(function () {
                    const condType = $(this).find('.dyn-cond-type').val() || 'tax_cross';
                    if (condType === 'page_level') {
                        const op  = $(this).find('.dyn-page-op').val();
                        const val = parseInt($(this).find('.dyn-page-level-val').val() || '0', 10);
                        conditions.push({ type: 'page_level', operator: op, value: val });
                    } else {
                        const src = $(this).find('.dyn-source-tax').val();
                        const tgt = $(this).find('.dyn-target-tax').val();
                        if (src && tgt) {
                            const cond = { source_tax: src, target_tax: tgt };
                            const srcDepth = $(this).find('.dyn-source-depth').val();
                            if (srcDepth !== '') {
                                cond.source_depth = parseInt(srcDepth, 10);
                            }
                            conditions.push(cond);
                        }
                    }
                });
                // Un segment dynamic_cpt requiert au moins une condition taxo croisée
                const hasTaxCross = conditions.some(c => c.source_tax && c.target_tax);
                if (cpt && hasTaxCross) {
                    segment = { type, cpt, conditions };
                }
            }

            if (segment) {
                if (label) segment.label = label;
                rule.segments.push(segment);
            }
        });

        if (currentRuleId !== null) {
            rules[currentRuleId] = rule;
        } else {
            rules.push(rule);
        }

        renderRules();
        $('#rule-modal').removeClass('active');
        saveAllSettings();
    }

    function saveAllSettings() {
        const $btn = $('#save-settings');
        const $status = $('.cb-save-status');

        $btn.prop('disabled', true);
        $status.text('Enregistrement en cours...').removeClass('success error');

        const settings = {
            rules: rules,
            global: {
                home_label: $('#home-label').val(),
                separator: $('#separator').val(),
                enable_jsonld: $('#enable-jsonld').is(':checked'),
                insert_position: $('#insert-position').val(),
                alignment: $('#alignment').val(),
                keep_settings_on_uninstall: $('#keep-settings-on-uninstall').is(':checked')
            }
        };

        $.ajax({
            url: customBreadcrumb.ajaxUrl,
            type: 'POST',
            data: {
                action: 'custom_breadcrumb_save',
                nonce: customBreadcrumb.nonce,
                settings: JSON.stringify(settings)
            },
            success: function (response) {
                if (response.success) {
                    $status.text('✅ Modifications enregistrées !').addClass('success');
                } else {
                    $status.text('❌ Erreur : ' + (response.data.message || 'Erreur inconnue')).addClass('error');
                }
                $btn.prop('disabled', false);

                setTimeout(function () {
                    $status.text('');
                }, 3000);
            },
            error: function () {
                $status.text('❌ Erreur de connexion').addClass('error');
                $btn.prop('disabled', false);
            }
        });
    }

    function updatePreview() {
        // Mise à jour de l'aperçu selon le contexte sélectionné
        const context = $('#preview-context').val();
        // TODO: Implémenter la génération d'aperçu selon les règles
    }

    // ── EXPORT ──────────────────────────────────────────────────────────────
    $('#export-settings').on('click', function () {
        const settings = {
            rules: rules,
            global: {
                home_label: $('#home-label').val(),
                separator: $('#separator').val(),
                enable_jsonld: $('#enable-jsonld').is(':checked'),
                insert_position: $('#insert-position').val(),
                alignment: $('#alignment').val(),
                keep_settings_on_uninstall: $('#keep-settings-on-uninstall').is(':checked')
            }
        };

        const json = JSON.stringify(settings, null, 2);
        const blob = new Blob([json], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const date = new Date().toISOString().slice(0, 10);

        const $a = $('<a>')
            .attr('href', url)
            .attr('download', `custom-breadcrumb-${date}.json`)
            .appendTo('body');
        $a[0].click();
        $a.remove();
        URL.revokeObjectURL(url);
    });

    // ── IMPORT ──────────────────────────────────────────────────────────────
    let importData = null;

    $('#import-pick').on('click', function () {
        $('#import-file').trigger('click');
    });

    $('#import-file').on('change', function () {
        const file = this.files[0];
        if (!file) return;

        $('#import-filename').text(file.name);
        $('#import-settings').prop('disabled', true);
        importData = null;

        const reader = new FileReader();
        reader.onload = function (e) {
            try {
                const parsed = JSON.parse(e.target.result);
                if (!parsed || typeof parsed !== 'object') throw new Error('Format invalide');
                importData = parsed;
                $('#import-settings').prop('disabled', false);
            } catch (err) {
                $('#import-filename').text('❌ Fichier invalide : ' + err.message);
            }
        };
        reader.readAsText(file);
    });

    $('#import-settings').on('click', function () {
        if (!importData) return;

        if (!confirm('Importer cette configuration ? La configuration actuelle sera remplacée.')) return;

        const $btn = $(this);
        $btn.prop('disabled', true).text('Importation…');

        $.ajax({
            url: customBreadcrumb.ajaxUrl,
            type: 'POST',
            data: {
                action: 'custom_breadcrumb_save',
                nonce: customBreadcrumb.nonce,
                settings: JSON.stringify(importData)
            },
            success: function (response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    alert('❌ Erreur : ' + (response.data.message || 'Erreur inconnue'));
                    $btn.prop('disabled', false).text('📤 Importer et enregistrer');
                }
            },
            error: function () {
                alert('❌ Erreur de connexion');
                $btn.prop('disabled', false).text('📤 Importer et enregistrer');
            }
        });
    });

    // ── RESET ────────────────────────────────────────────────────────────────
    $('#reset-settings').on('click', function () {
        if (!confirm('Supprimer définitivement toutes les règles et réglages ?\n\nCette action est irréversible. Exportez d\'abord si nécessaire.')) return;
        if (!confirm('Êtes-vous certain ? Toute la configuration sera effacée.')) return;

        const $btn = $(this);
        const $status = $('#reset-status');

        $btn.prop('disabled', true).text('Suppression…');

        $.ajax({
            url: customBreadcrumb.ajaxUrl,
            type: 'POST',
            data: {
                action: 'custom_breadcrumb_reset',
                nonce: customBreadcrumb.nonce
            },
            success: function (response) {
                if (response.success) {
                    $status.text('✅ Configuration effacée. Rechargement…').addClass('success');
                    setTimeout(function () { window.location.reload(); }, 1500);
                } else {
                    $status.text('❌ Erreur').addClass('error');
                    $btn.prop('disabled', false).text('🗑️ Tout réinitialiser');
                }
            },
            error: function () {
                $status.text('❌ Erreur de connexion').addClass('error');
                $btn.prop('disabled', false).text('🗑️ Tout réinitialiser');
            }
        });
    });

})(jQuery);
