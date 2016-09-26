/*jslint browser: true, white: true, plusplus: true, regexp: true, indent: 4, maxerr: 50, es5: true */
/*jshint multistr: true, latedef: nofunc */
/*global jQuery, $, Swiper*/

(function() {
    'use strict';

$(document).ready(function () {

    $( 'textarea' ).keyup();

//  contact form 7
    $(this).on('click', '.wpcf7-not-valid-tip', function(){
        $(this).prev().trigger('focus');
        $(this).fadeOut(500,function(){
            $(this).remove();
        });
    })

// auto adjust the height of
    .on( 'keyup', 'textarea', function (){
        $(this).height( 0 );
        $(this).height( this.scrollHeight );
    })

//numeric input
    .on('click', '.input-number-box i', function(){
        var t = $(this),
            input = t.siblings('input'),
            val = Number(input.val());
        if(t.hasClass('input-number-more')) {
            parseInt(input.val(val + 1), 1);
        } else if(t.hasClass('input-number-less') && val.length !== '' && val > 1) {
            parseInt(input.val(val - 1), 1);
        }
        input.trigger('change');
        return false;
    })

//  close wpa search_box
    .on('click', '.search_box i', function(){
        $(this).fadeOut(250).prev().fadeOut(250).parent().next().find('>div').html('');
        $(this).next().val('');
    });


//  hamburger menu
    $('.nav-icon').click(function(){
        $(this).toggleClass('is-active');
        return false;
    });

//  WP AJAX Search

    $('.searchform form[role="search"]').submit(function(){return false;});

    $('.search_box input[type="text"]').donetyping(function(){
        var t = $('.search_box input[type="text"]'),
            val = t.val(),
            form = t.parent();
        if(val.length >= 3) {
            $.ajax({
                type: 'post',
                url: $('body').data('a'),
                data: {
                    action: 'wpa_ajax_search',
                    key: val
                },
                beforeSend: function(){
                    $('img, .close', form).fadeIn(250);
                    form.next().find('.showsearch').html('<mark>Searching...</mark>');
                },
                success: function(html) {
                    form.next().find('.showsearch').html(html);
                    $('img', form).fadeOut(250);
                }
            });
        } else {
            form.next().find('.showsearch').html('<mark>Enter 3 or more letters</mark>');
            $('img', form).fadeOut(250);
        }
    });

    //Custom select
    $('select').selbel();

    $(document).on('change', 'select', function(){
        setTimeout(function(){
            $('select').selbel();
        }, 500);
    });

    $(document).ajaxComplete(function(){
        $('select').selbel();
        is_numeric_input();
        loadlater();
    });

});

$(window).on('load', function(){

    //  WP Gallery extension
    $('.wpa_slideshow').each(function(){
        var t = this,
            WPASwiper = new Swiper( t, {
                nextButton          : $('.swiper-button-next', t),
                prevButton          : $('.swiper-button-prev', t),
                pagination          : $('.swiper-pagination', t),
                observer            : true,
                paginationClickable : true,
                autoHeight          : true,
                speed               : 500
            });
    });

    //  fluid video (iframe)
    $('.content article iframe').each(function(i) {
        var t = $(this),
            p = t.parent();
        if (p.is('p') && !p.hasClass('fullframe')) {
            p.addClass('fullframe');
        }
    });
    $('.wp-video').each(function(){
        $('.mejs-video .mejs-inner', this).addClass('fullframe');
    });

})
.bind('orientationchange resize', function(){
    window.console.log('resize');
}).resizeEnd(function(){
    window.console.log('resizeEnd');
});

}(jQuery));
