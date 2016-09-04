<?php
/*
 * Function creates post duplicate as a draft and redirects then to the edit post screen
 */
function tt_wp_duplicate_posts(){
    global $wpdb;
    if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'tt_wp_duplicate_posts' == $_REQUEST['action'] ) ) ) {
        wp_die('No post to duplicate has been supplied!');
    }

    /*
     * get the original post id
     */
    $post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
    /*
     * and all the original post data then
     */
    $post = get_post( $post_id );

    /*
     * if you don't want current user to be the new post author,
     * then change next couple of lines to this: $new_post_author = $post->post_author;
     */
    $current_user = wp_get_current_user();
    $new_post_author = $current_user->ID;

    /*
     * if post data exists, create the post duplicate
     */
    if (isset( $post ) && $post != null) {

        /*
         * new post data array
         */
        $args = array(
            'comment_status' => $post->comment_status,
            'ping_status'    => $post->ping_status,
            'post_author'    => $new_post_author,
            'post_content'   => $post->post_content,
            'post_excerpt'   => $post->post_excerpt,
            'post_name'      => $post->post_name,
            'post_parent'    => $post->post_parent,
            'post_password'  => $post->post_password,
            'post_status'    => $post->post_status,
            'post_title'     => $post->post_title,
            'post_type'      => $post->post_type,
            'to_ping'        => $post->to_ping,
            'menu_order'     => $post->menu_order
        );

        /*
         * insert the post by wp_insert_post() function
         */
        $new_post_id = wp_insert_post( $args );

        /*
         * get all current post terms ad set them to the new post draft
         */
        $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        }

        /*
         * duplicate all post meta just in two SQL queries
         */
        $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
        if (count($post_meta_infos)!=0) {
            $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
            foreach ($post_meta_infos as $meta_info) {
                $meta_key = $meta_info->meta_key;
                $meta_value = addslashes($meta_info->meta_value);
                $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
            }
            $sql_query.= implode(" UNION ALL ", $sql_query_sel);
            $wpdb->query($sql_query);
        }


        /*
         * finally, redirect to the edit post screen for the new draft
         */
        wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
        exit;
    } else {
        wp_die('Post creation failed, could not find original post: ' . $post_id);
    }
}
add_action( 'admin_action_tt_wp_duplicate_posts', 'tt_wp_duplicate_posts' );

/*
 * Add the duplicate link to action list for post_row_actions
 */
function tt_wp_duplicate_post_link( $actions, $post ) {
    if (current_user_can('edit_posts')) {
        $actions['duplicate'] = '<a href="admin.php?action=tt_wp_duplicate_posts&amp;post=' . $post->ID . '" rel="permalink"><span class="dashicons dashicons-arrow-left-alt2" style="font-size: 8px;vertical-align: baseline"></span>Duplicate<span class="dashicons dashicons-arrow-right-alt2" style="font-size: 8px;vertical-align: baseline"></span></a>';
    }
    return $actions;
}

add_filter('post_row_actions', 'tt_wp_duplicate_post_link', 10, 2 );
add_filter('page_row_actions', 'tt_wp_duplicate_post_link', 10, 2);

/*
Plugin Name: Duplicate Menu
Plugin URI: https://github.com/jchristopher/duplicate-menu
Description: Easily duplicate your WordPress Menus
Author: Jonathan Christopher
Version: 0.2
Author URI: http://mondaybynoon.com
*/

/*  Copyright 2011-2014 Jonathan Christopher (email : jonathan@mondaybynoon.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function duplicate_menu_options_page() {
    add_theme_page( 'Duplicate Menu', 'Duplicate Menu', 'manage_options', 'duplicate-menu', array( 'DuplicateMenu', 'options_screen' ) );
}

add_action( 'admin_menu', 'duplicate_menu_options_page' );

/**
* Duplicate Menu
*/
class DuplicateMenu {

