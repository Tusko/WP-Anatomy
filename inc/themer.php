<?php

if(extension_loaded('zlib')) {
	remove_action('shutdown', 'wp_ob_end_flush_all', 1);
}

require_once 'qtranslate.php';
require_once 'search_query.php';

require_once 'plugins/colors.inc.php';

// Auto-install recommended plugins
require_once 'plugins/installer.php';

// Custom Posts Duplicator
require_once 'plugins/duplicator.php';

// Include custom assets
require_once 'assets.php';

// Custom admin area functions
require_once 'wpadmin/admin-addons.php';

// Custom shortcodes
require_once 'shortcodes.php';

// Custom theme url
function theme($filepath = null) {
	return preg_replace('(https?://)', '//', get_stylesheet_directory_uri() . ($filepath ? '/' . $filepath : ''));
}

// JS Defer Load
function wpa_defer_scripts($url) {
	if(strpos($url, '#defer') === false) {
		return $url;
	} else if(is_admin()) {
		return str_replace('#defer', '', $url);
	} else {
		return str_replace('#defer', '', $url) . "' defer='defer";
	}
}

add_action('after_switch_theme', 'wpa_activate_theme');
function wpa_activate_theme() {
	if(class_exists('AssetsMinify')) {
		update_option('am_async_flag', 0);
	}

	update_option('image_default_link_type', 'none');
	update_option('uploads_use_yearmonth_folders', 0);
	update_option('permalink_structure', '/%category%/%postname%/');
}

// Run this code on 'after_setup_theme', when plugins have already been loaded.
add_action('after_setup_theme', 'wpa_pre_plugins');

// This function loads the plugins && update some wordpress options
function wpa_pre_plugins() {

	// Check to see if your plugin has already been loaded. This can be done in several ways - here are a few examples:
	//
	// Check for a class:
	//	if (!class_exists('MyPluginClass')) {
	//
	// Check for a function:
	//	if (!function_exists('my_plugin_function_name')) {
	//
	// Check for a constant:
	//	if (!defined('MY_PLUGIN_CONSTANT')) {

	if( ! class_exists('AssetsMinify')) {
		include_once('plugins/assetsminify/plugin.php');
	}

	if( ! function_exists('ctl_schedule_conversion')) {
		include_once('plugins/cyr-to-lat.php');
	}

	if( ! function_exists('AjaxThumbnailRebuild')) {
		include_once('plugins/ajax-thumbnail-rebuild/ajax-thumbnail-rebuild.php');
	}

	if( ! function_exists('jr_uploadresize_options_page')) {
		include_once('plugins/min-max-img-dimentions.php');
	}

	if( ! function_exists('wpa_media_field_input')) {
		include_once('plugins/ars-alt-editor/alt.php');
	}

}

function tinymce_custom_settings() {
	global $current_screen;
	if($current_screen && $current_screen->id == 'settings_page_tinymce-advanced') {
		$json_string = file_get_contents('plugins/tinymce-advanced-preconfig.json', true); ?>
		<script type="text/javascript">jQuery(function($) {
        var tcs_json = '<?php echo trim($json_string); ?>';
        $('textarea#tadv-import').val(tcs_json);
      });</script>
	<?php }
}

add_action('admin_head', 'tinymce_custom_settings', 10);

//Remove embeds rewrites
function disable_embeds_rewrites($rules) {
	foreach($rules as $rule => $rewrite) {
		if(false !== strpos($rewrite, 'embed=true')) {
			unset($rules[ $rule ]);
		}
	}

	return $rules;
}

// Remove recent_comments_style in wp_head
function my_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
}

add_action('widgets_init', 'my_remove_recent_comments_style');

// Remove wp version param from any enqueued scripts
function wpa_remove_wp_ver_css_js($src) {
	if(strpos($src, 'ver=')) {
		$src = remove_query_arg('ver', $src);
	}

	return $src;
}

// Compress HTML
function ob_html_compress($buf) {
	return preg_replace(array('/<!--(?>(?!\[).)(.*)(?>(?!\]).)-->/Uis', '/[[:blank:]]+/'), array('', ' '), str_replace(array("\n", "\r", "\t"), '', $buf));
}

