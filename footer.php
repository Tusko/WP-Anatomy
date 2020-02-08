</div>
<footer>
	<div class="copyright row">
		<p>&copy; <?php bloginfo('name'); ?> <?php echo date('Y'); ?>. <?php _e('[:uk]Всі права захищено[:en]All rights reserved[:]'); ?></p>
		<p class="dev">
			<?php _e('[:uk]Створено з[:en]Created by[:]'); ?> <b>Arsmoon</b>
			<a href="https://arsmoon.com" target="_blank" data-defer="https://arsmoon.com/brand-wp-admin/logo_black.svg">
				<small><?php _e('[:uk]Веб-дизайн та розробка сайтів в Україні[:en]Top rated web development company[:]'); ?></small>
			</a>
		</p>
	</div>
</footer>

<script defer src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>
<?php wp_footer(); ?>
</body>
</html>
<?php if(defined('WP_DEBUG') && true !== WP_DEBUG) {
	ob_end_flush();
} ?>
