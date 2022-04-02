<?php get_header();
global $post; ?>
	<section class="content row">
		<article class="animatedParent animatedOnce">
			<?php the_post_thumbnail('full', array(
				'data-aos'      => 'fade-up',
				'data-fancybox' => ''
			)); ?>

			<?php if(have_posts()) : while(have_posts()) : the_post();
				the_content();
			endwhile; endif; ?>
		</article>
		<aside>
		</aside>
	</section>

<?php get_footer(); ?>