//custom wp_nav_menu classes
function wpa_discard_menu_classes($classes, $item) {
	$classes = array_filter(
			$classes, function($class) {
		return in_array($class, array("current-menu-item", "current-menu-parent", "menu-item-has-children"));
	}
	);

	return array_merge(
			$classes,
			(array) get_post_meta($item->ID, '_menu_item_classes', true)
	);
}

// Disables Kses only for textarea saves
foreach(array('pre_term_description', 'pre_link_description', 'pre_link_notes', 'pre_user_description') as $filter) {
	remove_filter($filter, 'wp_filter_kses');
}

// Disables Kses only for textarea admin displays
foreach(array('term_description', 'link_description', 'link_notes', 'user_description') as $filter) {
	remove_filter($filter, 'wp_kses_data');
}

//New Body Classe
function wpa_body_classes($classes) {
	if(is_page()) {
		global $post;
		$temp = get_page_template();
		if($temp != null) {
			$path      = pathinfo($temp);
			$tmp       = $path['filename'] . "." . $path['extension'];
			$tn        = str_replace(".php", "", $tmp);
			$classes[] = $tn;
		}
//        if (is_active_sidebar('sidebar')) {
//            $classes[] = 'with_sidebar';
//        }
		foreach($classes as $k => $v) {
			if(
					$v == 'page-template' ||
					$v == 'page-id-' . $post->ID ||
					$v == 'page-template-default' ||
					$v == 'woocommerce-page' ||
					($temp != null ? ($v == 'page-template-' . $tn . '-php' || $v == 'page-template-' . $tn) : '')) {
				unset($classes[ $k ]);
			}
		}
	}
	if(is_single()) {
		global $post;
		$f = get_post_format($post->ID);
		foreach($classes as $k => $v) {
			if($v == 'postid-' . $post->ID || $v == 'single-format-' . (! $f ? 'standard' : $f)) {
				unset($classes[ $k ]);
			}
		}
	}

	if(is_multi_author()) {
		$classes[] = 'group-blog';
	}

//	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	$browser = $_SERVER['HTTP_USER_AGENT'];

	// Mac, PC ...or Linux
	if(preg_match("/Mac/", $browser)) {
		$classes[] = 'macos';
	} elseif(preg_match("/Windows/", $browser)) {
		$classes[] = 'windows';
	} elseif(preg_match("/Linux/", $browser)) {
		$classes[] = 'linux';
	} else {
		$classes[] = 'unknown-os';
	}
	// Checks browsers in this order: Chrome, Safari, Opera, MSIE, FF
	if(preg_match("/Edge/", $browser)) {
		$classes[] = 'edge';
	} elseif(preg_match("/Chrome/", $browser)) {
		$classes[] = 'chrome';
		preg_match("/Chrome\/(\d.\d)/si", $browser, $matches);
		@$classesh_version = 'ch' . str_replace('.', '-', $matches[1]);
		$classes[] = $classesh_version;
	} elseif(preg_match("/Safari/", $browser)) {
		$classes[] = 'safari';
		@preg_match("/Version\/(\d.\d)/si", $browser, $matches);
		@$sf_version = 'sf' . str_replace('.', '-', $matches[1]);
		$classes[] = $sf_version;
	} elseif(preg_match("/Opera/", $browser)) {
		$classes[] = 'opera';
		preg_match("/Opera\/(\d.\d)/si", $browser, $matches);
		$op_version = 'op' . str_replace('.', '-', $matches[1]);
		$classes[]  = $op_version;
	} elseif(preg_match("/MSIE/", $browser)) {
		$classes[] = 'msie';
		if(preg_match("/MSIE 6.0/", $browser)) {
			$classes[] = 'ie6';
		} elseif(preg_match("/MSIE 7.0/", $browser)) {
			$classes[] = 'ie7';
		} elseif(preg_match("/MSIE 8.0/", $browser)) {
			$classes[] = 'ie8';
		} elseif(preg_match("/MSIE 9.0/", $browser)) {
			$classes[] = 'ie9';
		}
	} elseif(preg_match("/Firefox/", $browser) && preg_match("/Gecko/", $browser)) {
		$classes[] = 'firefox';
		preg_match("/Firefox\/(\d)/si", $browser, $matches);
		$ff_version = 'ff' . str_replace('.', '-', $matches[1]);
		$classes[]  = $ff_version;
	} else {
		$classes[] = 'unknown-browser';
	}

	//qtranslate classes
	if(defined('QTX_VERSION')) {
		$classes[] = 'qtrans-' . qtranxf_getLanguage();
	}

	return $classes;
}

