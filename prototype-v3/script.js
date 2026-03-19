/**
 * Custom Breadcrumb v3 - UX Simple
 */

(function($) {
    'use strict';

    let currentEditElement = null;

    $(document).ready(function() {
        
        // Édition des éléments cliquables
        $('.cb-crumb-editable, .cb-crumb-home').on('click', function() {
            currentEditElement = $(this);
            const currentText = $(this).find('.cb-text').text();
            
            $('#cb-edit-modal').addClass('active');
            $('.cb-modal-input').val(currentText).focus();
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
                if (newText) {
                    currentEditElement.find('.cb-text').text(newText);
                    
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
            
            // Copier dans le presse-papier
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

        // Bouton enregistrer
        $('.cb-save-btn').on('click', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const originalText = $btn.html();
            
            $btn.prop('disabled', true);
            $btn.html('⏳ Enregistrement en cours...');
            
            setTimeout(function() {
                $btn.html('✅ Modifications enregistrées !');
                $btn.css('background', '#10b981');
                
                setTimeout(function() {
                    $btn.html(originalText);
                    $btn.prop('disabled', false);
                    $btn.css('background', '');
                }, 2500);
            }, 1200);
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

        console.log('Custom Breadcrumb v3 - UX Designer loaded');
    });

})(jQuery);
