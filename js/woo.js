(function ($, window) {
  'use strict';
  $(document).ready(function () {
    $(this)
      .on('click', '[data-wooremove]', function () {
        var t = $(this),
          product_id = t.attr('data-wooremove');
        $.pAjax({
          type: 'post',
          url: document.body.dataset.a,
          data: {
            action: 'woo_product_remover',
            product_id: product_id
          }
        })
          .then(function (data) {
            $('header .cart-contents').html($.parseHTML(data));
          });
        return false;
      })
  });
})(jQuery, window);
