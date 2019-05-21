<?php
// Wordpress ?s= redirect to /search/
function wpa_search_redirect() {
    global $wp_rewrite;
    if (!isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->using_permalinks()) { return; }
    $search_base = $wp_rewrite->search_base;
    if (is_search() && !is_admin() && strpos($_SERVER['REQUEST_URI'], "/{$search_base}/") === false) {
        wp_redirect(home_url("/{$search_base}/" . urlencode(get_query_var('s'))));
        exit();
    }
}
add_action('template_redirect', 'wpa_search_redirect');

// Fix for empty search queries redirecting to home page
function wpa_request_filter($query_vars) {
    if (isset($_GET['s']) && empty($_GET['s']) && !is_admin()) {
        $query_vars['s'] = ' ';
    }
    return $query_vars;
}
add_filter('request', 'wpa_request_filter');

//  Custom AJAX search
add_filter( 'posts_search', '__search_by_title_only', 500, 2 );

function __search_by_title_only( $search, $wp_query ){
    global $wpdb;
    if ( empty( $search ) ) return $search;
    $q = $wp_query->query_vars;
    $n = ! empty( $q['exact'] ) ? '' : '%';
    $search = $searchand = '';
    foreach ( (array) $q['search_terms'] as $term ) {
        $term = esc_sql( $wpdb->esc_like( $term ) );
        $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
        $searchand = ' AND ';
    }
    if ( !empty( $search ) ) {
        $search = " AND ({$search}) ";
        if ( ! is_user_logged_in() )
            $search .= " AND ($wpdb->posts.post_password = '') ";
    }
    return $search;
}

function cf_search_join( $join ) {
    global $wpdb;
    $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    return $join;
}

function cf_search_where( $where ) {
    global $pagenow, $wpdb;
    $where = preg_replace(
        "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
        "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    return $where;
}

function cf_search_distinct( $where ) {
    global $wpdb;
    return "DISTINCT";
}

function wpa_ajax_search(){
    extract($_POST);
    add_filter( 'posts_join', 'cf_search_join' );
    add_filter( 'posts_where', 'cf_search_where' );
    add_filter( 'posts_distinct', 'cf_search_distinct' );
    $allsearch = new WP_Query("s=".$key."&showposts=-1&post_type=any&post_status=publish");
    if($allsearch->have_posts()): while($allsearch->have_posts()) : $allsearch->the_post();
    global $post; ?>
<p class="cfx">
    <a href="<?php the_permalink(); ?>">
        <?php if( has_post_thumbnail() ) the_post_thumbnail('thumbnail'); ?>
        <span><?php the_title(); ?></span>
    </a>
</p>
<?php endwhile; else :
    echo '<mark>There were no search results. <br />Please try using more general terms to get more results.</mark>';
    endif;
    remove_filter( 'posts_join', 'cf_search_join' );
    remove_filter( 'posts_where', 'cf_search_where' );
    remove_filter( 'posts_distinct', 'cf_search_distinct' );
    exit();
}
add_action('wp_ajax_wpa_ajax_search', 'wpa_ajax_search');
add_action('wp_ajax_nopriv_wpa_ajax_search', 'wpa_ajax_search');