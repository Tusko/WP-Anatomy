/*jslint browser: true, white: true, plusplus: true, regexp: true, indent: 4, maxerr: 50, es5: true */
/*jshint multistr: true, latedef: nofunc */
/*global jQuery, $, CustomEvent, Swiper, is_numeric_input, loadlater, mob*/

$(document).ready(function(){
    'use strict';

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
    $('select').selectric({
        maxHeight: (mob()?200:350),
        disableOnMobile: false,
        nativeOnMobile: false
    });

    $(document).ajaxComplete(function(){
        //Custom select
        $('select').selectric({
            maxHeight: (mob()?200:350),
            disableOnMobile: false,
            nativeOnMobile: false
        });
        is_numeric_input();
        loadlater();
    });

    //  fluid video (iframe)
    $('.content article iframe').each(function() {
        var t = $(this),
            p = t.parent();
        if (p.is('p') && !p.hasClass('fullframe')) {
            p.addClass('fullframe');
        }
    });

    $('.wp-video').each(function(){
        $('.mejs-video .mejs-inner', this).addClass('fullframe');
    });

    $(this).on("pageshow visibilitychange",function(){
        $(window).triggerHandler('resize');
    });

    cssDeffered().then(function(){
        $(window).triggerHandler('defer.cssLoad');
    });

});

$(window).on('defer.cssLoad', function(){
    'use strict';

    //  WP Gallery extension
    window.WPASwiper = [];
    $('.wpa_slideshow').each(function(i){
        var t = this,
            checklength = ($('.swiper-slide', t).length > 1),
            isPagin = false;
        if(checklength) {
            isPagin = {
                el              : $('.swiper-pagination', t)[0],
                type            : 'bullets',
                clickable       : true
            };
        }
        setTimeout(function(){
            window.WPASwiper[i] = new Swiper( t, {
                navigation: {
                    nextEl          : $('.swiper-button-next', t)[0],
                    prevEl          : $('.swiper-button-prev', t)[0]
                },
                simulateTouch               : isPagin,
                pagination                  : isPagin,
                roundLengths                : true,
                observer                    : true,
                observeParents              : true,
                grabCursor                  : checklength,
                autoHeight                  : true,
                speed                       : 500,
                preloadImages               : false,
                lazy                        : {
                    loadPrevNext    : true
                }
            });
        }, 1e3*i);
    });

})
.bind('orientationchange resize', function(){
    'use strict';
//    window.console.log('resize');
}).resizeEnd(function(){
    'use strict';
//    window.console.log('resizeEnd');
});