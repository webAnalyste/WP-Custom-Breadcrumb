/**
 * CDC WP Custom Breadcrumbs - Prototype UX Scripts
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        console.log('CDC Breadcrumbs Prototype UX loaded');

        $('.cdc-breadcrumbs-prototype').on('click', 'button, .button', function(e) {
            var $btn = $(this);
            var action = $btn.text().trim();
            
            if (action.includes('Enregistrer') || action.includes('Créer') || action.includes('Restaurer')) {
                e.preventDefault();
                
                $btn.prop('disabled', true);
                var originalText = $btn.html();
                $btn.html('<span class="dashicons dashicons-update spin"></span> Traitement...');
                
                setTimeout(function() {
                    $btn.html('<span class="dashicons dashicons-yes"></span> ' + action + ' réussi !');
                    $btn.css('background', '#1a7f37');
                    
                    setTimeout(function() {
                        $btn.html(originalText);
                        $btn.prop('disabled', false);
                        $btn.css('background', '');
                    }, 2000);
                }, 1500);
            }
        });

        $('select.cdc-select').on('change', function() {
            console.log('Filtre modifié:', $(this).val());
        });

        $('input[type="search"].cdc-input').on('input', function() {
            console.log('Recherche:', $(this).val());
        });

        $('.cdc-breadcrumb-item').each(function(index) {
            $(this).css('animation', 'fadeInUp 0.3s ease-out ' + (index * 0.1) + 's both');
        });
    });

    var style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .spin {
            animation: spin 1s linear infinite;
            display: inline-block;
        }
    `;
    document.head.appendChild(style);

})(jQuery);
