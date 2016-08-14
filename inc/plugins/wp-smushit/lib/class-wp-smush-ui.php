<?php
/**
 * @package WP Smush
 * @subpackage Admin
 * @version 1.0
 *
 * @author Umesh Kumar <umesh@incsub.com>
 *
 * @copyright (c) 2016, Incsub (http://incsub.com)
 */
if ( ! class_exists( 'WpSmushBulkUi' ) ) {
	/**
	 * Show settings in Media settings and add column to media library
	 *
	 */

	/**
	 * Class WpSmushBulkUi
	 */
	class WpSmushBulkUi {

		/**
		 * Prints the Header Section for a container as per the Shared UI
		 *
		 * @param string $classes Any additional classes that needs to be added to section
		 * @param string $heading Box Heading
		 * @param string $sub_heading Any additional text to be shown by the side of Heading
		 * @param bool $dismissible If the Box is dimissible
		 *
		 * @return string
		 */
		function container_header( $classes = '', $id = '', $heading = '', $sub_heading = '', $dismissible = false ) {
			if ( empty( $heading ) ) {
				return '';
			}
			echo '<section class="dev-box ' . $classes . ' wp-smush-container" id="' . $id . '">'; ?>
<div class="wp-smush-container-header box-title" xmlns="http://www.w3.org/1999/html">
			<h3 tabindex="0"><?php echo $heading ?></h3><?php
			//Sub Heading
			if ( ! empty( $sub_heading ) ) { ?>
				<div class="smush-container-subheading roboto-medium"><?php echo $sub_heading ?></div><?php
			}
			//Dismissible
			if ( $dismissible ) { ?>
				<div class="float-r smush-dismiss-welcome">
				<a href="#" title="<?php esc_html_e( "Dismiss Welcome notice", "wp-smushit" ); ?>">
					<i class="wdv-icon wdv-icon-fw wdv-icon-remove"></i>
				</a>
				</div><?php
			} ?>
			</div><?php
		}

		/**
		 *  Prints the content of WelCome Screen for New Installation
		 *  Dismissible by default
		 */
		function welcome_screen() {
			global $WpSmush;

			//Header Of the Box
			$this->container_header( 'wp-smush-welcome', 'wp-smush-welcome-box', esc_html__( "WELCOME", "wp-smushit" ), '', true );
			//Settings Page heading
			$plugin_name = $WpSmush->validate_install() ? "WP Smush Pro" : "WP Smush";
			?>
			<!-- Content -->
			<div class="box-content">
			<div class="row">
				<div class="wp-smush-welcome-content">
					<h4 class="roboto-condensed-regular"><?php esc_html_e( "OH YEAH, IT'S COMPRESSION TIME!", "wp-smushit" ); ?></h4>
					<p class="wp-smush-welcome-message roboto-medium"><?php printf( esc_html__( 'You\'ve just installed %3$s, the most popular image compression plugin for WordPress! %1$sChoose your desired settings%2$s and get smushing!', "wp-smushit" ), '<strong>', '</strong>', $plugin_name ); ?></p>
				</div>
			</div>
			</div><?php
			echo "</section>";
		}

		/**
		 * Bulk Smush UI and Progress bar
		 */
		function bulk_smush_container() {
			global $WpSmush;

			//Subheading content
			$smush_individual_msg = sprintf( esc_html__( "Smush individual images via your %sMedia Library%s", "wp-smushit" ), '<a href="' . esc_url( admin_url( 'upload.php' ) ) . '" title="' . esc_html__( 'Media Library', 'wp-smushit' ) . '">', '</a>' );

			$class = $WpSmush->validate_install() ? 'bulk-smush-wrapper wp-smush-pro-install' : 'bulk-smush-wrapper';

			//Contianer Header
			$this->container_header( $class, 'wp-smush-bulk-wrap-box', esc_html__( "BULK SMUSH", "wp-smushit" ), $smush_individual_msg ); ?>

			<div class="box-container"><?php
			$this->bulk_smush_content(); ?>
			</div><?php
			echo "</section>";
		}

		/**
		 * All the settings for Basic and Advanced Users
		 */
		function settings_ui() {
			global $WpSmush;
			$class = $WpSmush->validate_install() ? 'smush-settings-wrapper wp-smush-pro' : 'smush-settings-wrapper';
			$this->container_header( $class, 'wp-smush-settings-box', esc_html__( "SETTINGS", "wp-smushit" ), '' );
			// display the options
			$this->options_ui();
		}

		/**
		 * Outputs the Smush stats for the site
		 */
		function smush_stats_container() {
			global $WpSmush, $wpsmushit_admin, $wpsmush_stats;

			//If we have resmush list, smushed_count = totalcount - resmush count, else smushed_count
			$smushed_count = ( $resmush_count = count( $wpsmushit_admin->resmush_ids ) ) > 0 ? $wpsmushit_admin->total_count - ( $resmush_count + $wpsmushit_admin->remaining_count ) : $wpsmushit_admin->smushed_count;
			$smushed_count = $smushed_count > 0 ? $smushed_count : 0;

			$button = '<span class="spinner"></span><button tooltip="' . esc_html__( "Lets you check if any images can be further optimised. Useful after changing settings.", "wp-smushit" ) . '" class="wp-smush-title button button-grey button-small wp-smush-scan">' . esc_html__( "RE-CHECK IMAGES", "wp-smushit" ) . '</button>';
			$this->container_header( 'smush-stats-wrapper', 'wp-smush-stats-box', esc_html__( "STATS", "wp-smushit" ), $button );
			$dasharray = 125.663706144;
			$dash_offset = $wpsmushit_admin->total_count > 0 ? $dasharray - ( $dasharray * ( $smushed_count / $wpsmushit_admin->total_count) ) : $dasharray;
			$tooltip = $wpsmushit_admin->stats['total_images'] > 0 ? 'tooltip="' . sprintf( esc_html__(" You've smushed %d images in total", "wp-smushit"), $wpsmushit_admin->stats['total_images'] ) . '"' : ''; ?>
			<div class="box-content">
			<div class="row smush-total-savings smush-total-reduction-percent">

			<div class="wp-smush-current-progress" <?php echo $tooltip; ?> >
				<div class="wp-smushed-progress">
					<div class="wp-smush-score inside">
						<div class="tooltip-box">
							<div class="wp-smush-optimisation-progress">
								<div class="wp-smush-progress-circle">
									<svg class="wp-smush-svg" xmlns="http://www.w3.org/2000/svg" width="50" height="50">
										<circle class="wp-smush-svg-circle" r="20" cx="25" cy="25" fill="transparent" stroke-dasharray="0" stroke-dashoffset="0"></circle>
										<!-- Stroke Dasharray is 2 PI r -->
										<circle class="wp-smush-svg-circle wp-smush-svg-circle-progress" r="20" cx="25" cy="25" fill="transparent" stroke-dasharray="<?php echo $dasharray; ?>" style="stroke-dashoffset: <?php echo $dash_offset; ?>px;"></circle>
									</svg>
								</div>
							</div>
						</div><!-- end tooltip-box -->
					</div>
				</div>

				<div class="wp-smush-count-total">
					<div class="wp-smush-smush-stats-wrapper">
						<span class="wp-smush-optimised"><?php echo $smushed_count; ?></span>/<span><?php echo $wpsmushit_admin->total_count; ?></span>
					</div>
					<span class="total-stats-label"><strong><?php esc_html_e( "ATTACHMENTS SMUSHED", "wp-smushit" ); ?></strong></span>
				</div>
				</div>
			</div>
			<hr />
			<div class="row wp-smush-savings">
				<span class="float-l wp-smush-stats-label"><strong><?php esc_html_e("TOTAL SAVINGS", "wp-smushit");?></strong></span>
				<span class="float-r wp-smush-stats">
					<span class="wp-smush-stats-human">
						<?php echo $wpsmushit_admin->stats['human'] > 0 ? $wpsmushit_admin->stats['human'] : "0MB"; ?>
					</span>
					<span class="wp-smush-stats-sep">/</span>
					<span class="wp-smush-stats-percent"><?php echo $wpsmushit_admin->stats['percent'] > 0 ? number_format_i18n( $wpsmushit_admin->stats['percent'], 1, '.', '' ) : 0; ?></span>%
				</span>
			</div><?php
			/**
			 * Allows to hide the Super Smush stats as it might be heavy for some users
			 */
			if ( $WpSmush->validate_install() && apply_filters( 'wp_smush_show_lossy_stats', true ) ) {
				$wpsmushit_admin->super_smushed = $wpsmush_stats->super_smushed_count(); ?>
				<hr />
				<div class="row super-smush-attachments">
				<span class="float-l wp-smush-stats-label"><strong><?php esc_html_e( "ATTACHMENTS SUPER-SMUSHED", "wp-smushit" ); ?></strong></span>
				<span class="float-r wp-smush-stats<?php echo $WpSmush->lossy_enabled ? '' : ' wp-smush-lossy-disabled-wrap' ?>"><?php
					if ( $WpSmush->lossy_enabled ) {
						echo '<span class="smushed-count">' . intval( $wpsmushit_admin->super_smushed ) . '</span>/' . $wpsmushit_admin->total_count;
					} else {
						printf( esc_html__( "%sENABLE%s", "wp-smushit" ), '<button class="wp-smush-lossy-enable button button-small">', '</button>' );
					} ?>
				</span>
				</div><?php
			} ?>
			<hr />
			<div class="row smush-resize-savings">
				<span class="float-l wp-smush-stats-label"><strong><?php esc_html_e( "RESIZE SAVINGS", "wp-smushit" ); ?></strong></span>
				<span class="float-r wp-smush-stats"><?php
					if( !empty( $wpsmushit_admin->stats['resize_savings'] ) && $wpsmushit_admin->stats['resize_savings'] > 0 ) {
						echo $wpsmushit_admin->stats['resize_savings'];
					}else{
						if( !get_option( WP_SMUSH_PREFIX . 'resize' ) ) {
							//If Not enabled, Add a enable button
							printf( esc_html__( "%sENABLE%s", "wp-smushit" ), '<button class="wp-smush-resize-enable button button-small">', '</button>' );
						}else{
							echo "0MB";
						}
					} ?>
				</span>
			</div><?php
			if( $WpSmush->validate_install() && !empty( $wpsmushit_admin->stats['conversion_savings'] ) && $wpsmushit_admin->stats['conversion_savings'] > 0 ) { ?>
				<hr />
				<div class="row smush-conversion-savings">
					<span class="float-l wp-smush-stats-label"><strong><?php esc_html_e( "PNG TO JPEG SAVINGS", "wp-smushit" ); ?></strong></span>
					<span class="float-r wp-smush-stats"><?php echo $wpsmushit_admin->stats['conversion_savings'] > 0 ? $wpsmushit_admin->stats['conversion_savings'] : "0MB"; ?></span>
				</div><?php
			}
			//Pro Savings Expected: For free Version
			if ( ! $WpSmush->validate_install() ) {
				$savings = $wpsmushit_admin->stats['percent'] > 0 ? number_format_i18n( $wpsmushit_admin->stats['percent'], 1, '.', '' ) : 0;
				$savings_bytes = $wpsmushit_admin->stats['human'] > 0 ? $wpsmushit_admin->stats['bytes'] : "0";
				$show_pro_savings = false;
				//If the smush savings percent isn't empty and the percentage is below 45, double it
				if( !empty( $savings ) && $savings < 49 ) {
					$savings = 2 * $savings;
					$savings_bytes = 2 * $savings_bytes;
					$show_pro_savings = true;
				}elseif( !empty( $savings ) && $savings < 80  ){
					$savings = 1.1 * $savings;
					$savings_bytes = 1.1 * $savings_bytes;
					$show_pro_savings = true;
				}

				//If we have any savings
				if( $savings > 0 && $show_pro_savings ) {
					$upgrade_url = add_query_arg(
						array(
							'utm_source' => 'Smush-Free',
							'utm_medium' => 'Banner',
							'utm_campaign'=> 'pro-only-stats'
						),
						$wpsmushit_admin->upgrade_url
					);
					$pro_only = sprintf( esc_html__( '%sTRY PRO FREE%s', 'wp-smushit' ), '<a href="' . esc_url( $upgrade_url ) . '" target="_blank">', '</a>' ); ?>
					<hr />
					<div class="row smush-avg-pro-savings" tooltip="<?php esc_html_e("BASED ON AVERAGE SAVINGS IF YOU UPGRADE TO PRO", "wp-smushit"); ?>">
						<span class="float-l wp-smush-stats-label"><strong><?php esc_html_e( "PRO SAVINGS ESTIMATE", "wp-smushit" ); ?></strong><span class="wp-smush-stats-try-pro roboto-regular"><?php echo $pro_only; ?></span></span>
						<span class="float-r wp-smush-stats">
							<span class="wp-smush-stats-human">
								<?php echo $wpsmushit_admin->format_bytes( round( $savings_bytes, 1 ) ); ?>
							</span>
							<span class="wp-smush-stats-sep">/</span>
							<span class="wp-smush-stats-percent"><?php echo number_format_i18n( $savings, 1, '.', '' );  ?></span>%
						</span>
					</div><?php
				}
			}
			/**
			 * Allows you to output any content within the stats box at the end
			 */
			do_action( 'wp_smush_after_stats' );
			?>
			</div><?php
			echo "</section>";
		}

		/**
		 * Outputs the advanced settings for Pro users, Disabled for basic users by default
		 */
		function advanced_settings( $configure_screen = false ) {
			global $WpSmush, $wpsmushit_admin;

			//Content for the End of box container
			$div_end =
				wp_nonce_field( 'save_wp_smush_options', 'wp_smush_options_nonce', '', false ) .
				'<div class="wp-smush-submit-wrap">
					<input type="submit" id="wp-smush-save-settings" class="button button-grey"
					       value="' . esc_html__( 'UPDATE SETTINGS', 'wp-smushit' ) . '">
			        <span class="spinner"></span>
		        </div>
				</form>';
			//For Configuration screen we need to show the advanced settings in single box
			if ( ! $configure_screen ) {
				$div_end .= '</div><!-- Box Content -->
					</section><!-- Main Section -->';
			}

			//For Basic User, Show advanced settings in a separate box
			if ( ! $WpSmush->validate_install() ) {
				echo $div_end;
				$upgrade_url = add_query_arg(
					array(
						'utm_source' => 'Smush-Free',
						'utm_medium' => 'Banner',
						'utm_campaign'=> 'pro-only-advanced-settings'
					),
					$wpsmushit_admin->upgrade_url
				);
				$pro_only = sprintf( esc_html__( '%sTRY PRO FEATURES FREE%s', 'wp-smushit' ), '<a href="' . esc_url( $upgrade_url ) . '" target="_blank">', '</a>' );

				$this->container_header( 'wp-smush-premium', 'wp-smush-pro-settings-box', esc_html__( "ADVANCED SETTINGS", "wp-smushit" ), $pro_only, false ); ?>
				<div class="box-content"><?php
			}

			//Available advanced settings
			$pro_settings = array(
				'lossy',
				'original',
				'backup',
				'png_to_jpg',
				'nextgen'
			);

			if ( $WpSmush->validate_install() ) {
				echo "<hr />";
			}

			$transparent_png = get_option( WP_SMUSH_PREFIX . 'transparent_png', array('convert' => '', 'background' => '' ) );

			//Iterate Over all the available settings, and print a row for each of them
			foreach ( $pro_settings as $setting_key ) {
				if ( isset( $wpsmushit_admin->settings[ $setting_key ] ) ) {
					$setting_m_key = WP_SMUSH_PREFIX . $setting_key;
					$setting_val   = $WpSmush->validate_install() ? get_option( $setting_m_key, false ) : 0; ?>
					<div class='wp-smush-setting-row wp-smush-advanced'>
						<label class="inline-label" for="<?php echo $setting_m_key; ?>" tabindex="0">
						<span
							class="wp-smush-setting-label"><?php echo $wpsmushit_admin->settings[ $setting_key ]['label']; ?></span>
							<br/>
							<small class="smush-setting-description">
								<?php echo $wpsmushit_admin->settings[ $setting_key ]['desc']; ?>
							</small>
						</label>
						<span class="toggle float-r">
							<input type="checkbox" class="toggle-checkbox"
							       id="<?php echo $setting_m_key; ?>" <?php checked( $setting_val, 1, true ); ?>
							       value="1"
							       name="<?php echo $setting_m_key; ?>" tabindex= "0">
							<label class="toggle-label" for="<?php echo $setting_m_key; ?>"></label>
						</span><?php
						if( 'png_to_jpg' == $setting_key ) {
						$bg_color = '#<input type="text" pattern=".{6,}" id="png_to_jpg_background" class="wp-smush-png_to_jpg_background" value="' . $transparent_png['background'] . '" placeholder="ffffff" name="' . $setting_m_key . '_background" tabindex="0" width="50px" maxlength="6" title="' . esc_html__("Enter 6 character Hexcode", "wp-smushit") . '" />';
						?>
							<div class="wp-smush-png_to_jpg-wrap<?php echo $setting_val ? '' : ' hidden'?>">
								<label for="png_to_jpg_transparent">
									<input type="checkbox" id="png_to_jpg_transparent" class="wp-smush-png_to_jpg_transparent" name="<?php echo $setting_m_key; ?>_transparent" tabindex="0" <?php checked( $transparent_png['convert'] ); ?>/>
									<?php printf( esc_html__("Convert transparent images and use background color %s", "wp-smushit"), $bg_color ); ?>
								</label>
								<div class="wp-smush-settings-info wp-smush-hex-notice hidden"><?php esc_html_e("Color hexcode should be 6 characters.", "wp-smushit"); ?></div>
							</div><?php
						}
						?>
					</div>
					<hr><?php
				}
			}
			//Output Form end and Submit button for pro version
			if ( $WpSmush->validate_install() ) {
				echo $div_end;
			} else {
				echo "</div><!-- Box Content -->
				</section><!-- Main Section -->";
			}
		}

		/**
		 * Process and display the options form
		 */
		function options_ui( $configure_screen = false ) {
			global $wpsmushit_admin;
			echo '<div class="box-container">
				<form id="wp-smush-settings-form" method="post">';

			//Get max. dimesnions
			$max_sizes = $wpsmushit_admin->get_max_image_dimensions();

			//Placeholder width and Height
			$p_width = $p_height = 2048;

			//Smush auto key
			$opt_auto = WP_SMUSH_PREFIX . 'auto';
			//Auto value
			$opt_auto_val = get_option( $opt_auto, false );

			//If value is not set for auto smushing set it to 1
			if ( $opt_auto_val === false ) {
				//default to checked
				$opt_auto_val = 1;
			}

			//Keep Exif
			$opt_keep_exif = WP_SMUSH_PREFIX . 'keep_exif';
			$opt_keep_exif_val = get_option( $opt_keep_exif, false );

			 //Resize images
			$opt_resize = WP_SMUSH_PREFIX . 'resize';
			$opt_resize_val = get_option( $opt_resize, false );

			//Dimensions
			$resize_sizes = get_option( WP_SMUSH_PREFIX . 'resize_sizes', array( 'width' => '', 'height' => '' ) );

			//Additional Image sizes
			$image_sizes = get_option( WP_SMUSH_PREFIX . 'image_sizes' );
			$sizes = $wpsmushit_admin->image_dimensions();
			 ?>

			<!-- A tab index of 0 keeps the element in tab flow with other elements with an unspecified tab index which are still tabbable.) -->
			<div class='wp-smush-setting-row wp-smush-basic'>
				<label class="inline-label" for="<?php echo $opt_auto; ?>" tabindex="0">
					<span class="wp-smush-setting-label">
						<?php echo $wpsmushit_admin->settings['auto']['label']; ?>
					</span><br/>
					<small class="smush-setting-description">
						<?php echo $wpsmushit_admin->settings['auto']['desc']; ?>
					</small>
				</label>
				<span class="toggle float-r">
					<input type="checkbox" class="toggle-checkbox"
				       id="<?php echo $opt_auto; ?>"
				       name="<?php echo $opt_auto; ?>" <?php checked( $opt_auto_val, 1, true ); ?> value="1" tabindex="0">
					<label class="toggle-label" for="<?php echo $opt_auto; ?>"></label>
				</span><?php
				if( !empty( $sizes ) ) { ?>
					<!-- List of image sizes recognised by WP Smush -->
					<div class="wp-smush-image-size-list">
						<p><?php esc_html_e("The Following image sizes will be optimised by WP Smush:", "wp-smushit"); ?></p><?php
						foreach ( $sizes as $size_k => $size ) {
							if( 'medium_large' == $size_k ) {
								continue;
							}
							//If image sizes array isn't set, mark all checked ( Default Values )
							if ( false === $image_sizes ) {
								$checked = true;
							}else{
								$checked = is_array( $image_sizes ) ? in_array( $size_k, $image_sizes ) : false;
							} ?>
							<label>
								<input type="checkbox" id="wp-smush-size-<?php echo $size_k; ?>" <?php checked( $checked, true ); ?> name="wp-smush-image_sizes[]" value="<?php echo $size_k; ?>"><?php
								echo $size_k . " (" . $size['width'] . "x" . $size['height'] . ") "; ?>
							</label><?php
						} ?>
					</div><?php
				} ?>
			</div>
			<hr/>
			<div class='wp-smush-setting-row wp-smush-basic'>
				<label class="inline-label" for="<?php echo $opt_keep_exif; ?>" tabindex="0">
					<span class="wp-smush-setting-label"><?php echo $wpsmushit_admin->settings['keep_exif']['label']; ?></span>
					<br/>
					<small class="smush-setting-description">
						<?php echo $wpsmushit_admin->settings['keep_exif']['desc']; ?>
					</small>
				</label>
				<span class="toggle float-r">
					<input type="checkbox" class="toggle-checkbox"
					       id="<?php echo $opt_keep_exif; ?>" <?php checked( $opt_keep_exif_val, 1, true ); ?>
					       value="1" name="<?php echo $opt_keep_exif; ?>" tabindex="0">
					<label class="toggle-label" for="<?php echo $opt_keep_exif; ?>"></label>
				</span>
			</div>
			<hr/>
			<div class='wp-smush-setting-row wp-smush-basic'>
				<label class="inline-label" for="<?php echo $opt_resize; ?>" tabindex="0">
					<span class="wp-smush-setting-label"><?php echo $wpsmushit_admin->settings['resize']['label']; ?></span>
					<br/>
					<small class="smush-setting-description">
						<?php echo $wpsmushit_admin->settings['resize']['desc']; ?>
					</small>
				</label>
				<span class="toggle float-r">
					<input type="checkbox" class="toggle-checkbox"
					       id="<?php echo $opt_resize; ?>" <?php echo $resize_checked = checked( $opt_resize_val, 1, false ); ?>
					       value="1" name="<?php echo $opt_resize; ?>" tabindex="0">
					<label class="toggle-label" for="<?php echo $opt_resize; ?>"></label>
				</span>
				<div class="wp-smush-resize-settings-wrap<?php echo $resize_checked ? '' : ' hidden'?>">
					<label for="<?php echo $opt_resize . '_width'; ?>"><?php esc_html_e("Width", "wp-smushit"); ?>
						<input type="text" id="<?php echo $opt_resize . '_width'; ?>" class="wp-smush-resize-input" value="<?php echo isset( $resize_sizes['width'] ) && '' != $resize_sizes['width'] ? $resize_sizes['width'] : $p_width; ?>" placeholder="<?php echo $p_width; ?>" name="<?php echo $opt_resize . '_width'; ?>" tabindex="0" width=100 /> px
					</label>
					<label for"<?php echo $opt_resize . '_height'; ?>"><?php esc_html_e("Height", "wp-smushit"); ?>
						<input type="text" id="<?php echo $opt_resize . '_height'; ?>" class="wp-smush-resize-input" value="<?php echo isset( $resize_sizes['height'] ) && '' != $resize_sizes['height'] ? $resize_sizes['height'] : $p_height; ?>" placeholder="<?php echo $p_height; ?>" name="<?php echo $opt_resize . '_height'; ?>" tabindex="0" width=100 /> px
					</label>
					<div class="wp-smush-resize-note"><?php printf( esc_html__("Currently, your largest thumbnail size is set at %s%dpx wide x %dpx high%s. Anything above 2048px in width or height is huge and not recommended.", "wp-smushit"), '<strong>', $max_sizes['width'], $max_sizes['height'], '</strong>' ); ?></div>
					<div class="wp-smush-settings-info wp-smush-size-info wp-smush-update-width hidden"><?php esc_html_e( "Just to let you know, the width you've entered is less than your largest thumbnail and may result in pixelation.", "wp-smushit" ); ?></div>
					<div class="wp-smush-settings-info wp-smush-size-info wp-smush-update-height hidden"><?php esc_html_e( "Just to let you know, the height you’ve entered is less than your largest thumbnail and may result in pixelation.", "wp-smushit" ); ?></div>
				</div>
			</div><!-- End of Basic Settings --><?php

			do_action( 'wp_smush_after_basic_settings' );
			$this->advanced_settings( $configure_screen );
		}

		/**
		 * Display the Whole page ui, Call all the other functions under this
		 */
		function ui() {

			global $WpSmush, $wpsmushit_admin;

			if( !$WpSmush->validate_install() ) {
				//Reset Transient
				$wpsmushit_admin->check_bulk_limit( true );
			}

			$this->smush_page_header();

			//Show Configure screen for only a new installation and for only network admins
			if ( ( 1 != get_site_option( 'wp-smush-hide_smush_welcome' ) && 1 != get_option( 'wp-smush-hide_smush_welcome' ) ) && 1 != get_option( 'hide_smush_features' ) && 0 >= $wpsmushit_admin->smushed_count && is_super_admin() ) {
				echo '<div class="block float-l smush-welcome-wrapper">';
				$this->welcome_screen();
				echo '</div>';
			} ?>

			<!-- Bulk Smush Progress Bar -->
			<div class="wp-smushit-container-left col-half float-l"><?php
				//Bulk Smush Container
				$this->bulk_smush_container();
				?>
			</div>

			<!-- Stats -->
			<div class="wp-smushit-container-right col-half float-l"><?php
				//Stats
				$this->smush_stats_container();
				if ( ! $WpSmush->validate_install() ) {
					/**
					 * Allows to Hook in Additional Containers after Stats Box for free version
					 * Pro Version has a full width settings box, so we don't want to do it there
					 */
					do_action( 'wp_smush_after_stats_box' );
				} ?>
			</div><!-- End Of Smushit Container right -->

			<!-- Stats Share Widget -->
			<div class="row"><?php
				global $wpsmush_share;
				$wpsmush_share->share_widget(); ?>
			</div>

			<!-- Settings -->
			<div class="row"><?php
				$this->settings_ui();
				if( !$wpsmushit_admin->validate_install() ) {?>
					<div class="wp-smush-pro-for-free wp-smushit-container-left col-half float-l"><?php
						$this->wp_smush_promo();?>
					</div>
					<div class="wp-smushit-container-left col-half float-l"><?php
						$this->wp_smush_hummingbird_promo(); ?>
					</div><?php
				} ?>
			</div><?php
			$this->smush_page_footer();
		}

		/**
		 * Pro Version
		 */
		function wp_smush_promo() {
			global $wpsmushit_admin;
			$this->container_header( 'wp-smush-pro-adv', 'wp-smush-pro-promo', "FANCY A FREE SUPER SMUSH?" );
			$upgrade_url = add_query_arg(
				array(
				'utm_source' => 'Smush-Free',
				'utm_medium' => 'Banner',
				'utm_campaign' => 'settings-sidebar'
				),
				$wpsmushit_admin->upgrade_url
			);
			?>
			<div class="box-content">
				<p class="wp-smush-promo-content roboto-medium">You can now get Smush Pro... for FREE!</p>
				<p class="wp-smush-promo-content wp-smush-promo-content-2 roboto-medium">No obligation, no contracts, no
					catches. You'll get Smush Pro plus 100+ WPMU DEV plugins, Defender, Hummingbird & 24/7 WP support
					for absolutely nothing for 14 days.</p>
				<span class="wp-smush-pro-cta tc">
					<a href="<?php echo esc_url( $upgrade_url ); ?>"
					   class="button button-cta button-green" target="_blank">FIND OUT MORE</a>
				</span>
			</div><?php
			echo "</section>";
		}

		/**
		 * HummingBird Promo
		 */
		function wp_smush_hummingbird_promo() {
			//Plugin Already Installed
			if ( class_exists( 'WP_Hummingbird' ) ) {
				return;
			}
			$this->container_header( 'wp-smush-hb-adv', 'wp-smush-hb-promo', "OH YEAH, SMUSHING ON STEROIDS!" ); ?>
			<div class="box-content">
			<span class="wp-smush-hummingbird-image tc">
					<img src="<?php echo WP_SMUSH_URL . 'assets/images/hummingbird.png'; ?>"
					     alt="<?php esc_html_e( "BOOST YOUR PERFORMANCE - HUMMINGBIRD", "wp-smushit" ); ?>">
	        </span>
			<p class="wp-smush-promo-content tc roboto-medium">Hummingbird goes beyond Smush compression with
				minification, caching, performance monitoring and more - every millisecond counts!</p>
			<span class="wp-smush-hb-cta tc roboto-medium">
				<a href="<?php echo esc_url( "https://premium.wpmudev.org/project/wp-hummingbird/" ); ?>"
				   class="button button-cta button-yellow" target="_blank">TRY HUMMINGBIRD FOR FREE</a>
			</span>
			</div><?php
			echo "</section>";
		}

		/**
		 * Outputs the Content for Bulk Smush Div
		 */
		function bulk_smush_content() {

			global $WpSmush, $wpsmushit_admin;

			$all_done = ( $wpsmushit_admin->smushed_count == $wpsmushit_admin->total_count ) && 0 == count( $wpsmushit_admin->resmush_ids );

			echo $this->bulk_resmush_content();

			//If there are no images in Media Library
			if ( 0 >= $wpsmushit_admin->total_count ) { ?>
				<span class="wp-smush-no-image tc">
					<img src="<?php echo WP_SMUSH_URL . 'assets/images/smush-no-media.png'; ?>"
					     alt="<?php esc_html_e( "No attachments found - Upload some images", "wp-smushit" ); ?>">
		        </span>
				<p class="wp-smush-no-images-content tc roboto-regular"><?php printf( esc_html__( "We haven’t found any images in your %smedia library%s yet so there’s no smushing to be done! Once you upload images, reload this page and start playing!", "wp-smushit" ), '<a href="' . esc_url( admin_url( 'upload.php' ) ) . '">', '</a>' ); ?></p>
				<span class="wp-smush-upload-images tc">
				<a class="button button-cta"
				   href="<?php echo esc_url( admin_url( 'media-new.php' ) ); ?>"><?php esc_html_e( "UPLOAD IMAGES", "wp-smushit" ); ?></a>
				</span><?php
			} else { ?>
				<!-- Hide All done div if there are images pending -->
				<div class="wp-smush-notice wp-smush-all-done<?php echo $all_done ? '' : ' hidden' ?>" tabindex="0">
					<i class="dev-icon dev-icon-tick"></i><?php esc_html_e( "All images are smushed and up to date. Awesome!", "wp-smushit" ); ?>
				</div>
				<div class="wp-smush-bulk-wrapper <?php echo $all_done ? ' hidden' : ''; ?>"><?php
				//If all the images in media library are smushed
				//Button Text
				$button_content = esc_html__( "BULK SMUSH NOW", "wp-smushit" );

				//Show the notice only if there are remaining images and if we aren't showing a notice for resmush
				if ( $wpsmushit_admin->remaining_count > 0 ) {
					$class = count( $wpsmushit_admin->resmush_ids ) > 0 ? ' hidden' : '';
					$upgrade_url = add_query_arg(
						array(
						'utm_source' => 'Smush-Free',
						'utm_medium' => 'Banner',
						'utm_campaign' => 'yellow-bulk-smush-upsell'
						),
						$wpsmushit_admin->upgrade_url
					);
					?>
					<div class="wp-smush-notice wp-smush-remaining<?php echo $class; ?>" tabindex="0">
						<i class="dev-icon">
							<img src="<?php echo WP_SMUSH_URL . 'assets/images/icon-gzip.svg'; ?>" width="14px">
						</i>
						<span class="wp-smush-notice-text"><?php
							printf( _n( "%s, you have %s%s%d%s image%s that needs smushing!", "%s, you have %s%s%d%s images%s that need smushing!", $wpsmushit_admin->remaining_count, "wp-smushit" ), $wpsmushit_admin->get_user_name(), '<strong>', '<span class="wp-smush-remaining-count">', $wpsmushit_admin->remaining_count, '</span>', '</strong>' );
							if( !$WpSmush->validate_install() ) {
								printf( '<br />' . esc_html__("You can %sUpgrade to Pro%s to bulk smush all your images with one click.", "wp-smushit") .'<br />', '<a href="' . esc_url( $upgrade_url ). '" target="_blank" title="' . esc_html__("WP Smush Pro", "wp-smushit") . '">', '</a>' );
								esc_html_e("Free users can smush 50 images with each click.", "wp-smushit");
							 }?>
						</span>
					</div><?php
				} ?>
				<hr>
				<button type="button" class="wp-smush-all wp-smush-button"><?php echo $button_content; ?></button><?php
				//Enable Super Smush
				if ( $WpSmush->validate_install() && ! $WpSmush->lossy_enabled ) { ?>
					<p class="wp-smush-enable-lossy"><?php esc_html_e( "Enable Super-smush in the Settings area to get even more savings with almost no noticeable quality loss.", "wp-smushit" ); ?></p><?php
				} ?>
				</div><?php
				$this->progress_bar( $wpsmushit_admin );
				$this->super_smush_promo();
			}
		}

		/**
		 * Content for showing Progress Bar
		 */
		function progress_bar( $count ) {

			//If we have resmush list, smushed_count = totalcount - resmush count, else smushed_count
			$smushed_count = ( $resmush_count = count( $count->resmush_ids ) ) > 0 ? ( $count->total_count - ( $count->remaining_count + $resmush_count ) ) : $count->smushed_count;
			// calculate %ages, avoid divide by zero error with no attachments
			if ( $count->total_count > 0 && $count->smushed_count > 0 ) {
				$smushed_pc = $smushed_count / $count->total_count * 100;
			} else {
				$smushed_pc = 0;
			} ?>
			<div class="wp-smush-bulk-progress-bar-wrapper hidden">
			<p class="wp-smush-bulk-active roboto-medium"><span
					class="spinner is-active"></span><?php printf( esc_html__( "%sBulk smush is currently running.%s You need to keep this page open for the process to complete.", "wp-smushit" ), '<strong>', '</strong>' ); ?>
			</p>
			<div class="wp-smush-progress-wrap">
				<div class="wp-smush-progress-bar-wrap">
					<div class="wp-smush-progress-bar">
						<div class="wp-smush-progress-inner" style="width: <?php echo $smushed_pc; ?>%;">
						</div>
					</div>
				</div>
				<div class="wp-smush-count tc">
					<?php printf( esc_html__( "%s%d%s of %d attachments have been smushed." ), '<span class="wp-smush-images-smushed">', $smushed_count, '</span>', $count->total_count ); ?>
				</div>
			</div>
			<hr class="wp-smush-sep">
			<button type="button"
			        class="button button-grey wp-smush-cancel-bulk"><?php esc_html_e( "CANCEL", "wp-smushit" ); ?></button>
			</div>
			<div class="smush-final-log notice notice-warning inline hidden"></div><?php
		}

		/**
		 * Shows a option to ignore the Image ids which can be resmushed while bulk smushing
		 *
		 * @param int $count Resmush + Unsmushed Image count
		 */
		function bulk_resmush_content( $count = false, $show = false ) {

			global $wpsmushit_admin;

			//If we already have count, don't fetch it
			if ( false === $count ) {
				//If we have the resmush ids list, Show Resmush notice and button
				if ( $resmush_ids = get_option( "wp-smush-resmush-list" ) ) {

					$count = count( $resmush_ids );

					//Whether to show the remaining re-smush notice
					$show = $count > 0 ? true : false;

					//Get the Actual remainaing count
					if ( ! isset( $wpsmushit_admin->remaining_count ) ) {
						$wpsmushit_admin->setup_global_stats();
					}

					$count += $wpsmushit_admin->remaining_count;
				}
			}
			//Show only if we have any images to ber resmushed
			if ( $show ) {
				return '<div class="wp-smush-notice wp-smush-resmush-notice wp-smush-remaining" tabindex="0">
						<i class="dev-icon"><img src="' . WP_SMUSH_URL . 'assets/images/icon-gzip.svg" width="14px"></i>
						<span class="wp-smush-notice-text">' . sprintf( _n( "%s, you have %s%s%d%s image%s that needs re-compressing!", "%s, you have %s%s%d%s images%s that need re-compressing!", $count, "wp-smushit" ), $wpsmushit_admin->get_user_name(), '<strong>', '<span class="wp-smush-remaining-count">', $count, '</span>', '</strong>' ) . '</span>
						<button class="button button-grey button-small wp-smush-skip-resmush">' . esc_html__( "Skip", "wp-smushit" ) . '</button>
	                </div>';
			}
		}

		/**
		 * Displays a admin notice for settings update
		 */
		function settings_updated() {
			global $wpsmushit_admin;
			//Show Setttings Saved message
			if ( 1 == get_option( 'wp-smush-settings_updated', false ) ) {

				//Default message
				$message = esc_html__( "Your settings have been updated!", "wp-smushit" );

				//Additonal message if we got work to do!
				$resmush_count = is_array( $wpsmushit_admin->resmush_ids ) && count( $wpsmushit_admin->resmush_ids ) > 0;
				$smush_count   = is_array( $wpsmushit_admin->remaining_count ) && $wpsmushit_admin->remaining_count > 0;

				if ( $smush_count || $resmush_count ) {
					$message .= ' ' . sprintf( esc_html__( "You have images that need smushing. %sBulk smush now!%s", "wp-smushit" ), '<a href="#" class="wp-smush-trigger-bulk">', '</a>' );
				}
				echo '<div class="wp-smush-notice wp-smush-settings-updated"><i class="dev-icon dev-icon-tick"></i> ' . $message . '
				<i class="dev-icon dev-icon-cross"></i>
				</div>';

				//Remove the option
				delete_option( 'wp-smush-settings_updated' );
			}
		}

		/**
		 * Prints out the page header for Bulk Smush Page
		 */
		function smush_page_header() {
			global $WpSmush, $wpsmushit_admin;
			//Include Shared UI
			require_once WP_SMUSH_DIR . 'assets/shared-ui/plugin-ui.php';

			if( $wpsmushit_admin->remaining_count == 0 || $wpsmushit_admin->smushed_count == 0 ) {
				//Initialize global Stats
				$wpsmushit_admin->setup_global_stats();
			}

			//Page Heading for Free and Pro Version
			$page_heading = $WpSmush->validate_install() ? esc_html__( 'WP Smush Pro', 'wp-smushit' ) : esc_html__( 'WP Smush', 'wp-smushit' );

			$auto_smush_message = $WpSmush->is_auto_smush_enabled() ? sprintf( esc_html__( "Automatic smushing is %senabled%s. Newly uploaded images will be automagically compressed." ), '<span class="wp-smush-auto-enabled">', '</span>' ) : sprintf( esc_html__( "Automatic smushing is %sdisabled%s. Newly uploaded images will need to be manually smushed." ), '<span class="wp-smush-auto-disabled">', '</span>' );
			echo '<div class="smush-page-wrap">
				<section id="header">
					<div class="wp-smush-page-header">
						<h1 class="wp-smush-page-heading">' . $page_heading . '</h1>
						<div class="wp-smush-auto-message roboto-regular">' . $auto_smush_message . '</div>
					</div>
				</section>';
			//Check if settings were updated and shoe a notice
			$this->settings_updated();

			echo '<div class="row wp-smushit-container-wrap">';
		}

		/**
		 * Content of the Install/ Upgrade notice based on Free or Pro version
		 */
		function installation_notice() {
			global $wpsmushit_admin;
			$css_url = WP_SMUSH_URL . 'assets/css/notice.css?1';
			$js_url = WP_SMUSH_URL . 'assets/js/notice.js';

			//Whether New/Existing Installation
			$install_type = get_site_option('wp-smush-install-type', false );

			if( !$install_type ) {
				$install_type = $wpsmushit_admin->smushed_count > 0 ? 'existing' : 'new';
				update_site_option( 'wp-smush-install-type', $install_type );
			}

			if ( 'new' == $install_type  ) {
				$notice_heading = esc_html__( "Thanks for installing Smush. We hope you like it!", "wp-smushit" );
				$notice_content = esc_html__( "And hey, if you do, you can now try out Smush Pro for double the smushy goodness (benchmarked), entirely for free!", "wp-smushit" );
				$button_content = esc_html__( "Try Smush Pro for Free", "wp-smushit" );
			} else {
				$notice_heading = esc_html__( "Thanks for updating Smush. Did you know that you can now try the Smush Pro for FREE?!", "wp-smushit" );
				$notice_content = '<br />' . esc_html__( "Yep, Super Smush your images for double the savings, save originals and batch Smush thousands of images all at once.... no charge!", "wp-smushit" );
				$button_content = esc_html__( "Try Smush Pro for Free", "wp-smushit" );
			}
			$upgrade_url = add_query_arg(
				array(
				'utm_source' => 'Smush-Free',
				'utm_medium' => 'Banner',
				'utm_campaign' => 'try-pro-free'
				),
				$wpsmushit_admin->upgrade_url
			);?>
			<link rel="stylesheet" type="text/css" href="<?php echo esc_url( $css_url ); ?>" />
			<div class="notice smush-notice" style="display: none;">
				<div class="smush-notice-logo"><span></span></div>
				<div
					class="smush-notice-message<?php echo 'new' == $install_type ? ' wp-smush-fresh' : ' wp-smush-existing'; ?>">
					<strong><?php echo $notice_heading; ?></strong>
					<?php echo $notice_content; ?>
				</div>
				<div class="smush-notice-cta">
					<a href="<?php echo esc_url( $upgrade_url ); ?>" class="smush-notice-act button-primary" target="_blank">
					<?php echo $button_content; ?>
					</a>
					<button class="smush-notice-dismiss smush-dismiss-welcome" data-msg="<?php esc_html_e( 'Saving', 'wp-smushit'); ?>"><?php esc_html_e( 'Dismiss', "wp-smushit" ); ?></button>
				</div>
			</div>
			<script src="<?php echo esc_url( $js_url )  . '?v=' . WP_SMUSH_VERSION; ?>"></script><?php
		}

		/**
		 * Super Smush promo content
		 */
		function super_smush_promo() {
			global $WpSmush, $wpsmushit_admin;
			if ( $WpSmush->validate_install() ) {
				return;
			}
			$upgrade_url = add_query_arg(
				array(
				'utm_source' => 'Smush-Free',
				'utm_medium' => 'Banner',
				'utm_campaign' => 'smush-lady-upgrade'
				),
				$wpsmushit_admin->upgrade_url
			); ?>
			<div class="wp-smush-super-smush-promo">
				<div class="wp-smush-super-smush-content"><?php
					printf( esc_html__("Did you know WP Smush Pro delivers up to 2x better compression, allows you to smush your originals and removes any bulk smushing limits? – %sTry it absolutely FREE%s", "wp-smushit"), '<a href="' . esc_url( $upgrade_url ). '" target="_blank" title="' . esc_html__("Try WP Smush Pro for FREE", "wp-smushit") . '">', '</a>' ); ?>
				</div>
			</div>
			<?php
		}

		/**
		 * Prints Out the page Footer
		 */
		function smush_page_footer() {
			echo '</div><!-- End of Container wrap -->
			</div> <!-- End of div wrap -->';
		}
	}
}
