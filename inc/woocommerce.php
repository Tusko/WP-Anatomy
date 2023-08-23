<?php
function content_badge($atts)
{
    extract(shortcode_atts(array(
        'icon' => theme('images/new.svg')
    ), $atts));

    return '<img data-src="' . theme('images/' . $icon . '.svg') . '" class="aligncenter badge-icon lazyload" />';
}

add_shortcode("badge", "content_badge");

add_action('wp_print_scripts', 'wc_disable_password_strength_meter', 100);
function wc_disable_password_strength_meter()
{
    global $wp;
    $wp_check = isset($wp->query_vars['lost-password']) || (isset($_GET['action']) && $_GET['action'] === 'lostpassword') || is_page('lost_password');
    $wc_check = (class_exists('WooCommerce') && (is_account_page() || is_checkout()));

    if (!$wp_check && !$wc_check) {
        if (wp_script_is('zxcvbn-async', 'enqueued')) {
            wp_dequeue_script('zxcvbn-async');
        }

        if (wp_script_is('password-strength-meter', 'enqueued')) {
            wp_dequeue_script('password-strength-meter');
        }

        if (wp_script_is('wc-password-strength-meter', 'enqueued')) {
            wp_dequeue_script('wc-password-strength-meter');
        }
    }
}

add_filter('show_recent_comments_widget_style', '__return_false');
add_filter('woocommerce_allow_marketplace_suggestions', '__return_false', 999);
add_filter('jetpack_just_in_time_msgs', '__return_false', 20);
add_filter('jetpack_show_promotions', '__return_false', 20);

add_filter('woocommerce_enqueue_styles', '__return_false');
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

add_action('admin_menu', 'wc_remove_admin_addon_submenu', 999);
function wc_remove_admin_addon_submenu()
{
    remove_submenu_page('woocommerce', 'wc-addons');
    remove_submenu_page('woocommerce', 'wc-addons&section=helper');
}

function wc_cleaner_css_and_js()
{

    //remove generator meta tag
    remove_action('wp_head', array($GLOBALS['woocommerce'], 'generator'));
    remove_action('wp_head', 'wc_generator_tag');

    //first check that woo exists to prevent fatal errors
    if (function_exists('is_woocommerce')) {
        //dequeue scripts and styles
        if (!is_woocommerce() && !is_cart() && !is_checkout()) {
            wp_dequeue_style('woocommerce-general');
			wp_dequeue_style('woocommerce-layout');
			wp_dequeue_style('woocommerce-smallscreen');
			wp_dequeue_style('woocommerce_frontend_styles');
			wp_dequeue_style('woocommerce_fancybox_styles');
			wp_dequeue_style('woocommerce_chosen_styles');
			wp_dequeue_style('woocommerce_prettyPhoto_css');
			wp_dequeue_script('wc_price_slider');
			wp_dequeue_script('wc-single-product');
			wp_dequeue_script('wc-add-to-cart');
			wp_dequeue_script('wc-cart-fragments');
			wp_dequeue_script('wc-checkout');
			wp_dequeue_script('wc-add-to-cart-variation');
			wp_dequeue_script('wc-single-product');
			wp_dequeue_script('wc-cart');
			wp_dequeue_script('wc-chosen');
			wp_dequeue_script('woocommerce');
			wp_dequeue_script('prettyPhoto');
			wp_dequeue_script('prettyPhoto-init');
			wp_dequeue_script('jquery-blockui');
			wp_dequeue_script('jquery-placeholder');
			wp_dequeue_script('fancybox');
			wp_dequeue_script('jqueryui');
			wp_dequeue_script('wc-add-to-cart-variation');

		}
		wp_dequeue_style('select2');
		wp_deregister_style('select2');
		wp_dequeue_script('select2');
		wp_deregister_script('select2');
	}

}

add_action('wp_enqueue_scripts', 'wc_cleaner_css_and_js', 99);

