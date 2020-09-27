<?php get_header();
global $post; ?>
	<section class="content row">

		<h1>test 1 2 3 4 5 5 5 5333</h1>
		<h2>test cache 224</h2>
		<article class="animatedParent animatedOnce">
			<?php the_post_thumbnail('full', array(
				'class' => 'animated fadeInUp ac'
			)); ?>

			<?php if(have_posts()) : while(have_posts()) : the_post();
				the_content();
			endwhile; endif; ?>
			<p><a href="#">Hello world</a></p>
			<p><a href="#" class="button">Hello world</a></p>
		</article>
		<aside>
		</aside>
	</section>

<?php get_footer(); ?>