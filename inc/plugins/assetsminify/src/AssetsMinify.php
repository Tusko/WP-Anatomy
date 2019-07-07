<?php

/**
 * Bootstrap class for AssetsMinify plugin.
 * It's the only entry point of this plugin.
 * A singleton class.
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class AssetsMinify extends AssetsMinify\Pattern\Singleton {

	public $js,
			$css;

	/**
	 * Constructor
	 */
	protected function __construct() {


		if( ! is_admin()) {
			return new AssetsMinify\Init;
		}

		$this->loadAdmin();

		add_action('admin_bar_menu', array($this, 'wpa_remove_cache_btn'), 80);


		if(isset($_GET['empty_cache'])) {

			$uploadsDir = wp_upload_dir();
			$filesList  = glob($uploadsDir['basedir'] . '/am_assets/' . "*.*");
			if($filesList !== false) {
				array_map('unlink', $filesList);
			}

			$purgeCacheResponse = 'Cache plugins disabled';

//			// if W3 Total Cache is being used, clear the cache
			if(function_exists('w3tc_pgcache_flush')) {
				w3tc_pgcache_flush();
				$purgeCacheResponse = 'W3 Total Cache removed';
			} else if(class_exists('Cache_Enabler', 'cache_autoload')) {
				do_action('ce_clear_cache');
				$purgeCacheResponse = 'Cache Enabler removed';
			} else if(class_exists('LiteSpeed_Cache')) {
				do_action('edit_post');
				do_action('litespeed_cache_api_purge');

				echo '<script>window.onload = function(){ if(location.search.indexOf(\'purge_all\') === -1) window.location = jQuery("#wp-admin-bar-litespeed-purge-all > a").attr("href") }</script>';

				$purgeCacheResponse = 'LiteSpeed Cache removed';
			} // if WP Super Cache is being used, clear the cache
			else if(function_exists('wp_cache_clean_cache')) {
				global $file_prefix, $supercachedir;
				if(empty($supercachedir) && function_exists('get_supercache_dir')) {
					$supercachedir = get_supercache_dir();
				}
				wp_cache_clean_cache($file_prefix);
				$purgeCacheResponse = 'WP Super Cache removed';
			} else if(class_exists('WpeCommon')) {
				//be extra careful, just in case 3rd party changes things on us
				if(method_exists('WpeCommon', 'purge_memcached')) {
					WpeCommon::purge_memcached();
				}
				if(method_exists('WpeCommon', 'clear_maxcdn_cache')) {
					WpeCommon::clear_maxcdn_cache();
				}
				if(method_exists('WpeCommon', 'purge_varnish_cache')) {
					WpeCommon::purge_varnish_cache();
				}
				$purgeCacheResponse = 'WP Engine Cache removed';
			} else if(method_exists('WpFastestCache', 'deleteCache') && ! empty($wp_fastest_cache)) {
				$wp_fastest_cache->deleteCache();
				$purgeCacheResponse = 'WP Fastest Cache removed';
			}

			add_action('admin_notices', function() use ($filesList, $purgeCacheResponse) {
				echo '<div class="error">
                    <h4>✖ Cache removed!</h4>
                    ' . (! empty($purgeCacheResponse) ? '<h4>✖ ' . $purgeCacheResponse . '.</h4>' : '') . '
                    <p>', count($filesList), ' files was removed.</p><p>Reload your <a href="' . site_url('/') . '" target="">homepage</a> to refrefh cache.</p></div>';
			});
		}
	}

	/**
	 * Initialize the admin panel
	 */
	public function loadAdmin() {
		new AssetsMinify\Admin;
	}

	public function wpa_remove_cache_btn($wp_admin_bar) {
		$args = array(
				'id'    => 'am-clear-cache',
				'title' => '&#10006; Clear Assets Cache',
				'href'  => get_admin_url() . '?empty_cache=true'
		);
		$wp_admin_bar->add_node($args);
	}

}