function get_parent_terms($term) {
	if($term->parent > 0) {
		$term = get_term_by("id", $term->parent, "product_cat");
		if($term->parent > 0) {
			get_parent_terms($term);
		} else {
			return $term;
		}
	} else {
		return $term;
	}
}

function plural_form($n, $forms) {
	return $n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]);
}

//woo wp_nav_menu classes
function woo_special_nav_class($classes, $item) {
	if((is_woocommerce() || is_product_category() || is_checkout() || is_checkout_pay_page() || is_cart()) && get_post_meta($item->ID, '_menu_item_object_id', true) == SHOP_ID) {
		$classes[] = 'current-menu-item';
	}

	return $classes;
}

add_filter('nav_menu_css_class', 'woo_special_nav_class', 10, 2);

// check for empty-cart get param to clear the cart
add_action('init', 'woocommerce_clear_cart_url');
function woocommerce_clear_cart_url() {
	global $woocommerce;
	if(isset($_GET['clear-cart'])) {
		$woocommerce->cart->empty_cart();
	}
}

function woocommerce_header_add_to_cart_fragment($fragments) {
	ob_start();
	minicart_tpl();
	$fragments['.cart-contents'] = '<div class="cart-contents">' . ob_get_clean() . '</div>';

	return $fragments;
}

add_filter('woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');

//custom mini-cart
function minicart_tpl() { ?>
	<a class="minicart-icon" href="<?php echo WC()->cart->get_cart_url(); ?>">
		<i class="icon_cart_alt"></i>
		<?php echo WC()->cart->cart_contents_count !== 0 ? '<span>' . WC()->cart->cart_contents_count . '</span>' : ''; ?>
	</a>
	<div class="cc_cart">
		<?php
		if(WC()->cart->cart_contents) {
			$_pf = new WC_Product_Factory();
			echo '<table class="shop_table shop_table_mini"><thead><th colspan="2">Product</th><th colspan="2">Price</th></thead><tbody>';
			foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item) {
				$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
				$pid      = $cart_item['product_id'];
				?>
				<tr>
					<td class="product-thumbnail">
						<a href="<?php echo get_permalink($pid); ?>"><?php echo has_post_thumbnail($pid) ? get_the_post_thumbnail($pid, 'shop_thumbnail') : '<img src="' . woocommerce_placeholder_img_src() . '" alt="" />'; ?></a>
					</td>
					<td class="product-name">
						<?php
						if( ! $_product->is_visible()) {
							echo apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key) . '&nbsp;';
						} else {
							echo apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($_product->get_permalink($cart_item)), $_product->get_title()), $cart_item, $cart_item_key);
						}
						// Meta data
						echo WC()->cart->get_item_data($cart_item);
						?>
					</td>
					<td class="product-price">
						<span><?php echo $_product->get_price_html(); ?></span>
					</td>
					<td class="product-remove">
						<span data-wooremove="<?php echo $pid; ?>" class="icon_close"></span>
					</td>
				</tr>
			<?php }
			echo '</tbody></table>'
			     . '<div class="cc_totals">'
			     . '<a href="' . get_permalink(CART_ID) . '" class="button">Manage Cart</a>'
			     . '<a href="?clear-cart" class="button">Clear Cart</a>'
			     . '<div class="cc_total">Total: ' . WC()->cart->get_cart_total() . '</div>'
			     . '<a href="' . get_permalink(CHECKOUT_ID) . '" class="button">Checkout →</a>';
		} else {
			echo '<div class="cc_empty">Your cart is empty. <br /><a href="' . site_url('/#shopping') . '">Go to store</a> and buy something.</div>';
		} ?>
	</div>
<?php }

//remove product from minicart
function woo_product_remover() {
	extract($_POST);
	if(isset($product_id) && $product_id !== 'all') {
		$prod_to_remove = intval($product_id);
		foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item) {
			$prod_id = $cart_item['product_id'];
			if($prod_to_remove == $prod_id) {
				WC()->cart->set_quantity($cart_item_key, 0, true);
				break;
			}
		}
	} else {
		global $woocommerce;
		$woocommerce->cart->empty_cart();
	}
	echo minicart_tpl();
	wp_die();
}

