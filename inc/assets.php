<?php

// custom js/stylesheet
function tt_add_jscss() {
    if (!is_admin()) {
        wp_deregister_script( 'jquery' );
    }

    if(defined('WPCF7_VERSION')) {
        wp_deregister_style( 'contact-form-7' );
    }

    if(defined('QTX_VERSION')) {
        wp_deregister_style('qtranslate-style');
    }

    if(defined('GOOGLEMAPS')) {
        wp_enqueue_script('googlemaps', '//maps.googleapis.com/maps/api/js?v=3.exp', false, null, false);
    }

    wp_enqueue_script('jquery', get_stylesheet_directory_uri(). '/js/libs/_jquery.js', false, null, false);

    if($js_lib = directoryToArray( get_stylesheet_directory(),'/js/libs/', array('js') )) {
        foreach($js_lib as $name => $js){
            wp_enqueue_script($name, $js, array('jquery'), null, true);
        }
    }

    wp_enqueue_script('libs', get_stylesheet_directory_uri(). '/js/lib.js', array('jquery'), null, true);
    wp_enqueue_script('init', get_stylesheet_directory_uri(). '/js/init.js', array('libs'), null, true);

    if($style_lib = directoryToArray( get_stylesheet_directory(),'/style/libs/', array('css', 'scss') )) {
        foreach($style_lib as $name => $lib){
            wp_enqueue_style($name, $lib );
        }
    }

    wp_enqueue_style('main', get_stylesheet_directory_uri(). '/style/style.scss' );

    if(class_exists('Woocommerce')) {
        wp_enqueue_style('custom-woo-styles', get_stylesheet_directory_uri(). '/style/woo.scss' );
        wp_enqueue_script('custom-woo-scripts', get_stylesheet_directory_uri(). '/js/woo.js', false, null, true);
    }

    wp_enqueue_style('responsive', get_stylesheet_directory_uri(). '/style/rwd.scss' );
}
add_action('wp_enqueue_scripts', 'tt_add_jscss');

/* ACF Repeater Styles */
function acf_repeater_even() {
    $scheme = get_user_option( 'admin_color' );
    $color = '';
    switch ($scheme) {
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
    echo '<style>.acf-repeater > table > tbody > tr:nth-child(even) > td.order {color: #fff !important;background-color: '.$color.' !important; text-shadow: none}.acf-fc-layout-handle {color: #fff !important;background-color: #23282d!important; text-shadow: none}</style>';
}
add_action('admin_footer', 'acf_repeater_even');

function getFileExt($path) {
    $pos = strrpos($path, ".");
    if($pos === FALSE){
        return "";
    }else{
        return substr($path, $pos + 1);
    }
}

function fileNotIgnore($name) {
    if( substr( $name, 0, 1 ) !== "_" ) {
        return $name;
    }
}

function directoryToArray( $abs, $directory, $filterMap = NULL ) {
    $assets = array();
    $dir = $abs . $directory;
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && fileNotIgnore($file) && !is_dir($dir. "/" . $file)) {
                if( $filterMap ) {
                    if(in_array(getFileExt($file), $filterMap)) $assets[basename($file)] = get_stylesheet_directory_uri() . $directory . $file;
                } else {
                    $assets[basename($file)] = get_stylesheet_directory_uri() . $directory . $file;
                }
            }
        }
        closedir($handle);
    }
    return $assets;
}
