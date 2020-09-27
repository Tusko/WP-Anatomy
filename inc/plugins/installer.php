<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see        http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1 for parent theme wpa
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 *
 * Depending on your implementation, you may want to change the include call:
 *
 * Parent Theme:
 * require_once get_template_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Child Theme:
 * require_once get_stylesheet_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Plugin:
 * require_once dirname( __FILE__ ) . '/path/to/class-tgm-plugin-activation.php';
 */
require_once dirname(__FILE__) . '/class-tgm-plugin-activation.php';

add_action('tgmpa_register', 'wpa_register_required_plugins');

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 * If you are not changing anything in the configuration array, you can remove the array and remove the
 * variable from the function call: `tgmpa( $plugins );`.
 * In that case, the TGMPA default settings will be used.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function wpa_register_required_plugins() {

	$plugins = array(
		array(
			'name'     => 'Optimus – WordPress Image Optimizer',
			'slug'     => 'optimus',
			'required' => false,
		),
		array(
			'name'     => 'TinyMCE Advanced',
			'slug'     => 'tinymce-advanced',
			'required' => false,
		),
		array(
			'name'     => 'WP Migrate DB',
			'slug'     => 'wp-migrate-db',
			'required' => false,
		),
		array(
			'name'        => 'Yoast SEO',
			'slug'        => 'wordpress-seo',
			'required'    => false,
			'is_callable' => 'wpseo_init',
		),
		array(
			'name'               => 'Advanced Custom Fields: PRO',
			'slug'               => 'advanced-custom-fields-pro',
			'source'             => get_stylesheet_directory() . '/inc/plugins/advanced-custom-fields-pro.zip',
			'required'           => true,
			'version'            => '',
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => 'http://www.advancedcustomfields.com/pro/', // If set, overrides default API URL and points to an external URL.
		),
		array(
			'name'         => 'Gravity Forms',
			'slug'         => 'gravityforms',
			'source'       => get_stylesheet_directory() . '/inc/plugins/gravityforms.zip',
			'required'     => false,
			'external_url' => 'https://gravityforms.com', // If set, overrides default API URL and points to an external URL.
		),
		array(
			'name'        => 'GravityWP – CSS Selector',
			'slug'        => 'gravitywp-css-selector',
			'required'    => false,
			'is_callable' => 'gf_upgrade'
		)
	);

	$config = array(
		'id'           => 'wpa_inst',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'wpa-install-plugins',                    // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'parent_slug'  => 'themes.php',            // Parent menu slug.
		'capability'   => 'edit_theme_options', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);
	tgmpa($plugins, $config);
}