add_action('wp_ajax_woo_product_remover', 'woo_product_remover');
add_action('wp_ajax_nopriv_woo_product_remover', 'woo_product_remover');

//woocommerce ajax
function woo_product_list($firstload = array()) {

	if(defined('DOING_AJAX') && DOING_AJAX) {
		extract($_POST);
		$pager = isset($pager) ? $pager : 1;
	} else {
		extract($firstload);
		$pager = 1;
	}

	$grid_args = array(
		'post_type'      => 'product',
		'posts_per_page' => POSTS_PER_PAGE,
		'post_status'    => 'publish',
		'paged'          => $pager,
		'meta_query'     => array(
			array(
				'key'     => '_visibility',
				'value'   => array('catalog', 'visible'),
				'compare' => 'IN'
			)
		)
	);

//    if(isset($term_id) && $term_id !== 0) {
//        $grid_args['tax_query'] = array(
//            'taxonomy' => 'product_cat',
//            'field' => 'id',
//            'terms' => $term_id
//        );
//    }

	if(is_tax('product_cat') || is_tax('product_tag')) {
		$grid_args['posts_per_page'] = -1;
	}

	if(isset($issearch) && trim($issearch) && mb_strlen(trim($issearch)) > 2) {
		$grid_args['s'] = $issearch;
	}

	if(isset($sale)) {
		$grid_args['meta_query'] = WC()->query->get_meta_query();
		$grid_args['post__in']   = array_merge(array(0), wc_get_product_ids_on_sale());
	}

	$grid_items = new WP_Query($grid_args);

	$maxpages = $grid_items->max_num_pages;

	if($grid_items->have_posts()) {
		while($grid_items->have_posts()) {
			$grid_items->the_post();
			wc_get_template_part('content', 'product');
		}
		if($maxpages > $pager) { ?>
			<div class="grid-item woo-loadmore">
				<a href="" class="button" data-pager="<?php echo $pager ? $pager + 1 : 2; ?>" data-max="<?php echo $maxpages; ?>">Load more</a>
				<?php echo get_loader(); ?>
			</div>
		<?php }
	}

	if(defined('DOING_AJAX') && DOING_AJAX) {
		exit();
	}
}

add_action('wp_ajax_woo_product_list', 'woo_product_list');
add_action('wp_ajax_nopriv_woo_product_list', 'woo_product_list');

//get smallest price of product
function wpa_variation_price_min($price, $product) {

	// Main Price
	$prices = array($product->get_variation_price('min', true), $product->get_variation_price('max', true));
	$price  = $prices[0] !== $prices[1] ? sprintf(__('%1$s', 'woocommerce'), wc_price($prices[0])) : wc_price($prices[0]);

	// Sale Price
	$prices = array($product->get_variation_regular_price('min', true), $product->get_variation_regular_price('max', true));
	sort($prices);
	$saleprice = $prices[0] !== $prices[1] ? sprintf(__('%1$s', 'woocommerce'), wc_price($prices[0])) : wc_price($prices[0]);

	if($price !== $saleprice) {
		$price = '<ins>' . $price . '</ins><del>' . $saleprice . '</del>';
	}

	return $price;
}

add_filter('woocommerce_variable_sale_price_html', 'wpa_variation_price_min', 10, 2);
add_filter('woocommerce_variable_price_html', 'wpa_variation_price_min', 10, 2);

//woo rename tabs
function woo_rename_tabs($tabs) {
	if(isset($tabs['additional_information'])) {
		$tabs['additional_information']['title'] = __('Additional');
	}
	if(isset($tabs['reviews'])) {
		$tabs['reviews']['title'] = __('Reviews');
	}

	return $tabs;
}

add_filter('woocommerce_product_tabs', 'woo_rename_tabs', 98);
