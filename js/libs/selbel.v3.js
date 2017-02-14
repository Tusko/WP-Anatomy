//selbel
(function($) {
    'use strict';
    var select = {},
        defaults = {
            onChange: function () {}
        };
    $.fn.selbel = function(options) {

        if (this.length > 1) {
            this.each(function () {
                $(this).selbel(options);
            });
            return this;
        }
        var el = this;
        var init = function(){

            select.settings = $.extend({}, defaults, options);

            var sel_label = el.attr("data-label") !== undefined ? '<label>' + el.attr("data-label") + '</label>' : '';
            if (!el.hasClass('selbel')) { el.addClass('selbel'); }
            if(el.parent().is('.selbel_w')) {
                var txt;
                if($(':selected', el).length > 0) {
                    txt = $(':selected', el);
                } else {
                    txt = $('option:first', el);
                }
                el.next().text( txt.text() );
                return false;
            }
            el.each(function() {
                $(this).wrap("<span class='selbel_w' />")
                    .before(select.label )
                    .after('<span>' + $('*:selected', this).text() + '</span>');

                if( $('*:selected', this).val() == '' ) {
                    el.addClass('not_selected');
                } else {
                    el.removeClass('not_selected');
                }
            });
            el.change(function() {
                $(this).next().text($('*:selected', this).text());
                if( $('*:selected', this).val() == '' ) {
                    el.addClass('not_selected');
                } else {
                    el.removeClass('not_selected');
                }
                select.settings.onChange( el );
            });
        };

        init();

        // returns the current jQuery object
        return this;
    };
}(jQuery));