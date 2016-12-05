<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see     http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}

$terms = get_the_terms( $product->id, 'product_cat' );

$classes = '';
$co = count($terms); $i = $c = 1;
if($terms) :
    foreach($terms as $cat){
        $classes .= ' ' . $cat->slug;
    }
endif;
?>
<div class="grid-item<?php echo $product->is_on_sale()?' sale':''; ?><?php echo $classes; ?>">
    <?php $thumbnal_id = get_post_thumbnail_id($product->id);
          $img = wp_get_attachment_image_src($thumbnal_id, 'shop_catalog', true);
          echo '<a class="product-image" href="' . get_permalink($product->id) . '">';
            woocommerce_show_product_loop_sale_flash();
            if(has_post_thumbnail($product->id)) {
                echo '<img src="'.placeImg($img[1], $img[2]).'" alt="'.get_alt($thumbnal_id).'" data-defer="'.$img[0].'" class="aligncenter" width="'.$img[1].'" height="'.$img[2].'" />';
            } else {
                echo wc_placeholder_img( 'shop_catalog' );
            }
            echo '</a>';
    ?>
    <div class="product-data">
        <a class="product-title" href="<?php the_permalink(); ?>">
            <h3><?php echo get_the_title($product->id); ?></h3>
            <div class="product-price"><?php echo $product->get_price_html(); ?></div>
        </a>
    </div>
</div>