    function duplicate( $id = null, $name = null ) {

        // sanity check
        if ( empty( $id ) || empty( $name ) ) {
            return false;
        }

        $id = intval( $id );
        $name = sanitize_text_field( $name );
        $source = wp_get_nav_menu_object( $id );
        $source_items = wp_get_nav_menu_items( $id );
        $new_id = wp_create_nav_menu( $name );

        if ( ! $new_id ) {
            return false;
        }

        // key is the original db ID, val is the new
        $rel = array();

        $i = 1;
        foreach ( $source_items as $menu_item ) {
            $args = array(
                'menu-item-db-id'       => $menu_item->db_id,
                'menu-item-object-id'   => $menu_item->object_id,
                'menu-item-object'      => $menu_item->object,
                'menu-item-position'    => $i,
                'menu-item-type'        => $menu_item->type,
                'menu-item-title'       => $menu_item->title,
                'menu-item-url'         => $menu_item->url,
                'menu-item-description' => $menu_item->description,
                'menu-item-attr-title'  => $menu_item->attr_title,
                'menu-item-target'      => $menu_item->target,
                'menu-item-classes'     => implode( ' ', $menu_item->classes ),
                'menu-item-xfn'         => $menu_item->xfn,
                'menu-item-status'      => $menu_item->post_status
            );

            $parent_id = wp_update_nav_menu_item( $new_id, 0, $args );

            $rel[$menu_item->db_id] = $parent_id;

            // did it have a parent? if so, we need to update with the NEW ID
            if ( $menu_item->menu_item_parent ) {
                $args['menu-item-parent-id'] = $rel[$menu_item->menu_item_parent];
                $parent_id = wp_update_nav_menu_item( $new_id, $parent_id, $args );
            }

            // allow developers to run any custom functionality they'd like
            do_action( 'duplicate_menu_item', $menu_item, $args );

            $i++;
        }

        return $new_id;
    }

    function options_screen() {
        $nav_menus = wp_get_nav_menus();
?>
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2><?php _e( 'Duplicate Menu' ); ?></h2>

    <?php if ( ! empty( $_POST ) && wp_verify_nonce( $_POST['duplicate_menu_nonce'], 'duplicate_menu' ) ) : ?>
    <?php
        $source         = intval( $_POST['source'] );
        $destination    = sanitize_text_field( $_POST['new_menu_name'] );

        // go ahead and duplicate our menu
        $duplicator = new DuplicateMenu();
        $new_menu_id = $duplicator->duplicate( $source, $destination );
    ?>

    <div id="message" class="updated"><p>
        <?php if ( $new_menu_id ) : ?>
        <?php _e( 'Menu Duplicated' ) ?>. <a href="nav-menus.php?action=edit&amp;menu=<?php echo absint( $new_menu_id ); ?>"><?php _e( 'View' ) ?></a>
        <?php else: ?>
        <?php _e( 'There was a problem duplicating your menu. No action was taken.' ) ?>.
        <?php endif; ?>
        </p></div>

    <?php endif; ?>

    <?php if ( empty( $nav_menus ) ) : ?>
    <p><?php _e( "You haven't created any Menus yet." ); ?></p>
    <?php else: ?>
    <form method="post" action="">
        <?php wp_nonce_field( 'duplicate_menu','duplicate_menu_nonce' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="source"><?php _e( 'Duplicate this menu' ); ?>:</label>
                </th>
                <td>
                    <select name="source">
                        <?php foreach ( (array) $nav_menus as $_nav_menu ) : ?>
                        <option value="<?php echo esc_attr($_nav_menu->term_id) ?>">
                            <?php echo esc_html( $_nav_menu->name ); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <span style="display:inline-block; padding:0 10px;"><?php _e( 'and call it' ); ?></span>
                    <input name="new_menu_name" type="text" id="new_menu_name" value="" class="regular-text" />
                </td>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button-primary" value="Duplicate Menu" />
        </p>
    </form>
    <?php endif; ?>
</div>
<?php }
}
