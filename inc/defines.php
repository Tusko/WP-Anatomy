<?php
/* BEGIN: Theme config params*/

//define ('GOOGLEMAPS','AIzaSyDNsicAsP6-VuGtAb1O9riI3oc_NOb7IOU');
define('HOME_PAGE_ID', get_option('page_on_front'));
define('BLOG_ID', get_option('page_for_posts'));
define('POSTS_PER_PAGE', get_option('posts_per_page'));
if(class_exists('Woocommerce')) :
	define('SHOP_ID', get_option('woocommerce_shop_page_id'));
	define('ACCOUNT_ID', get_option('woocommerce_myaccount_page_id'));
	define('CART_ID', get_option('woocommerce_cart_page_id'));
	define('CHECKOUT_ID', get_option('woocommerce_checkout_page_id'));
	require_once('woocommerce.php');
endif;

if(class_exists('GFForms')) {
	define('GF_LICENSE_KEY', '94845faee900af43387fce4f9f1ce525');
	define('WPA_SPINNER', "https://arsmoon.com/brand-wp-admin/spin.php?color=000");
}

if(class_exists('Optimus_HQ')) {
	$optimus_HQ_key = get_site_option('optimus_key');
	if( ! $optimus_HQ_key) {
		update_site_option('optimus_key', '1CD4IJ468PD7KI0RBJPKI7P0');
	}
}

if(class_exists('Tribe__Events__Pro__Main')) {
	$eventsCalendarPro = get_site_option('pue_install_key_events_calendar_pro');
	if( ! $eventsCalendarPro) {
		update_site_option('pue_install_key_events_calendar_pro', '2b8e0de6f9b94c3e1a067759a36aeb45b9ffc95f');
	}
}

function wpa__prelicense() {
	if( ! acf_pro_is_license_active()) {
		$args = array(
				'_nonce'      => wp_create_nonce('activate_pro_licence'),
				'acf_license' => base64_encode('order_id=37918|type=personal|date=2014-08-21 15:02:59'),
				'acf_version' => acf_get_setting('version'),
				'wp_name'     => get_bloginfo('name'),
				'wp_url'      => home_url(),
				'wp_version'  => get_bloginfo('version'),
				'wp_language' => get_bloginfo('language'),
				'wp_timezone' => get_option('timezone_string'),
		);

		$response = acf_pro_get_remote_response('activate-license', $args);
		$response = json_decode($response, true);
		if($response['status'] == 1) {
			acf_pro_update_license($response['license']);
		}
	}
}

if(function_exists('acf_pro_is_license_active')) {
	add_action('acf/init', 'wpa__prelicense', 99);
}

/* END: Theme config params */
