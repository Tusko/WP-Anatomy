<?php if(defined('WP_DEBUG') && true !== WP_DEBUG && ! isset($_REQUEST['debug'])) {
	ob_start('ob_html_compress');
} ?>
<!DOCTYPE html>
<html lang="<?php wpa_html_lang() ?>">
<head>
	<meta charset="UTF-8">
	<title><?php wpa_title(); ?></title>
	<meta name="MobileOptimized" content="width"/>
	<meta name="HandheldFriendly" content="True"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, minimal-ui"/>
	<?php wp_head(); ?>
	<style>
		body {
			opacity: 0
		}
		.h100 {
			min-height: 100vh;
		}
		.cover {
			background-position: 50% 50%;
			background-repeat: no-repeat;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			background-size: cover;
		}
	</style>
</head>
<body <?php body_class(); ?> data-hash="<?php wpa_fontbase64(true); ?>" data-a="<?php echo admin_url('admin-ajax.php'); ?>">
<div id="wrap">
	<header class="lazyload" data-lazyload-children=".lazyload">
		<div class="row">
			<a href="<?php echo site_url(); ?>" class="logo">
				<img class="lazyload" data-src="<?php echo theme('images/logo.svg'); ?>" alt="<?php bloginfo('name'); ?>">
			</a>
			<a class="nav-icon" href=""><i></i><i></i><i></i></a>
			<nav>
				<?php if(has_nav_menu('primary_menu')) {
					wp_nav_menu(array('container' => false, 'items_wrap' => '<ul id="%1$s">%3$s</ul>', 'theme_location' => 'primary_menu'));
				} ?>
			</nav>
			<?php get_search_form(); ?>
		</div>
	</header>
