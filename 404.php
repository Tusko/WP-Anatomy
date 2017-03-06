<?php get_header(); ?>
<section class="content row cfx">
    <article>
        <h1>Error 404</h1>
        <h2>Something happened.</h2>
        <p>404 is a mistake and it means that the page can't be found or has been moved.</p>
        <p><a href="mailto:<?php echo get_option('admin_email'); ?>">Write me</a> about this error, or <a href="<?php echo site_url(); ?>">return to the homepage</a> and start over.</p>
    </article>
</section>
<?php get_footer(); ?>