//Custom SEO Title
function wpa_title() {
	global $post;
	if( ! defined('WPSEO_VERSION')) {
		if(is_404()) {
			echo '404 Page not found - ';
		} elseif((is_single() || is_page()) && $post->post_parent) {
			$parent_title = get_the_title($post->post_parent);
			echo wp_title('-', true, 'right') . $parent_title . ' - ';
		} elseif(class_exists('Woocommerce') && is_shop()) {
			echo get_the_title(SHOP_ID) . ' - ';
		} else {
			wp_title('-', true, 'right');
		}
		bloginfo('name');
	} else {
		wp_title();
	}
}

function wpa_qtrans_site_url($url) {
	// you probably don't want this in admin side
	if(is_admin()) {
		return $url;
	}

	return function_exists('qtranxf_convertURL') ? qtranxf_convertURL($url) : $url;
}

//Show empty categories in category widget
function show_empty_widget_links($args) {
	$args['hide_empty'] = 0;

	return $args;
}

//remove empty title from widget
function wpa_widget_title($title) {
	return $title == '&nbsp;' ? '' : $title;
}

//simple function for wp_get_attachment_image_src()
function image_src($id, $size = 'full', $background_image = false, $height = false) {
	if($image = wp_get_attachment_image_src((get_post_type($id) == 'attachment' ? $id : get_post_thumbnail_id($id)), $size, true)) {
		return $background_image ? 'background-image: url(' . $image[0] . ');' . ($height ? 'height:' . $image[2] . 'px' : '') : $image[0];
	}
}

//Hooks a single callback to multiple tags
function add_filters($tags, $function, $priority = 10, $accepted_args = 1) {
	foreach((array) $tags as $tag) {
		add_filter($tag, $function, $priority, $accepted_args);
	}
}

/**
 * Add multiple actions to a closure
 *
 * @param     $tags
 * @param     $function_to_add
 * @param int $priority
 * @param int $accepted_args
 *
 * @return bool true
 */
function add_actions($tags, $function_to_add, $priority = 10, $accepted_args = 1) {
	//add_action() is just a wrapper around add_filter(), so we do the same
	return add_filters($tags, $function_to_add, $priority, $accepted_args);
}

