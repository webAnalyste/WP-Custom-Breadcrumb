/**
 * Custom Breadcrumb - Interface simplifiée
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Gestion des onglets
        $('.cb-tab').on('click', function() {
            var tabId = $(this).data('tab');
            
            $('.cb-tab').removeClass('cb-tab--active');
            $(this).addClass('cb-tab--active');
            
            $('.cb-tab-content').removeClass('cb-tab-content--active');
            $('#tab-' + tabId).addClass('cb-tab-content--active');
        });

        // Simulation des boutons
        $('.cb-wrap').on('click', 'button.button-primary', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var originalText = $btn.html();
            
            $btn.prop('disabled', true);
            $btn.html('⏳ Enregistrement...');
            
            setTimeout(function() {
                $btn.html('✅ Enregistré !');
                $btn.css('background', '#00a32a');
                
                setTimeout(function() {
                    $btn.html(originalText);
                    $btn.prop('disabled', false);
                    $btn.css('background', '');
                }, 1500);
            }, 800);
        });

        // Toggle des sections de configuration
        $('.cb-config-header').on('click', function() {
            var $body = $(this).next('.cb-config-body');
            $body.toggleClass('cb-config-body--collapsed');
        });

        // Mise à jour en temps réel de l'aperçu
        $('.cb-option input[type="text"]').on('input', function() {
            var value = $(this).val();
            if (value) {
                $('.cb-crumb--editable').text(value);
            }
        });

        // Animation des cartes d'exemples
        $('.cb-example-card').each(function(index) {
            $(this).css({
                'opacity': '0',
                'transform': 'translateY(20px)'
            });
            
            setTimeout(function(card) {
                $(card).css({
                    'opacity': '1',
                    'transform': 'translateY(0)',
                    'transition': 'all 0.4s ease-out'
                });
            }, index * 100, this);
        });

        console.log('Custom Breadcrumb prototype v2 chargé');
    });

})(jQuery);
