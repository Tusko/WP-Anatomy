<?php
remove_theme_support('block-templates');
remove_theme_support('block-styles');
remove_theme_support('widgets-block-editor');
remove_theme_support('wc-block-editor');

add_action('init', 'custom_wp_remove_global_css');
function custom_wp_remove_global_css()
{
    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
}

add_action('init', 'remove_l1on');
function remove_l1on()
{
    if (!is_admin()) {
        wp_deregister_script('l10n');
    }
}

// Disable Gutenberg editor.
add_filter('use_block_editor_for_post_type', '__return_false', 10);
add_action('wp_enqueue_scripts', 'wc_remove_woo_block_styles', 98);
function wc_remove_woo_block_styles()
{
    global $wp_styles;
    if (!is_admin()) {
        wp_dequeue_style('wc-blocks-style'); // Remove WooCommerce block CSS
        wp_dequeue_style('wc-blocks-vendors-style'); // Remove WooCommerce block vendors CSS
        wp_dequeue_style('wc-blocks-packages-style'); // Remove WooCommerce block packages CSS

        // Remove WooCommerce block styles after v8.0.1 update
        foreach ($wp_styles->registered as $registered) {
            if (
                str_starts_with($registered->handle, 'wc-blocks-style-') ||
                str_starts_with($registered->handle, 'wp-block-')
            ) {
                wp_dequeue_style($registered->handle);
            }
        }
    }
}

function wc_disable_gutenberg_hooks()
{
    remove_action('admin_menu', 'gutenberg_menu');
    remove_action('admin_init', 'gutenberg_redirect_demo');

    remove_filter('wp_refresh_nonces', 'gutenberg_add_rest_nonce_to_heartbeat_response_headers');
    remove_filter('get_edit_post_link', 'gutenberg_revisions_link_to_editor');
    remove_filter('wp_prepare_revision_for_js', 'gutenberg_revisions_restore');

    remove_action('rest_api_init', 'gutenberg_register_rest_routes');
    remove_action('rest_api_init', 'gutenberg_add_taxonomy_visibility_field');
    remove_filter('rest_request_after_callbacks', 'gutenberg_filter_oembed_result');
    remove_filter('registered_post_type', 'gutenberg_register_post_prepare_functions');

    remove_action('do_meta_boxes', 'gutenberg_meta_box_save', 1000);
    remove_action('submitpost_box', 'gutenberg_intercept_meta_box_render');
    remove_action('submitpage_box', 'gutenberg_intercept_meta_box_render');
    remove_action('edit_page_form', 'gutenberg_intercept_meta_box_render');
    remove_action('edit_form_advanced', 'gutenberg_intercept_meta_box_render');
    remove_filter('redirect_post_location', 'gutenberg_meta_box_save_redirect');
    remove_filter('filter_gutenberg_meta_boxes', 'gutenberg_filter_meta_boxes');

    remove_action('admin_notices', 'gutenberg_build_files_notice');
    remove_filter('body_class', 'gutenberg_add_responsive_body_class');
    remove_filter('admin_url', 'gutenberg_modify_add_new_button_url'); // old
    remove_action('admin_enqueue_scripts', 'gutenberg_check_if_classic_needs_warning_about_blocks');
    remove_filter('register_post_type_args', 'gutenberg_filter_post_type_labels');

    remove_action('admin_init', 'gutenberg_add_edit_link_filters');
    remove_action('admin_print_scripts-edit.php', 'gutenberg_replace_default_add_new_button');
    remove_filter('redirect_post_location', 'gutenberg_redirect_to_classic_editor_when_saving_posts');
    remove_filter('display_post_states', 'gutenberg_add_gutenberg_post_state');
    remove_action('edit_form_top', 'gutenberg_remember_classic_editor_when_saving_posts');
}

add_filter('init', 'wc_disable_gutenberg_hooks', 10);

// custom js/stylesheet
function tt_add_jscss()
{
    if (defined('QTX_VERSION')) {
        wp_deregister_style('qtranslate-style');
    }

    if (defined('GOOGLEMAPS')) {
        wp_enqueue_script('defer-google-maps', get_stylesheet_directory_uri() . '/js/libs/_defer-google-maps.js', array('jquery'), null, false);
    }

    if ($js_lib = directoryToArray(get_stylesheet_directory(), '/js/libs/', array('js'))) {
        asort($js_lib);
        foreach ($js_lib as $name => $js) {
            wp_enqueue_script($name, $js, array('jquery'), null, true);
        }
    }

    wp_enqueue_script('libs', get_stylesheet_directory_uri() . '/js/lib.js', array('jquery', 'jquery-migrate'), null, true);
    wp_enqueue_script('init', get_stylesheet_directory_uri() . '/js/init.js', array('libs'), null, true);

//	if($style_lib = directoryToArray(get_stylesheet_directory(), '/style/libs/', array('css', 'scss'))) {
//		asort($style_lib);
//		foreach($style_lib as $name => $lib) {
//			wp_enqueue_style($name, $lib);
//		}
//	}

//	wp_enqueue_style('main', get_stylesheet_directory_uri() . '/style/style.scss');

    if (class_exists('Woocommerce')) {
//		wp_enqueue_style('custom-woo-styles', get_stylesheet_directory_uri() . '/style/woo.scss');
        wp_enqueue_script('custom-woo-scripts', get_stylesheet_directory_uri() . '/js/woo.js', false, null, true);
    }

//	wp_enqueue_style('responsive', get_stylesheet_directory_uri() . '/style/rwd.scss');
}

add_action('wp_enqueue_scripts', 'tt_add_jscss');

/* ACF Repeater Styles */
function acf_repeater_even() {
	$scheme = get_user_option('admin_color');
	$color  = '';
	switch($scheme) {
		case 'fresh':
			$color = '#0073aa';
			break;
		case 'light':
			$color = '#d64e07';
			break;
		case 'blue':
			$color = '#52accc';
			break;
		case 'coffee':
			$color = '#59524c';
			break;
		case 'ectoplasm':
			$color = '#523f6d';
			break;
		case 'midnight':
			$color = '#e14d43';
			break;
		case 'ocean':
			$color = '#738e96';
			break;
		case 'sunrise':
			$color = '#dd823b';
			break;
	}
	echo '<style>.acf-repeater > table > tbody > tr:nth-child(even) > td.order {color: #fff !important;background-color: ' . $color . ' !important; text-shadow: none}.acf-fc-layout-handle {color: #fff !important;background-color: #23282d!important; text-shadow: none}</style>';
}

add_action('admin_footer', 'acf_repeater_even');

function getFileExt($path) {
	$pos = strrpos($path, ".");
	if($pos === false) {
		return "";
	} else {
		return substr($path, $pos + 1);
	}
}

function fileNotIgnore($name) {
	if(substr($name, 0, 1) !== "_") {
		return $name;
	}
}

function directoryToArray($abs, $directory, $filterMap = null) {
	$assets = array();
	$dir    = $abs . $directory;
	if($handle = opendir($dir)) {
		while(false !== ($file = readdir($handle))) {
			if($file != "." && $file != ".." && fileNotIgnore($file) && ! is_dir($dir . "/" . $file)) {
				if($filterMap) {
					if(in_array(getFileExt($file), $filterMap)) {
						$assets[ basename($file) ] = get_stylesheet_directory_uri() . $directory . $file;
					}
				} else {
					$assets[ basename($file) ] = get_stylesheet_directory_uri() . $directory . $file;
				}
			}
		}
		closedir($handle);
	}

	return $assets;
}