function wpa_init() {
	/* @var WP $wp */
	global $wp;
	// Remove the embed query var.
	$wp->public_query_vars = array_diff($wp->public_query_vars, array(
			'embed',
	));
	// Filters for WP-API version 1.x
	add_filter('json_enabled', '__return_false');
	add_filter('json_jsonp_enabled', '__return_false');

	// Filters for WP-API version 2.x
//    add_filter('rest_enabled', '__return_false');
//    add_filter('rest_jsonp_enabled', '__return_false');
	remove_action('wp_head', 'rest_output_link_wp_head');
	remove_action('template_redirect', 'rest_output_link_header', 11);

	//Disable Thumbnails Embeds
	add_filter('embed_thumbnail_image_shape', '__return_false');


	// Turn off oEmbed auto discovery.
	add_filter('embed_oembed_discover', '__return_false');

	// Don't filter oEmbed results.
	remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

	// Remove oEmbed discovery links.
	remove_action('wp_head', 'wp_oembed_add_discovery_links');

	// Remove oEmbed-specific JavaScript from the front-end and back-end.
	remove_action('wp_head', 'wp_oembed_add_host_js');

	// Remove all embeds rewrite rules.
	add_filter('rewrite_rules_array', 'disable_embeds_rewrites');

	remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'parent_post_rel_link', 10);
	remove_action('wp_head', 'start_post_rel_link', 10);
	remove_action('wp_head', 'wp_shortlink_wp_head');
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'rel_canonical');

	// Remove all actions related to emojis
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');

	// Remove TinyMCE emojis
	add_filter('tiny_mce_plugins', function($plugins) {
		if(is_array($plugins)) {
			return array_diff($plugins, array('wpemoji'));
		} else {
			return array();
		}
	});

	//Disbale RSS feeds
	add_actions([
			'do_feed',
			'do_feed_rdf',
			'do_feed_rss',
			'do_feed_rss2',
			'do_feed_atom',
			'do_feed_rss2_comments',
			'do_feed_atom_comments'
	], function() {
		global $wp_query;
		$wp_query->set_404();
		status_header(404);
		wp_die(__('No feed available. Please visit the <a href="' . esc_url(home_url('/')) . '">homepage</a>'));
	}, 1);

	//Page/Post thumbnail support
	add_theme_support('post-thumbnails');
	// Disable Responsive Images
	add_filter('max_srcset_image_width', function() {
		return 1;
	});

	//site_url convert with qTranslate-x
	add_filter('site_url', 'wpa_qtrans_site_url');

	// Remove Default Menu Classes
	add_filter('nav_menu_css_class', 'wpa_discard_menu_classes', 10, 2);
	//Remove IDs from menu
	add_filter('nav_menu_item_id', '__return_false', 10);

	add_filter('style_loader_src', 'wpa_remove_wp_ver_css_js', 9999);
	add_filter('script_loader_src', 'wpa_remove_wp_ver_css_js', 9999);

	//defer JS
	add_filter('clean_url', 'wpa_defer_scripts', 11, 1);

	add_filter('body_class', 'wpa_body_classes');

	//Widgets extension
	add_filter('widget_categories_args', 'show_empty_widget_links');
	add_filter('widget_tag_cloud_args', 'show_empty_widget_links');
	add_filter('widget_title', 'wpa_widget_title');

	//Disbale XML-RPC (Maybe)
	add_filter('xmlrpc_enabled', '__return_false');

	//Remove x-pingback HTTP header
	add_filter('wp_headers', function($headers) {
		unset($headers['X-Pingback']);

		return $headers;
	});

	//Disable pingbacks
	add_filter('xmlrpc_methods', function($methods) {
		unset($methods['pingback.ping']);

		return $methods;
	});

	//No author pages. Send to 404
	add_action('template_redirect', function() {
		global $wp_query;
		if(is_author()) {
			$wp_query->set_404();
		}
	});

}

add_action('init', 'wpa_init', 9999);

function wpa_fontbase64($fonthash) {
	$font       = get_stylesheet_directory() . '/fonts.css';
	$md5        = filemtime($font);
	$md5_cached = get_transient('fonts64_md5');
	if($md5_cached !== $md5) {
		set_transient('fonts64_md5', $md5, 168 * 3600);
	}
	if($fonthash) {
		echo $md5_cached ? $md5_cached : $md5;
	} else {
		$minfont  = file_get_contents($font);
		$minfont  = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minfont);
		$minfont  = str_replace(array(': ', ' : '), ':', $minfont);
		$minfont  = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $minfont);
		$minfont  = str_replace(';}', '}', $minfont);
		$fontpack = array(
				'md5'   => $md5_cached,
				'value' => $minfont
		);
		echo json_encode($fontpack);
		exit;
	}
}

add_action('wp_ajax_wpa_fontbase64', 'wpa_fontbase64');
add_action('wp_ajax_nopriv_wpa_fontbase64', 'wpa_fontbase64');


if(defined('GOOGLEMAPS')) {
	function my_acf_init() {
		acf_update_setting('google_api_key', GOOGLEMAPS);
	}

	add_action('acf/init', 'my_acf_init');
}

function add_gf_cap() {
	$role = get_role('editor');
	$role->add_cap('gform_full_access');
}

add_action('admin_init', 'add_gf_cap');

add_filter('gform_pre_render', 'gw_first_error_focus');
function gw_first_error_focus($form) {
	add_filter('gform_confirmation_anchor', '__return_false'); ?>
	<script type="text/javascript">
    if(window['jQuery']) {
      (function($) {
        $(document).bind('gform_post_render', function() {
          var $firstError = $('li.gfield.gfield_error:first');
          if($firstError.length > 0) {
            $firstError.find('input, select, textarea').eq(0).focus();
            document.body.scrollTop = $firstError.offset().top;
          }
          fixGravityFileInput();
        });
      })(jQuery);
    }
	</script>
	<?php
	return $form;
}

add_filter('gform_ajax_spinner_url', 'spinner_url', 10, 2);
function spinner_url($image_src, $form) {
	return WPA_SPINNER;
}