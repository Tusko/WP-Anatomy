var selectEl;

(function ($, window) {
  $(document).ready(function () {
    'use strict';

    $('textarea').keyup();

    selectEl = $('select');

    $(this)

      // auto adjust the height of
      .on('keyup', 'textarea', function () {
        var t = $(this);
        if (t.hasClass('small')) return;
        t.height(0);
        t.height(this.scrollHeight);
      })

      //numeric input
      .on('click', '.input-number-box i', function () {
        var t = $(this),
          input = t.siblings('input'),
          val = Number(input.val());
        if (t.hasClass('input-number-more')) {
          if (typeof input.attr('max') !== 'undefined' && input.attr('max').length > 0 && val >= input.attr('max')) {
            input.val(input.attr('max'));
            return;
          }
          parseInt(input.val(val + 1), 1);
        } else if (t.hasClass('input-number-less') && val.length !== '' && val > 1) {
          parseInt(input.val(val - 1), 1);
        }
        input.trigger('change');
        return false;
      })

      //  close wpa search_box
      .on('click', '.search_box i', function () {
        $(this).fadeOut(250).prev().fadeOut(250).parent().next().find('>div').html('');
        $(this).next().val('');
      })

      .ajaxComplete(function () {
        //Custom select rebuild
        selectEl.each(function () {
          var e = $(this);
          if (typeof e.data('select2') === 'undefined') {
            $.selectInit(selectEl)
          }
        });

        is_numeric_input();
      });

//  hamburger menu
    $('.nav-icon').click(function () {
      $(this).toggleClass('is-active');
      return false;
    });

//  WP AJAX Search
    $('.searchform form[role="search"]').submit(function () {
      return false;
    });

    $(document).on('keyup', '.search_box input[type="text"]', $.debounce(350, true, function () {
      var t = $('.search_box input[type="text"]'),
        val = t.val(),
        form = t.parent();
      if (val.length >= 3) {
        $.ajax({
          type: 'post',
          url: document.body.dataset.a,
          data: {
            action: 'wpa_ajax_search',
            key: val
          },
          beforeSend: function () {
            $('img, .close', form).fadeIn(250);
            form.next().find('.showsearch').html('<mark>Searching...</mark>');
          },
          success: function (html) {
            form.next().find('.showsearch').html(html);
            $('img', form).fadeOut(250);
          }
        });
      } else {
        form.next().find('.showsearch').html('<mark>Enter 3 or more letters</mark>');
        $('img', form).fadeOut(250);
      }
    }));

    //Custom select
    $.selectInit(selectEl)

    //  fluid video (iframe)
    $('.content article iframe').each(function () {
      var t = $(this),
        p = t.parent();
      if (p.is('p') && !p.hasClass('fullframe')) {
          p.addClass('fullframe');
      }
    });

      $('.wp-video').each(function () {
          $('.mejs-video .mejs-inner', this).addClass('fullframe');
      });

  });

    $(window).on('load', function () {
        'use strict';

    });
})(jQuery, window);