jQuery(document).ready(function ($) {

    function tt_update_alt_field($attachment){
        $('#wrapper-' + $attachment + ' .waiting').show();
        var alt_text = $('#alt-' + $attachment).val();

        $.post(ajaxurl, {
            action: 'tt_media_alt_update',
            'post_id': $attachment,
            'alt_text': alt_text
        },
               function (alt) {
            if (alt) {
                $('#wrapper-' + $attachment + ' .waiting').hide();
            }
        }
              );
    }

    $(this).on('keydown', '.tt-m-alt input.large-text', function(event){
        if(event.keyCode == 13) {
            $(this).blur();
            return false;
        }
    }).on('blur', '.tt-m-alt input.large-text', function () {
        tt_update_alt_field( $(this).attr("id").replace('alt-', '') );
        return false;
    });

    /*
    //qtranxsList hack for custom text input
    //source input has to have class qtranxs-translatable for this to work
    var qtranslatable_inputs_init = function(){
        var selector = ".qtranxs-translatable";
        jQuery(selector).each(function(index){
            var _this = $(this),
                input_id = _this.attr("id"),
                input_class = _this.attr("class"),
                integrated = _this.attr('value');
//              splitted = qtranxf_split(integrated);
            jQuery.each(qtranxsList.enabled_languages, function(value){
                var new_id = input_id+'_'+qtranxsList.enabled_languages[value];
                _this.before('<label for="'+new_id+'">'+qtranxsList.enabled_languages[value]+'</label>');
                _this.before('<input id="'+new_id+'" type="text" class="'+input_class+'" value="'+qtranxsList.enabled_languages[value]+'" >');
                var lang_input = jQuery('#'+new_id);
                _this.css( "display", "none" );
                lang_input.on("input", null, null,function(){
                    jQuery.each(qtranxsList.enabled_languages, function(v){
                        var _new_id = input_id+'_'+qtranxsList.enabled_languages[v];
                        var _lang_input = jQuery('#'+_new_id);
                        _this.attr("value", qtrans_integrate(qtranxsList.enabled_languages[v],_lang_input.attr('value'),_this.attr('value')));
                    });
                });
            });
        });
    }

//    qtranslatable_inputs_init();
    */

});
