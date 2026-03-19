/**
 * Custom Breadcrumb - Admin fonctionnel
 */

(function($) {
    'use strict';

    let currentEditElement = null;
    let settings = customBreadcrumb.settings || {};

    $(document).ready(function() {
        
        // Édition des éléments cliquables
        $('.cb-crumb-editable, .cb-crumb-home').on('click', function() {
            currentEditElement = $(this);
            const currentText = $(this).find('.cb-text').text();
            const editType = $(this).data('edit');
            
            $('#cb-edit-modal').addClass('active');
            $('.cb-modal-input').val(currentText).focus().data('edit-type', editType);
        });

        // Fermer la modal
        $('.cb-modal-close, .cb-modal-cancel').on('click', function() {
            $('#cb-edit-modal').removeClass('active');
            currentEditElement = null;
        });

        // Sauvegarder la modification
        $('.cb-modal-save').on('click', function() {
            if (currentEditElement) {
                const newText = $('.cb-modal-input').val().trim();
                const editType = $('.cb-modal-input').data('edit-type');
                
                if (newText) {
                    currentEditElement.find('.cb-text').text(newText);
                    
                    // Mettre à jour les settings
                    updateSetting(editType, newText);
                    
                    // Animation de confirmation
                    currentEditElement.css({
                        'background': '#10b981',
                        'color': '#fff',
                        'transform': 'scale(1.05)'
                    });
                    
                    setTimeout(function() {
                        currentEditElement.css({
                            'background': '',
                            'color': '',
                            'transform': ''
                        });
                    }, 500);
                }
            }
            $('#cb-edit-modal').removeClass('active');
            currentEditElement = null;
        });

        // Enter dans la modal
        $('.cb-modal-input').on('keypress', function(e) {
            if (e.which === 13) {
                $('.cb-modal-save').click();
            }
        });

        // Escape pour fermer
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('#cb-edit-modal').removeClass('active');
            }
        });

        // Copier le code
        $('.cb-copy-btn').on('click', function() {
            const code = $(this).data('copy');
            const $btn = $(this);
            
            navigator.clipboard.writeText(code).then(function() {
                const originalText = $btn.html();
                $btn.html('✅ Copié !');
                $btn.css('background', '#059669');
                
                setTimeout(function() {
                    $btn.html(originalText);
                    $btn.css('background', '');
                }, 2000);
            });
        });

        // Gestion des checkboxes
        $('.cb-checkbox input[type="checkbox"]').on('change', function() {
            const $parent = $(this).closest('[data-setting]');
            if ($parent.length) {
                const settingPath = $parent.data('setting');
                updateSettingByPath(settingPath, $(this).is(':checked'));
            }
        });

        // Gestion des inputs texte
        $('.cb-input').on('change', function() {
            const $parent = $(this).closest('[data-setting]');
            if ($parent.length) {
                const settingPath = $parent.data('setting');
                updateSettingByPath(settingPath, $(this).val());
            }
        });

        // Gestion des radios (séparateur)
        $('input[name="sep"]').on('change', function() {
            if ($(this).is(':checked')) {
                updateSettingByPath('global.separator', $(this).val());
                updateSeparatorsDisplay($(this).val());
            }
        });

        // Bouton enregistrer
        $('.cb-save-btn').on('click', function(e) {
            e.preventDefault();
            saveSettings();
        });

        // Animation d'entrée
        $('.cb-section').each(function(index) {
            $(this).css({
                'opacity': '0',
                'transform': 'translateY(30px)'
            });
            
            setTimeout(function(section) {
                $(section).css({
                    'opacity': '1',
                    'transform': 'translateY(0)',
                    'transition': 'all 0.5s ease-out'
                });
            }, index * 100, this);
        });

        console.log('Custom Breadcrumb Admin loaded', settings);
    });

    function updateSetting(editType, value) {
        switch(editType) {
            case 'home':
                settings.global = settings.global || {};
                settings.global.home_label = value;
                break;
            case 'blog-label':
                settings.post = settings.post || {};
                settings.post.section_label = value;
                break;
            case 'formation-label':
                settings.formation = settings.formation || {};
                settings.formation.section_label = value;
                break;
        }
    }

    function updateSettingByPath(path, value) {
        const parts = path.split('.');
        let current = settings;
        
        for (let i = 0; i < parts.length - 1; i++) {
            if (!current[parts[i]]) {
                current[parts[i]] = {};
            }
            current = current[parts[i]];
        }
        
        current[parts[parts.length - 1]] = value;
    }

    function updateSeparatorsDisplay(separator) {
        $('.cb-sep').text(separator);
    }

    function saveSettings() {
        const $btn = $('.cb-save-btn');
        const originalText = $btn.html();
        
        $btn.prop('disabled', true);
        $btn.html('⏳ Enregistrement en cours...');

        $.ajax({
            url: customBreadcrumb.ajaxUrl,
            type: 'POST',
            data: {
                action: 'custom_breadcrumb_save',
                nonce: customBreadcrumb.nonce,
                settings: JSON.stringify(settings)
            },
            success: function(response) {
                if (response.success) {
                    $btn.html('✅ Modifications enregistrées !');
                    $btn.css('background', '#10b981');
                    
                    setTimeout(function() {
                        $btn.html(originalText);
                        $btn.prop('disabled', false);
                        $btn.css('background', '');
                    }, 2500);
                } else {
                    $btn.html('❌ Erreur : ' + (response.data.message || 'Erreur inconnue'));
                    $btn.css('background', '#dc2626');
                    
                    setTimeout(function() {
                        $btn.html(originalText);
                        $btn.prop('disabled', false);
                        $btn.css('background', '');
                    }, 3000);
                }
            },
            error: function() {
                $btn.html('❌ Erreur de connexion');
                $btn.css('background', '#dc2626');
                
                setTimeout(function() {
                    $btn.html(originalText);
                    $btn.prop('disabled', false);
                    $btn.css('background', '');
                }, 3000);
            }
        });
    }

})(jQuery);
