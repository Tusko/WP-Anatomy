<?php get_header();

$queryname = __('Sorry, posts not found');

$allsearch = new WP_Query("s=$s&showposts=-1");
$queryname = 'We found ' . $allsearch->post_count . ' results for the search "' . esc_html($s, 1) .'"';
wp_reset_query();

?>

<section class="content row cfx">
    <?php if($queryname) : echo '<h1>'. $queryname. '</h1>'; endif; ?>
    <article role="main">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <?php if(is_sticky($post->ID)) { ?>
        <div class="sticky_post blogpost cfx">
            <?php if ( has_post_thumbnail() ) { ?>
            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
            <?php } ?>
            <h2><a href="<?php the_permalink(); ?>" class="blogtitle"><?php the_title() ;?></a></h2>
            <time><?php echo get_the_date( 'F j, Y'); ?></time>
            <p><?php echo wp_trim_words(get_the_content($post->ID), 50, 'Read More'); ?></p>
        </div>
        <?php } else { ?>
        <div class="blogpost cfx">
            <?php if ( has_post_thumbnail() ) { ?>
            <a href="<?php the_permalink(); ?>" class="alignleft blogimg"><?php the_post_thumbnail('blog_image'); ?></a>
            <?php } ?>
            <div class="excerpt">
                <h2><a href="<?php the_permalink(); ?>" class="blogtitle"><?php the_title(); ?></a></h2>
                <time><?php echo get_the_date( 'F j, Y'); ?></time>
                <p><?php echo gebid(get_the_content($post->ID), 30, 'Read More'); ?></p>
            </div>
        </div>
        <?php } ?>
        <?php endwhile;
            if(function_exists('wp_pagenavi')){
                wp_pagenavi();
            }
        endif; ?>
    </article>
    <aside>
        <?php dynamic_sidebar('Blog Sidebar'); ?>
    </aside>
</section>
<?php get_footer(); ?>
