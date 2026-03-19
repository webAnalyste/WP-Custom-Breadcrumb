/**
 * Custom Breadcrumb - Interface avancée avec règles
 */

(function ($) {
    'use strict';

    let rules = [];
    let currentRuleId = null;
    let segmentCounter = 0;

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
        return typeLabels[postType] || postType;
    }

    function getRulePreview(rule) {
        let preview = '🏠 Accueil';

        if (rule.segments && rule.segments.length > 0) {
            rule.segments.forEach(function (segment) {
                preview += ' / ';
                const customLabel = segment.label ? `<em>${segment.label}</em>` : null;
                if (segment.type === 'text') {
                    preview += customLabel || segment.value;
                } else if (segment.type === 'page') {
                    preview += customLabel || `📄 Page #${segment.value}`;
                } else if (segment.type === 'archive') {
                    preview += customLabel || `📚 ${segment.value}`;
                } else if (segment.type === 'taxonomy') {
                    preview += customLabel || `🏷️ ${segment.value}`;
                } else {
                    preview += customLabel || '🔧 Personnalisé';
                }
            });
        }

        if (rule.showTaxonomy) {
            preview += ` / 🏷️ ${rule.taxonomy || 'Taxonomie'}`;
        }

        preview += ' / Page actuelle';

        return preview;
    }

    function resetRuleForm() {
        $('#rule-post-type').val('post');
        $('#rule-enabled').prop('checked', true);
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
            }
        }

        $('#segments-container').append($template);
        updateSegmentFields($template);
    }

    function updateSegmentFields($segment) {
        const type = $segment.find('.segment-type').val();

        $segment.find('.segment-text, .segment-page, .segment-archive, .segment-taxonomy').hide();

        if (type === 'text') {
            $segment.find('.segment-text').show();
        } else if (type === 'page') {
            $segment.find('.segment-page').show();
        } else if (type === 'archive') {
            $segment.find('.segment-archive').show();
        } else if (type === 'taxonomy') {
            $segment.find('.segment-taxonomy').show();
        }
    }

    function saveRule() {
        const rule = {
            postType: $('#rule-post-type').val(),
            enabled: $('#rule-enabled').is(':checked'),
            showParents: $('#rule-show-parents').is(':checked'),
            showTaxonomy: $('#rule-show-taxonomy').is(':checked'),
            taxonomy: $('#rule-taxonomy').val(),
            segments: []
        };

        $('#segments-container .cb-segment').each(function () {
            const $segment = $(this);
            const type = $segment.find('.segment-type').val();
            const label = $segment.find('.segment-label').val().trim();
            let value = '';

            if (type === 'text') {
                value = $segment.find('.segment-text').val();
            } else if (type === 'page') {
                value = $segment.find('.segment-page').val();
            } else if (type === 'archive') {
                value = $segment.find('.segment-archive').val();
            } else if (type === 'taxonomy') {
                value = $segment.find('.segment-taxonomy').val();
            }

            if (value) {
                const segment = { type, value };
                if (label) {
                    segment.label = label;
                }
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
                alignment: $('#alignment').val()
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

})(jQuery);
