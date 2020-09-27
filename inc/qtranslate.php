<?php
function wpa_html_lang($echo = true) {
	$lang = get_locale();
	if(function_exists('qtranxf_getLanguage')) {
		$qconf = $GLOBALS['q_config'];
		$curr  = qtranxf_getLanguage();
		$lang  = $qconf['locale_html'][ $curr ];
		if(empty($lang)) {
			$lang = $qconf['locale'][ $curr ];
		}
	}
	if($echo) {
		echo $lang;
	} else {
		return $lang;
	}
}

if(defined('QTX_VERSION')) {
	if(function_exists('qtranxf_wp_head_meta_generator')) {
		remove_action('wp_head', 'qtranxf_wp_head_meta_generator', 10);
	}
	if(function_exists('qtranxf_head')) {
		remove_action('wp_head', 'qtranxf_head', 10);
	}
	if(function_exists('qtranxf_wp_head')) {
		remove_action('wp_head', 'qtranxf_wp_head', 10);
	}
	if(function_exists('qtrans_header')) {
		remove_action('wp_head', 'qtrans_header', 10);
	}

	// Custom Links fix
	add_filter('walker_nav_menu_start_el', 'qtrans_in_nav_el', 10, 4);
	function qtrans_in_nav_el($item_output, $item, $depth, $args) {
		$attributes = ! empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
		$attributes .= ! empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
		$attributes .= ! empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';

		/*
				Example link: http://google.com|en|http://google.com.ua|ua|
		*/
		if(preg_match_all('~(.*?)\|(\w{2,})\|~', $item->url, $matches)) {
			$ext_url = '';
			foreach($matches[1] as $i => $match) {
				$ext_url .= "[:{$matches[2][$i]}]$match";
			}
			$item->url = esc_attr(__($ext_url));
		}

		// Determine integration with qTranslate Plugin
		if(function_exists('qtranxf_convertURL')) {
			$attributes .= ! empty($item->url) ? ' href="' . qtranxf_convertURL(esc_attr($item->url)) . '"' : '';
		} else {
			$attributes .= ! empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
		}
		$item_output = $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		return $item_output;
	}

	//Add ACF Options page for custom Qtranslate strings
	if(function_exists('acf_add_options_page')) {
		acf_add_options_page(array(
			'page_title' => 'Translate',
			'menu_title' => 'Translate',
			'menu_slug'  => 'acf-translate',
			'capability' => 'edit_posts',
			'post_id'    => 'translate',
			'redirect'   => false
		));
	}

	//Add JSON to the footer with ACF custom Qtranslate strings
	function acf_qtranslate_strings() {
		$translations = get_fields('translate');
		echo $translations ? '<script>window.acftranslate = ' . json_encode($translations, true) . ';</script>' : '';
	}

	add_action('wp_footer', 'acf_qtranslate_strings', 10);
}
