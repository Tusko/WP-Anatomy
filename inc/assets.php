<?php

// Disable Gutenberg editor.
add_filter('use_block_editor_for_post_type', '__return_false', 10);
// Don't load Gutenberg-related stylesheets.
add_action('wp_enqueue_scripts', 'remove_block_css', 100);
function remove_block_css() {
	wp_dequeue_style('wp-block-library'); // Wordpress core
	wp_dequeue_style('wp-block-library-theme'); // Wordpress core
	wp_dequeue_style('wc-block-style'); // WooCommerce
	wp_dequeue_style('storefront-gutenberg-blocks'); // Storefront theme
}

// custom js/stylesheet
function tt_add_jscss() {
	if( ! is_admin()) {
		wp_deregister_script('jquery');
	}

	if(defined('QTX_VERSION')) {
		wp_deregister_style('qtranslate-style');
	}

	wp_enqueue_script('jquery', get_stylesheet_directory_uri() . '/js/libs/_jquery.js', false, null, false);
	if(defined('GOOGLEMAPS')) {
		wp_enqueue_script('defer-google-maps', get_stylesheet_directory_uri() . '/js/libs/_defer-google-maps.js', array('jquery'), null, false);
	}

	if($js_lib = directoryToArray(get_stylesheet_directory(), '/js/libs/', array('js'))) {
		asort($js_lib);
		foreach($js_lib as $name => $js) {
			wp_enqueue_script($name, $js, array('jquery'), null, true);
		}
	}

	wp_enqueue_script('libs', get_stylesheet_directory_uri() . '/js/lib.js', array('jquery', 'jquery-migrate'), null, true);
	wp_enqueue_script('init', get_stylesheet_directory_uri() . '/js/init.js', array('libs'), null, true);

	if($style_lib = directoryToArray(get_stylesheet_directory(), '/style/libs/', array('css', 'scss'))) {
		asort($style_lib);
		foreach($style_lib as $name => $lib) {
			wp_enqueue_style($name, $lib);
		}
	}

	wp_enqueue_style('main', get_stylesheet_directory_uri() . '/style/style.scss');

	if(class_exists('Woocommerce')) {
		wp_enqueue_style('custom-woo-styles', get_stylesheet_directory_uri() . '/style/woo.scss');
		wp_enqueue_script('custom-woo-scripts', get_stylesheet_directory_uri() . '/js/woo.js', false, null, true);
	}

	wp_enqueue_style('responsive', get_stylesheet_directory_uri() . '/style/rwd.scss');
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