<?php
/* BEGIN: Theme config params*/

//define ('GOOGLEMAPS',' YOUR-API-KEY-HERE');
define ('HOME_PAGE_ID', get_option('page_on_front'));
define ('BLOG_ID', get_option('page_for_posts'));
define ('POSTS_PER_PAGE', get_option('posts_per_page'));
define ('ALOAD', 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=');
if(class_exists('Woocommerce')) :
    define ('SHOP_ID', get_option('woocommerce_shop_page_id'));
    define ('ACCOUNT_ID', get_option('woocommerce_myaccount_page_id'));
    define ('CART_ID', get_option('woocommerce_cart_page_id'));
    define ('CHECKOUT_ID', get_option('woocommerce_checkout_page_id'));
    require_once('woocommerce.php');
endif;

/* END: Theme config params */
