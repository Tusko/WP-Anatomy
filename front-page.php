<?php get_header(); global $post;?>
<section class="content row">
    <article>
        <img src="<?php echo wpa_placeholder(get_post_thumbnail_id($post->ID), 'full'); ?>" data-defer="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full') ?>" alt="<?php echo get_alt(get_post_thumbnail_id($post->ID)); ?>" />
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
            the_content();
        endwhile; endif;
        ?>
    </article>
    <aside>
    </aside>
</section>

<?php get_footer(); ?>