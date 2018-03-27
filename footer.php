</div>
<footer>
    <div class="copyright row">
        <p>Copyright &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
        <p class="dev">
            Handcrafted by <a href="https://arsmoon.com" target="_blank" data-defer="https://arsmoon.com/brand-wp-admin/logo_white.svg"><span class="hidden">Arsmoon Digital Agency</span></a>
        </p>
    </div>
</footer>

<script defer src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>
<?php wp_footer(); ?>
</body>
</html>
<?php if( defined('WP_DEBUG') && true !== WP_DEBUG) {
    ob_end_flush();
} ?>
