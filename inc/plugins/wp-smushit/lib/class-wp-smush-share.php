<?php
/**
 * @package WP Smush
 *
 * @version 2.4
 *
 * @author Umesh Kumar <umesh@incsub.com>
 *
 * @copyright (c) 2016, Incsub (http://incsub.com)
 */
if ( ! class_exists( 'WpSmushShare' ) ) {

	class WpSmushShare {
		function __construct() {}

		function share_widget() {
			global $wpsmushit_admin, $wpsmush_stats;
			$savings     = $wpsmushit_admin->global_stats_from_ids();
			$image_count = $wpsmush_stats->smushed_count();

			//If there is any saving, greater than 1Mb, show stats
			if ( empty( $savings ) || empty( $savings['bytes'] ) || $savings['bytes'] <= 1048576 || $image_count <= 1 || ! is_super_admin() ) {
				return false;
			}
			$message   = sprintf( esc_html__( "%s, you've smushed %d images and saved %s in total. Help your friends save bandwidth easily, and help me in my quest to Smush the internet!", "wp-smushit" ), $wpsmushit_admin->get_user_name(), $image_count, $savings['human'] );
			$share_msg = sprintf( esc_html__( 'I saved %s on my site with WP Smush ( %s ) - wanna make your website smaller and faster?', "wp-smushit" ) , $savings['human'], urlencode( "https://wordpress.org/plugins/wp-smushit/" ) );
			$url       = urlencode( "http://wordpress.org/plugins/wp-smushit/" ); ?>
			<section class="dev-box" id="wp-smush-share-widget">
			<div class="box-content roboto-medium">
				<p class="wp-smush-share-message"><?php echo $message; ?></p>
				<div class="wp-smush-share-buttons-wrapper">
					<!-- Twitter Button -->
					<a href="https://twitter.com/intent/tweet?text=<?php echo esc_attr( $share_msg ); ?>"
					   class="button wp-smush-share-button" id="wp-smush-twitter-share">
						<i class="dev-icon dev-icon-twitter"></i><?php esc_html_e( "TWEET", "wp-smushit" ); ?></a>
					<!-- Facebook Button -->
					<a href="http://www.facebook.com/sharer.php?s=100&p[title]=WP Smush&p[url]=http://wordpress.org/plugins/wp-smushit/"
					   class="button wp-smush-share-button" id="wp-smush-facebook-share">
						<i class="dev-icon dev-icon-facebook"></i><?php esc_html_e( "SHARE", "wp-smushit" ); ?></a>
					<a href="whatsapp://send?text='<?php echo esc_attr( $share_msg ); ?>'"
					   class="button wp-smush-share-button"
					   id="wp-smush-whatsapp-share">
						<?php esc_html_e( "WhatsApp", "wp-smushit" ); ?></a>
				</div>
			</div>
			</section><?php
		}

	}

	global $wpsmush_share;
	$wpsmush_share = new WpSmushShare();
}
