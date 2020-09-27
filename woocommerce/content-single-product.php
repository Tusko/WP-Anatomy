<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woothemes.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     1.6.4
 */

if( ! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
global $product;
$attachment_ids = $product->get_gallery_attachment_ids();

?>

<div class="inner-product" itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>">
	<div class="summary">
		<div class="row">
			<?php
			/**
			 * woocommerce_before_single_product hook.
			 *
			 * @hooked wc_print_notices - 10
			 */
			do_action('woocommerce_before_single_product');
			if(post_password_required()) {
				echo get_the_password_form();

				return;
			}
			?>
		</div>
		<div class="summary-row row">
			<div class="woo-gallery">
				<?php if($attachment_ids) : ?>
					<div class="swiper-container">
						<div class="swiper-wrapper">
							<?php foreach($attachment_ids as $attachment_id) { ?>
								<div class="swiper-slide">
									<img data-src="<?php echo image_src($attachment_id, 'shop_single'); ?>" class="swiper-lazy">
									<div class="swiper-lazy-preloader"></div>
								</div>
							<?php } ?>
						</div>
						<div class="ms-button-prev"></div>
						<div class="ms-button-next"></div>
						<div class="pager"></div>
					</div>
				<?php else : ?>
					<?php the_post_thumbnail('shop_single'); ?>
				<?php endif; ?>
			</div>

			<div class="entry-summary">
				<?php
				/**
				 * woocommerce_single_product_summary hook.
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 */
				do_action('woocommerce_single_product_summary');
				?>

			</div>
		</div>
	</div>

	<?php
	/**
	 * woocommerce_after_single_product_summary hook.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action('woocommerce_after_single_product_summary');
	?>

	<meta itemprop="url" content="<?php the_permalink(); ?>"/>

</div>

<div class="woo-loadmore">
	<a href="<?php echo site_url('/#shopping'); ?>" class="button">Back to shop</a>
</div>
<?php do_action('woocommerce_after_single_product'); ?>
