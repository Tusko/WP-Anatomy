<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(!is_front_page()) get_header( 'shop' );
    $sale = isset( $_REQUEST['sale'] )?$_REQUEST['sale']:NULL;
?>

<?php if(is_tax('product_cat') || is_tax('product_tag')) {
        echo '<h1 class="page-title">'. single_term_title(false, false) .'</h1>';
        echo term_description()?'<article class="row">' . term_description() . '</article>':'';
    } else {
    $terms = get_terms('product_cat', 'hide_empty=1');
        if ( !empty( $terms ) && ! is_wp_error( $terms ) ) { ?>
            <nav role="directory" class="filters">
                <a data-filter="*" class="is-filtered">All items</a>
                <?php foreach($terms as $term) { ?>
                    <a data-filter=".<?php echo $term->slug; ?>" href="<?php echo get_term_link($term); ?>"><?php echo $term->name; ?></a>
                <?php } ?>
            </nav>
        <?php }
} ?>

<div id="shopping" class="row grid-wrap">
    <div class="grid">
        <div class="grid-sizer"></div>
        <?php woo_product_list( array('sale' => $sale) ); ?>
    </div>
</div>

<?php if(!is_front_page()) get_footer( 'shop' );  ?>

