<?php
namespace AssetsMinify;

use AssetsMinify\Cache;
use AssetsMinify\Assets\Htaccess;

/**
 * Admin's page manager.
 * Prints out every field managed from AssetsMinify's admin page
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */
class Admin {

    /**
     * Admin options provided
     */
    protected $options = array(
        'am_use_compass',
        'am_compass_path',
        'am_sass_path',
        'am_coffeescript_path',
        'am_async_flag_header',
        'am_async_flag_footer',
        'am_leverage_browser_caching',
        'am_enable_compression',
        'am_compress_styles',
        'am_compress_scripts',
        'am_files_to_exclude',
        'am_log',
        'am_dev_mode',
    );

    private $systemMessage = array();
    public $htaccess;

    /**
     * Constructor
     */
    public function __construct() {
        // Cache manager
        $this->cache = new Cache;
        $this->htaccess = new Htaccess;

        add_action('admin_init', array( $this, 'options') );
        add_action('admin_menu', array( $this, 'menu') );
        add_action( 'update_option_am_leverage_browser_caching', array( $this, 'lbc') );
        add_action( 'update_option_am_enable_compression', array( $this, 'lbc') );

        if ( isset($_GET['empty_cache']) && current_user_can( 'activate_plugins' ) ) {
            $uploadsDir = wp_upload_dir();
            $filesList = glob($uploadsDir['basedir'] . '/am_assets/' . "*.*");
            if ( $filesList !== false )
                array_map('unlink', $filesList);

            global $wp_fastest_cache;
            $purgeCacheResponse = '';

            // if W3 Total Cache is being used, clear the cache
            if ( function_exists( 'w3tc_pgcache_flush' ) ) {
                w3tc_pgcache_flush();
                $purgeCacheResponse = 'W3 Total Cache removed';
            }
            // if WP Super Cache is being used, clear the cache
            else if ( function_exists( 'wp_cache_clean_cache' ) ) {
                global $file_prefix, $supercachedir;
                if ( empty( $supercachedir ) && function_exists( 'get_supercache_dir' ) ) {
                    $supercachedir = get_supercache_dir();
                }
                wp_cache_clean_cache( $file_prefix );
                $purgeCacheResponse = 'WP Super Cache removed';
            }
            else if ( class_exists( 'WpeCommon' ) ) {
                //be extra careful, just in case 3rd party changes things on us
                if ( method_exists( 'WpeCommon', 'purge_memcached' ) ) {
                    WpeCommon::purge_memcached();
                }
                if ( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) ) {
                    WpeCommon::clear_maxcdn_cache();
                }
                if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
                    WpeCommon::purge_varnish_cache();
                }
                $purgeCacheResponse = 'WP Engine Cache removed';
            }
            else if ( method_exists( 'WpFastestCache', 'deleteCache' ) && !empty( $wp_fastest_cache ) ) {
                $wp_fastest_cache->deleteCache();
                $purgeCacheResponse = 'WP Fastest Cache removed';
            }

            add_action('admin_notices', function() use ($filesList, $purgeCacheResponse) {
                echo '<div class="error">
                    <h4>✖ Cache removed!</h4>
                    '.(!empty($purgeCacheResponse)?'<h4>✖ '.$purgeCacheResponse.'.</h4>':'').'
                    <p>', count($filesList), ' files was removed.</p><p>Reload your <a href="' . site_url('/') . '" target="">homepage</a> to refrefh cache.</p></div>';
            });
        }

    }

    // Leverage browser caching
    public function lbc() {

        $this->systemMessage = $this->htaccess->modifyHtaccess();

        $message = __( $this->systemMessage[0], 'adsense-theme' );
        $type = $this->systemMessage[1];
        add_settings_error(
            'lbc-notice',
            esc_attr( 'settings_updated' ),
            $message,
            $type
        );

    }

    /**
     * Initalizes the plugin's admin menu
     */
    public function menu() {
        add_options_page('AssetsMinify', 'AssetsMinify', 'administrator', 'assets-minify', array( $this, 'settings') );
    }

    /**
     * Outputs the tpl provided
     *
     * @param string $tplFile The template to output
     */
    protected function tpl( $tplFile ) {
        include plugin_dir_path( dirname(dirname(__FILE__)) ) . 'templates/' . $tplFile;
    }

    /**
     * Registers plugin's options
     */
    public function options() {
        foreach ( $this->options as $opt ) {
            register_setting('am_options_group', $opt);
        }
        return $this->options;
    }

    /**
     * Defines plugin's settings
     */
    public function settings() {
        $this->tpl( "settings.phtml" );
    }
}
