<?php
function wpa_convert_webp_src($src) {
	if(strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') === false) {
		return $src;
	}
	$upload_dir  = wp_upload_dir();
	$src_url     = parse_url($upload_dir['baseurl']);
	$upload_path = $src_url['path'];
	if(strpos($src, $upload_path) !== false) {
		$src_webp      = str_replace('.jpg', '.webp', $src);
		$src_webp      = str_replace('.jpeg', '.webp', $src_webp);
		$src_webp      = str_replace('.png', '.webp', $src_webp);
		$parts         = explode($upload_path, $src_webp);
		$relative_path = $parts[1];
		// check if relative path is not empty and file exists
		if( ! empty($relative_path) && file_exists($upload_dir['basedir'] . $relative_path)) {
			return $src_webp;
		} else {
			// try appended webp extension
			$src_webp_appended      = $src . '.webp';
			$parts_appended         = explode($upload_path, $src_webp_appended);
			$relative_path_appended = $parts_appended[1];
			// check if relative path is not empty and file exists
			if( ! empty($relative_path_appended) && file_exists($upload_dir['basedir'] . $relative_path_appended)) {
				return $src_webp_appended;
			}
		}
	}

	return $src;
}

//simple function for wp_get_attachment_image_src()
function image_src($id, $size = 'full', $background_image = false, $height = false) {
	$attachmentID = get_post_type($id) === 'attachment' ? $id : get_post_thumbnail_id($id);
	$image        = wp_get_attachment_image_src($attachmentID, $size, true);
	if($image) {
		$src = "/?imgsrc=$image[0]";

		return $background_image ? 'background-image: url(' . $src . ');' . ($height ? 'height:' . $image[2] . 'px' : '') : $src;
	}
}

function add_query_vars_filter($vars) {
	$vars[] = "imgsrc";

	return $vars;
}

add_filter('query_vars', 'add_query_vars_filter');

add_action('template_redirect', function() {
	$imghandle = get_query_var('imgsrc');
	if( ! empty($imghandle)) {
		$image = wpa_convert_webp_src($imghandle);
		header("Location:$image");
//		$headers = @get_headers( $image );
//
//		if ( $headers && strpos( $headers[0], '200' ) ) {
//			foreach ( $headers as $header ) {
//				header( $header );
//			}
//			echo file_get_contents( $image );
//		} else {
//			http_response_code( 404 );
//		}
		die();
	}
});