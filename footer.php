</div>
<footer>
    <div class="copyright row">
        <p>Copyright &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
        <p class="dev">Design &amp; Development <a href="https://arsmoon.com" target="_blank">Independent developers team</a></p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
<?php if( defined('WP_DEBUG') && true !== WP_DEBUG) {
    ob_end_flush();
} ?>
