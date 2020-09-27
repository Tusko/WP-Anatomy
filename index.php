<?php get_header();

$s = 0;
if(get_query_var('paged')) {
	$paged = get_query_var('paged');
} elseif(get_query_var('page')) {
	$paged = get_query_var('page');
} else {
	$paged = 1;
}

if(is_home()) {
	$queryname = get_the_title(BLOG_ID);
} else {
	$queryname = 'Archive of ' . get_the_archive_title();
} ?>

	<div class="blog-meta row">
		<?php if(function_exists('yoast_breadcrumb')) {
			yoast_breadcrumb('<div id="breadcrumbs">', '</div>');
		} ?>
		<?php get_search_form(); ?>
	</div>

	<section id="bloggrid" class="content row">
		<article role="main"<?php echo ! is_active_sidebar('blog_sidebar') ? ' class="no-sidebar"' : ''; ?>>
			<?php if($queryname) : echo '<h1>' . $queryname . '</h1>'; endif; ?>
			<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
				<?php if($paged == 1 && $s++ == 0) { ?>
					<div class="sticky_post blogpost">
						<div class="post-data"><?php echo get_the_date('d.m.Y'); ?> | by <?php the_author(); ?></div>
						<?php if(has_post_thumbnail()) { ?>
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
						<?php } ?>
						<h2><a href="<?php the_permalink(); ?>" class="blogtitle"><?php the_title(); ?></a></h2>
						<p><?php echo wp_trim_words(get_the_content($post->ID), 50, '<br /><a href="' . get_permalink($post->ID) . '">Read More</a>'); ?></p>
						<div class="post-meta">
							<div class="author"><i class="icon-editor"></i><?php the_author(); ?></div>
							<div class="comments"><i class="icon-comments"></i><?php comments_number('No comments', 'One comment', '% comments'); ?></div>
							<div class="cats"><i class="icon-category"></i><?php echo cats($post->ID); ?></div>
							<?php echo get_the_tag_list('<div class="tags"><i class="icon-tag"></i>', ', ', '</div>'); ?>
						</div>
					</div>
				<?php } else { ?>
					<div class="blogpost">
						<?php if(has_post_thumbnail()) { ?>
							<a href="<?php the_permalink(); ?>" class="blogimg"><?php the_post_thumbnail('blog_image'); ?></a>
						<?php } ?>
						<h2><a href="<?php the_permalink(); ?>" class="blogtitle"><?php the_title(); ?></a></h2>
						<p><?php echo wp_trim_words(get_the_content($post->ID), 50, '<br /><a href="' . get_permalink($post->ID) . '">Read More</a>'); ?></p>
						<div class="post-meta">
							<div class="author"><i class="icon-editor"></i>
								<div><?php the_author(); ?></div>
							</div>
							<div class="comments"><i class="icon-comments"></i>
								<div><?php comments_number('No comments', 'One comment', '% comments'); ?></div>
							</div>
							<div class="cats"><i class="icon-category"></i>
								<div><?php echo cats($post->ID); ?></div>
							</div>
							<?php echo get_the_tag_list('<div class="tags"><i class="icon-tag"></i><div>', ', ', '</div></div>'); ?>
						</div>
					</div>
				<?php } ?>
			<?php endwhile;
				if(function_exists('wp_pagenavi')) {
					wp_pagenavi();
				}
			endif; ?>
		</article>
		<?php if(is_active_sidebar('blog_sidebar')) { ?>
			<aside><?php dynamic_sidebar('blog_sidebar'); ?></aside>
		<?php } ?>
	</section>
<?php get_footer(); ?>