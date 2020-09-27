(function() {
  'use strict';

  $(document).ready(function() {

    $(this)
      .on('click', '[data-wooremove]', function() {
        var t = $(this),
          product_id = t.attr('data-wooremove');
        $.ajax({
          type: 'post',
          url: $('body').data('a'),
          data: {
            action: 'woo_product_remover',
            product_id: product_id
          },
          success: function(data) {
            $('header .cart-contents').html($.parseHTML(data));
          }
        });
        return false;
      })

      .on('click', '.woo-loadmore > a[href=""]', function() {
        var t = $(this),
          pager = parseInt(t.attr('data-pager'), 10),
          maxpages = parseInt(t.attr('data-max'), 10),
          loader = t.next();
        $.ajax({
          type: 'post',
          url: $('body').data('a'),
          data: {
            action: 'woo_product_list',
            pager: pager
          },
          beforeSend: function() {
            loader.addClass('is-loading');
            t.fadeTo(350, 0);
          },
          success: function(items) {
            var $newEls = $.parseHTML(items);
            t.parent().remove();
            $grid.append($newEls).isotope('appended', $newEls).isotope('layout');
          }
        });
        return false;
      });

    $('.accordion > h6').addEvent('click', function(e) {
      var t = $(this),
        all = $(e.data.selector);
      if(!t.hasClass('o')) {
        all.removeClass('o').next().slideUp(350);
        t.addClass('o').next().slideDown(350);
      } else {
        all.removeClass('o').next().slideUp(350);
      }
      return false;
    });

    $('.variations_form').on('woocommerce_variation_has_changed', function() {
      $('.variations select').each(function() {
        $(this).next().text($('*:selected', this).text());
      });
      $('[itemprop="offers"] .price').html($('.woocommerce-variation-price .price').html());
    });

  });
});
