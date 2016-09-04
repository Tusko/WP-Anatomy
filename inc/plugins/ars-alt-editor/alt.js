/*jslint browser: true, white: true, vars: true, plusplus: true, regexp: true, indent: 4, maxerr: 50 */
/*global $, jQuery, qTranslateConfig, ajaxurl*/

jQuery(document).ready(function ($) {
    'use strict';

    function wpa_update_alt_field($attachment){
        var alt_id = $('#wpa_mc_' + $attachment);
        alt_id.next().show();
        var alt_text = $('#wpa_mc_' + $attachment).val();

        if( $('#wpa_mc_' + $attachment).parents('.tt-m-alt').find('input').length > 1 ) {
            var langs = qTranslateConfig.qtx.getLanguages();
            alt_text = '';
            $.each(langs, function(lang){
                alt_text += '[:' + lang + ']' + $('input[name="qtranslate-fields[wpa_mc_qtx]['+lang+']"]').val();
            });
            alt_text += '[:]';
        }

        $.post(ajaxurl, {
            action: 'wpa_media_alt_update',
            'post_id': $attachment,
            'alt_text': alt_text
        }, function (alt) {
            if (alt) {
                alt_id.next().hide();
            }
        });
    }

    $(this).on('keydown', '.tt-m-alt input.wpa_mc_qtx', function(event){
        if(event.keyCode === 13) {
            $(this).blur();
            return false;
        }
    }).on('blur', '.tt-m-alt input.wpa_mc_qtx', function () {
        if( $(this).parents('.tt-m-alt').find('input').length > 1 ) {
            $('input[name="qtranslate-fields[wpa_mc_qtx][' + qTranslateConfig.activeLanguage + ']"]').val( $(this).val() );
        }
        wpa_update_alt_field( $(this).attr("id").replace('wpa_mc_', '') );
        return false;
    });

});
