<?php
//Example for Custom Post Type with Taxonimies

/*
    *** You van use dash-icons https://developer.wordpress.org/resource/dashicons/
*/
add_action( 'init', 'register_cpts' );
function register_cpts() {

    //custom taxonomy attached to CPT
    $taxname = 'Taxonomy Name';
    $taxlabels = array(
        'name'                          => $taxname,
        'singular_name'                 => $taxname,
        'search_items'                  => 'Search '.$taxname,
        'popular_items'                 => 'Popular '.$taxname,
        'all_items'                     => 'All '.$taxname.'s',
        'parent_item'                   => 'Parent '.$taxname,
        'edit_item'                     => 'Edit '.$taxname,
        'update_item'                   => 'Update '.$taxname,
        'add_new_item'                  => 'Add New '.$taxname,
        'new_item_name'                 => 'New '.$taxname,
        'separate_items_with_commas'    => 'Separate '.$taxname.'s with commas',
        'add_or_remove_items'           => 'Add or remove '.$taxname.'s',
        'choose_from_most_used'         => 'Choose from most used '.$taxname.'s'
    );
    $taxarr = array(
        'label'                         => $taxname,
        'labels'                        => $taxlabels,
        'public'                        => true,
        'hierarchical'                  => true,
        'show_in_nav_menus'             => true,
        'args'                          => array( 'orderby' => 'term_order' ),
        'query_var'                     => true,
        'show_ui'                       => true,
        'rewrite'                       => true,
        'show_admin_column'             => true
    );
    register_taxonomy( 'taxonomy_name', 'custom_post_type', $taxarr );

    register_post_type( 'custom_post_type',
        array(
            'labels' => array(
            'name' => 'Custom Post Type',
            'singular_name' => 'Custom Post Type',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New',
            'edit_item' => 'Edit',
            'new_item' => 'New',
            'all_items' => 'All',
            'view_item' => 'View',
            'search_items' => 'Search',
            'not_found' =>  'Not found',
            'not_found_in_trash' => 'No found in Trash',
            'parent_item_colon' => '',
            'menu_name' => 'Custom Post Type'
        ),
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'supports'              => array( 'title', 'editor', 'thumbnail' ),
        'rewrite'               => array( 'slug' => 'permalink' ),
        'has_archive'           => true,
        'hierarchical'          => true,
        'show_in_nav_menus'     => true,
        'capability_type'       => 'page',
        'query_var'             => true,
        'menu_icon'             => 'dashicons-admin-page',
    ));

    if( defined('WP_DEBUG') && true !== WP_DEBUG) {
        flush_rewrite_rules();
    }

}

function workd_restrict_manage_posts() {
    global $typenow;
    $args=array( 'public' => true, '_builtin' => false );
    $post_types = get_post_types($args);
    if ( in_array($typenow, $post_types) ) {
    $filters = get_object_taxonomies($typenow);
        foreach ($filters as $tax_slug) {
            $tax_obj = get_taxonomy($tax_slug);
            wp_dropdown_categories(array(
                'show_option_all' => __('Filter -> '.$tax_obj->label ),
                'taxonomy' => $tax_slug,
                'name' => $tax_obj->name,
                'orderby' => 'term_order',
                'selected' => isset($_GET[$tax_obj->query_var])?$_GET[$tax_obj->query_var]:'',
                'hierarchical' => $tax_obj->hierarchical,
                'show_count' => false,
                'hide_empty' => true
            ));
        }
    }
}
function workd_convert_restrict($query) {
    global $pagenow;
    global $typenow;
    if ($pagenow=='edit.php') {
        $filters = get_object_taxonomies($typenow);
        foreach ($filters as $tax_slug) {
            $var = &$query->query_vars[$tax_slug];
            if ( isset($var) && $var>0) {
                $term = get_term_by('id',$var,$tax_slug);
                $var = $term->slug;
            }
        }
    }
    return $query;
}
add_action('restrict_manage_posts', 'workd_restrict_manage_posts');
add_filter('parse_query', 'workd_convert_restrict');
