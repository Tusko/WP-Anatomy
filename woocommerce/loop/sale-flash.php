<?php

/**
 * Product loop sale flash
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/sale-flash.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post, $product;
$featured = get_post_meta($post->ID, '_featured', true);

echo '<mark class="product_badges">';

if ($product->is_on_sale()) {
    echo do_shortcode('[badge icon="discount"]');
}
if (strtotime($product->post->post_date) > (time() - (7 * 3600 * 24))) {
    echo do_shortcode('[badge icon="new"]');
}
if($featured && $featured == 'yes') {
    echo do_shortcode('[badge icon="featured"]');
}

echo '</mark>';

?>
