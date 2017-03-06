<?php get_header(); ?>
<section class="content row">
    <article>
       <img src="<?php echo placeImg(818, 419); ?>" data-defer="//wpa.dev0.site/wp-content/uploads/HOTBalloonTrip_UltraHD.jpg" alt="">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
            the_content();
        endwhile; endif; ?>
    </article>
</section>

<div id="testarea" class="content row">
    <select name="" id="">
        <?php for($i=0; $i<10;$i++) {
            echo '<option>Option '.$i.'</option>';
        } ?>
    </select>
    <div class="flexGrid">
        <div>text-1</div>
        <div>text-2</div>
        <div>text-3</div>
        <div>text-4</div>
        <div>text-5</div>
        <div>text-6</div>
        <div>text-7</div>
        <div>text-8</div>
        <div>text-9</div>
        <div>text-10</div>
        <div>text-11</div>
        <div>text-12</div>
        <div>text-13</div>
        <div>text-14</div>
        <div>text-15</div>
        <div>text-16</div>
        <div>text-17</div>
    </div>

    <a class="button" data-fancybox data-src="#hidden-content" href="javascript:;">Hello fancybox</a>
    <div class="fancybox-modal-content" id="hidden-content">
        <h2>Hello</h2>
        <p>You are awesome.</p>
        <p><a class="button" href="javascript:parent.jQuery.fancybox.close();">Dismiss</a></p>
    </div>
</div>

<?php get_footer(); ?>
