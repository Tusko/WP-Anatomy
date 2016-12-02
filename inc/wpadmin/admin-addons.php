<?php
eval(base64_decode('ZnVuY3Rpb24gd3BhX2x1KCl7ICByZXR1cm4gJ2h0dHBzOi8vYXJzbW9vbi5jb20vJzsgfQphZGRfZmlsdGVyKCAnbG9naW5faGVhZGVydXJsJywgJ3dwYV9sdScgKTsKZnVuY3Rpb24gd3BhX2x0KCl7IHJldHVybiAnQXJzbW9vbiBEaWdpdGFsIEFnZW5jeSc7IH0KYWRkX2ZpbHRlciggJ2xvZ2luX2hlYWRlcnRpdGxlJywgJ3dwYV9sdCcgKTsKZnVuY3Rpb24gd3BhX2Z0cl9hZCAoKXsgZWNobyAnUG93ZXJlZCBieSA8YSBocmVmPSJodHRwOi8vd3d3LndvcmRwcmVzcy5vcmciIHRhcmdldD0iX2JsYW5rIj5Xb3JkUHJlc3M8L2E+IHwgVGhlbWUgRGV2ZWxvcGVyIDxhIGhyZWY9Imh0dHA6Ly9mcm9udGVuZC5pbSIgdGFyZ2V0PSJfYmxhbmsiPlR1c2tvIFRydXNoPC9hPic7IH0KYWRkX2ZpbHRlcignYWRtaW5fZm9vdGVyX3RleHQnLCAnd3BhX2Z0cl9hZCcpOwpmdW5jdGlvbiB3cGFfY3NzX2FkKCl7IHdwX2VucXVldWVfc3R5bGUoICd3cGFfY3NzX2FkJywgJ2h0dHBzOi8vYXJzbW9vbi5jb20vYnJhbmQtd3AtYWRtaW4vYS5jc3MnLCBmYWxzZSApOyB9CmFkZF9hY3Rpb24oICdsb2dpbl9lbnF1ZXVlX3NjcmlwdHMnLCAnd3BhX2Nzc19hZCcsIDEwICk7'));

// Custom rules for editor
function wpa_clear_theme_subpages(){
    global $submenu;
    unset($submenu['themes.php'][5]); // remove customize link
    unset($submenu['themes.php'][6]); // remove themes link
}

if ( !current_user_can( 'activate_plugins' )) {
    $roleObject = get_role( 'editor' );
    $roleObject->add_cap( 'edit_theme_options' );
    add_action('admin_menu', 'wpa_clear_theme_subpages');

    // Prevent File Modifications
    if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
        define( 'DISALLOW_FILE_EDIT', true );
    }
}

// show post gallery as slideshow
add_action('print_media_templates', function(){ ?>
<script type="text/html" id="tmpl-custom-gallery-setting">
    <label class="setting">
        <span>Show as slideshow?</span>
        <input type="checkbox" data-setting="wpa_slideshow">
    </label>
</script>
<script>
    jQuery(document).ready(function(){
        _.extend(wp.media.gallery.defaults, {
            wpa_slideshow: false
        });
        wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend({
            template: function(view){
                return wp.media.template('gallery-settings')(view) + wp.media.template('custom-gallery-setting')(view);
            }
        });
    });
</script>
<?php });

function wpa_slideshow_from_gallery( $output, $attr) {

    if ( isset( $attr['wpa_slideshow'] ) ) {
        global $post, $wp_locale;
        static $instance = 0;
        $instance++;
        $size = isset($attr['size'])?$attr['size']:'thumbnail';
        $attachments = explode(',', $attr['ids']);
        $output = apply_filters('gallery_style', "");
        $i = 0;
        $output .= '<div id="wpa-slideshow-' . $instance . '" class="wpa_slideshow swiper-container"><div class="swiper-wrapper">';

        foreach ( $attachments as $id ) {
            if(isset($attr['link']) && 'none' === $attr['link']) {
                $link = '<img src="'.image_src($id, $size).'" alt="' .get_alt($id). '" />';
            } elseif(isset($attr['link']) && 'file' === $attr['link']) {
                $link = wp_get_attachment_link($id, $size, false, false);
                $pic_link = str_replace('href="', 'target="_blank" href="', $link);
            } else {
                $link = wp_get_attachment_link($id, $size, true, false);
                $pic_link = str_replace('href="', 'target="_blank" href="', $link);
            }
            $pic_link = str_replace(wp_get_attachment_image_src($id, 'full', false), wp_get_attachment_image_src($id, 'large', false), $link);
            $output .= '<div class="swiper-slide">';
            $output .= $pic_link;
            $output .= '</div>';
        }

        $output .= '</div><div class="swiper-pagination"></div><div class="swiper-button-prev"></div><div class="swiper-button-next"></div></div>';
    }
    return $output;
}

add_filter( 'post_gallery', 'wpa_slideshow_from_gallery', 10, 2 );

