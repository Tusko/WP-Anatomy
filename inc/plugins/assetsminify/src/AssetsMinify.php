<?php
/**
 * Bootstrap class for AssetsMinify plugin.
 * It's the only entry point of this plugin.
 * A singleton class.
 *
 * @author Alessandro Carbone <ale.carbo@gmail.com>
 */

class AssetsMinify extends AssetsMinify\Pattern\Singleton {

    /**
     * Constructor
     */
    protected function __construct() {
        if ( !is_admin() )
            return new AssetsMinify\Init;

        $this->loadAdmin();

        add_action('admin_bar_menu', array( $this, 'wpa_remove_cache_btn' ), 80);
    }

    /**
     * Initialize the admin panel
     */
    public function loadAdmin() {
        new AssetsMinify\Admin;
    }

    public function wpa_remove_cache_btn($wp_admin_bar){
        $args = array(
            'id' => 'am-clear-cache',
            'title' => '&#10006; Clear Assets Cache',
            'href' => get_admin_url() . '?empty_cache=true'
        );
        $wp_admin_bar->add_node($args);
    }

}
