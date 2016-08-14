<?php

if ( ! class_exists( 'WpSmushBackup' ) ) {

	class WpSmushBackup {

		/**
		 * Whether to backp images or not
		 * @var bool
		 */
		var $backup_enabled = false;

		/**
		 * Constructor
		 */
		function __construct() {
			//Initialize Variables and perform other operations
			add_action( 'admin_init', array( $this, 'admin_init' ) );

			//Handle Restore operation
			add_action( 'wp_ajax_smush_restore_image', array( $this, 'restore_image' ) );
		}

		function admin_init() {

			$this->initialize();

		}

		function initialize() {
			//Whether backup is enabled or not
			$this->backup_enabled = get_option( WP_SMUSH_PREFIX . 'backup' );
		}

		function create_backup( $file_path = '', $backup_path = '' ) {
			global $WpSmush, $wpsmush_pngjpg;

			if ( empty( $file_path ) ) {
				return '';
			}

			//Return file path if backup is disabled
			if ( ! $this->backup_enabled || ! $WpSmush->validate_install() ) {
				return $file_path;
			}

			//Get a backup path if empty
			if ( empty( $backup_path ) ) {
				$backup_path = $WpSmush->get_image_backup_path( $file_path );
			}

			//If we don't have any backup path yet, bail!
			if ( empty( $backup_path ) ) {
				return $file_path;
			}

			if ( ! empty( $WpSmush->attachment_id ) && $wpsmush_pngjpg->is_converted( $WpSmush->attachment_id ) ) {
				//No need to create a backup, we already have one if enabled
				return $file_path;
			}

			//Check for backup from other plugins, like nextgen, if it doesn't exists, create our own
			if ( ! file_exists( $file_path . '_backup' ) || ! file_exists( $backup_path ) ) {
				@copy( $file_path, $backup_path );
			}

		}

		/**
		 * Restore the image and its sizes from backup
		 *
		 */
		function restore_image( $attachment = '', $resp = true ) {
			global $WpSmush;
			//If no attachment id is provided, check $_POST variable for attachment_id
			if ( empty( $attachment ) ) {
				//Check Empty fields
				if ( empty( $_POST['attachment_id'] ) || empty( $_POST['_nonce'] ) ) {
					wp_send_json_error( array(
						'error'   => 'empty_fields',
						'message' => esc_html__( "Error in processing restore action, Fields empty.", "wp-smushit" )
					) );
				}
				//Check Nonce
				if ( ! wp_verify_nonce( $_POST['_nonce'], "wp-smush-restore-" . $_POST['attachment_id'] ) ) {
					wp_send_json_error( array(
						'error'   => 'empty_fields',
						'message' => esc_html__( "Image not restored, Nonce verification failed.", "wp-smushit" )
					) );
				}
			}

			//Store the restore success/failure for all the sizes
			$restored = array();

			//Process Now
			$image_id = empty( $attachment ) ? absint( (int) $_POST['attachment_id'] ) : $attachment;

			//Restore Full size -> get other image sizes -> restore other images

			//Get the Original Path
			$file_path = get_attached_file( $image_id );

			//Get the backup path
			$backup_name = $WpSmush->get_image_backup_path( $file_path );

			//Check if it's a jpg converted from png, and restore the jpg to png
			$original_file      = get_post_meta( $image_id, WP_SMUSH_PREFIX . 'original_file', true );
			$original_file_path = $WpSmush->original_file( $original_file );

			//Flag used to restore other sizes
			$restore_png = false;

			if ( !empty( $original_file) && file_exists( $original_file_path ) ) {
				//restore PNG full size and all other image sizes
				$restored[]  = $this->restore_png( $image_id, 'full', $original_file, $file_path );
				$restore_png = true;
			} elseif ( file_exists( $backup_name ) ) {
				//If file exists, corresponding to our backup path
				//Restore
				$restored[] = @copy( $backup_name, $file_path );

				//Delete the backup
				@unlink( $backup_name );
			} elseif ( file_exists( $file_path . '_backup' ) ) {
				//Restore from other backups
				$restored[] = @copy( $file_path . '_backup', $file_path );
			}

			//Get other sizes and restore
			//Get attachment data
			$attachment_data = wp_get_attachment_metadata( $image_id );

			//Get the sizes
			$sizes = ! empty( $attachment_data['sizes'] ) ? $attachment_data['sizes'] : '';

			//Loop on images to restore them
			foreach ( $sizes as $size_k => $size ) {
				//Get the file path
				if ( empty( $size['file'] ) ) {
					continue;
				}

				//Image Path and Backup path
				$image_size_path  = path_join( dirname( $file_path ), $size['file'] );
				$image_bckup_path = $WpSmush->get_image_backup_path( $image_size_path );

				//Restore
				if ( $restore_png ) {
					$restored[] = $this->restore_png( $image_id, $size_k, $original_file, $image_size_path );
				} elseif ( file_exists( $image_bckup_path ) ) {
					$restored[] = @copy( $image_bckup_path, $image_size_path );
					//Delete the backup
					@unlink( $image_bckup_path );
				} elseif ( file_exists( $image_size_path . '_backup' ) ) {
					$restored[] = @copy( $image_size_path . '_backup', $image_size_path );
				}
			}

			//If any of the image is restored, we count it as success
			if ( in_array( true, $restored ) ) {

				//Remove the Meta, And send json success
				delete_post_meta( $image_id, $WpSmush->smushed_meta_key );

				//Remove PNG to JPG conversion savings
				delete_post_meta( $image_id, WP_SMUSH_PREFIX . 'pngjpg_savings' );

				//Remove Original File
				delete_post_meta( $image_id, WP_SMUSH_PREFIX . 'original_file' );

				//Get the Button html without wrapper
				$button_html = $WpSmush->set_status( $image_id, false, false, false );

				if ( $resp ) {
					wp_send_json_success( array( 'button' => $button_html ) );
				} else {
					return true;
				}
			}
			if ( $resp ) {
				wp_send_json_error( array( 'message' => '<div class="wp-smush-error">' . __( "Unable to restore image", "wp-smushit" ) . '</div>' ) );
			}

			return false;
		}


		function restore_png( $image_id = '', $size = 'full', $original_file = '', $file_path = '' ) {

			global $WpSmush, $wpsmush_pngjpg;

			//If we don't have attachment id, there is nothing we can do
			if ( empty ( $image_id ) ) {
				return false;
			}

			$meta = '';

			//Else get the Attachment details
			/**
			 * For Full Size
			 * 1. Get the original file path
			 * 2. Update the attachment metadata and all other meta details
			 * 3. Delete the JPEG
			 * 4. And we're done
			 * 5. Add a action after updating the URLs, that'd allow the users to perform a additional search, replace action
			 **/
			if ( empty( $original_file ) ) {
				$original_file = get_post_meta( $image_id, WP_SMUSH_PREFIX . 'original_file', true );
			}
			$original_file_path = $WpSmush->original_file( $original_file );
			if ( 'full' == $size ) {
				if ( file_exists( $original_file_path ) ) {
					//Update the path details in meta and attached file, replace the image
					$meta = $wpsmush_pngjpg->update_image_path( $image_id, $file_path, $original_file_path, $meta, $size, 'restore' );
					//@todo: Add a check in meta if file was updated or not, before unlinking jpg
					if ( ! empty( $meta['file'] ) && $original_file == $meta['file'] ) {
						@unlink( $file_path );
					}
					/**
					 *  Perform a action after the image URL is updated in post content
					 */
					do_action( 'wp_smush_image_url_updated', $image_id, $file_path, $original_file, $size );
				}
			} else {
				/**
				 * For any other size
				 *  1. Figure out the image path for the respective size
				 *  2. Update the image path in attachment metadata
				 *  3. Delete the JPEG
				 *  4. And done!
				 *  5. Add a action after updating the URLs, that'd allow the users to perform a additional search, replace action
				 **/
				//Get the file path for the specific size
				$n_file      = dirname( $original_file_path ) . '/' . basename( $file_path );
				$n_file_path = pathinfo( $n_file, PATHINFO_FILENAME ) . '.png';
				if ( file_exists( $n_file ) ) {
					//Update the path details in meta and attached file, replace the image
					$meta = $wpsmush_pngjpg->update_image_path( $image_id, $file_path, $n_file_path, $meta, $size, 'restore' );
					if ( ! empty( $meta['sizes'] ) && ! empty( $meta['sizes'][ $size ] ) && ! empty( $meta['sizes'][ $size ]['file'] ) && $n_file_path != $meta['sizes'][ $size ]['file'] ) {
						@unlink( $file_path );
					}
					/**
					 *  Perform a action after the image URL is updated in post content
					 */
					do_action( 'wp_smush_image_url_updated', $image_id, $file_path, $n_file, $size );
				}
			}
			//Update Meta
			if ( ! empty( $meta ) ) {
				//Remove Smushing, while attachment data is updated for the image
				remove_filter( 'wp_update_attachment_metadata', array( $WpSmush, 'smush_image' ), 15 );
				wp_update_attachment_metadata( $image_id, $meta );
				return true;
			}
			return false;

		}
	}

	global $wpsmush_backup;
	$wpsmush_backup = new WpSmushBackup();

}
