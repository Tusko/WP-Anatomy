<?php get_header();
global $post; ?>
    <section class="content row cfx">
        <article>
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
                the_content();
            endwhile; endif; ?>
        </article>
    </section>
<?php get_footer(); ?>
