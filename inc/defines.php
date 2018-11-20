<?php
/* BEGIN: Theme config params*/

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

if(class_exists('GFForms')) {
	define('GF_LICENSE_KEY','94845faee900af43387fce4f9f1ce525');
}

if(class_exists('Optimus_HQ')) {
	$optimus_HQ_key = get_site_option( 'optimus_key' );
	if(!$optimus_HQ_key) {
		update_site_option('optimus_key', '1CD4IJ468PD7KI0RBJPKI7P0');
	}
}

if(class_exists('Tribe__Events__Pro__Main')) {
	$eventsCalendarPro = get_site_option( 'pue_install_key_events_calendar_pro' );
	if(!$eventsCalendarPro) {
		update_site_option('pue_install_key_events_calendar_pro', '2b8e0de6f9b94c3e1a067759a36aeb45b9ffc95f');
	}
}

/* END: Theme config params */