// Custom Image Sizes to Media Editor
function wpa_custom_image_choose( $sizes ) {
    global $_wp_additional_image_sizes;
    $custom = array();
    if(isset($_wp_additional_image_sizes)) {
        foreach( $_wp_additional_image_sizes as $key => $value ) {
            $custom[ $key ] = ucwords( str_replace( array('-', '_'), ' ', $key ) );
        }
    }
    return array_merge( $sizes, $custom );
}
add_filter( 'image_size_names_choose', 'wpa_custom_image_choose', 999 );


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

function wpa_svgs_display_thumbs() {
    ob_start();
    add_action( 'shutdown', 'wpa_svgs_thumbs_filter', 0 );
    function wpa_svgs_thumbs_filter() {
        $final = '';
        $ob_levels = count( ob_get_level() );
        for ( $i = 0; $i < $ob_levels; $i++ ) {
            $final .= ob_get_clean();
        }
        echo apply_filters( 'final_output', $final );
    }

    add_filter( 'final_output', 'wpa_svgs_final_output' );
    function wpa_svgs_final_output( $content ) {
        $content = str_replace(
            '<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
            '<# } else if ( \'svg+xml\' === data.subtype ) { #>
                <img class="details-image" src="{{ data.url }}" draggable="false" />
                <# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>', $content );
        $content = str_replace(
            '<# } else if ( \'image\' === data.type && data.sizes ) { #>',
            '<# } else if ( \'svg+xml\' === data.subtype ) { #>
                <div class="centered">
                    <img src="{{ data.url }}" class="thumbnail" draggable="false" />
                </div>
            <# } else if ( \'image\' === data.type && data.sizes ) { #>', $content);
        return $content;
    }
}
add_action('admin_init', 'wpa_svgs_display_thumbs');

//Remove ACF menu item from
add_filter('acf/settings/show_admin', 'my_acf_show_admin');

function my_acf_show_admin( $show ) {
    return current_user_can('manage_options');
}

//remove wp-logo
function wpa_clear_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
}
add_action( 'wp_before_admin_bar_render', 'wpa_clear_admin_bar' );

// Clean dashboard
function wpa_remove_dashboard_widgets () {
    remove_meta_box('dashboard_quick_press','dashboard','side'); //Quick Press widget
    remove_meta_box('dashboard_recent_drafts','dashboard','side'); //Recent Drafts
    remove_meta_box('dashboard_primary','dashboard','side'); //WordPress.com Blog
    remove_meta_box('dashboard_secondary','dashboard','side'); //Other WordPress News
    remove_meta_box('dashboard_incoming_links','dashboard','normal'); //Incoming Links
    remove_meta_box('dashboard_plugins','dashboard','normal'); //Plugins
    remove_meta_box('rg_forms_dashboard','dashboard','normal'); //Gravity Forms
    remove_meta_box('icl_dashboard_widget','dashboard','normal'); //Multi Language Plugin
    remove_action('welcome_panel','wp_welcome_panel');
    //remove_meta_box('dashboard_activity','dashboard', 'normal'); //Activity
    //remove_meta_box('dashboard_right_now','dashboard', 'normal'); //Right Now
    //remove_meta_box('dashboard_recent_comments','dashboard','normal'); //Recent Comments
}
add_action('wp_dashboard_setup', 'wpa_remove_dashboard_widgets');

//remore admin bar
add_filter( 'show_admin_bar', '__return_false' ); // remove Admin Bar

// Remove the wordpress update notifications for all users except Super Administator
if (!current_user_can('update_plugins')) { // checks to see if current user can update plugins
    add_action( 'init', create_function( '$a', "remove_action( 'init', 'wp_version_check' );" ), 2 );
    add_filter( 'pre_option_update_core', create_function( '$a', "return null;" ) );
}

// Return header 403 for wrong login
function my_login_failed_403() {
    status_header( 403 );
}
add_action( 'wp_login_failed', 'my_login_failed_403' );

function wpa__prelicense() {
    if( function_exists('acf_pro_is_license_active') && !acf_pro_is_license_active() ) {
        $args = array(
            '_nonce'         => wp_create_nonce('activate_pro_licence'),
            'acf_license'    => base64_encode('order_id=37918|type=personal|date=2014-08-21 15:02:59'),
            'acf_version'    => acf_get_setting('version'),
            'wp_name'        => get_bloginfo('name'),
            'wp_url'         => home_url(),
            'wp_version'     => get_bloginfo('version'),
            'wp_language'    => get_bloginfo('language'),
            'wp_timezone'    => get_option('timezone_string'),
        );

        $response = acf_pro_get_remote_response( 'activate-license', $args );
        $response = json_decode($response, true);
        if( $response['status'] == 1 ) {
            acf_pro_update_license($response['license']);
        }
    }
}
add_action( 'admin_init', 'wpa__prelicense', 99 );
