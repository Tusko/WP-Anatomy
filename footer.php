</div>
<footer>
	<div class="copyright row">
		<p>&copy; <?php bloginfo('name'); ?> <?php echo date('Y'); ?>. <?php _e('[:uk]Всі права захищено[:en]All rights reserved[:]'); ?></p>
		<p class="dev">
			<?php esc_attr_e('[:ua]Створено з[:uk]Створено з[:en]Created by[:]'); ?> <b>Arsmoon</b>
			<a href="https://arsmoon.com" target="_blank" data-defer="https://arsmoon.com/brand-wp-admin/logo_black.svg">
				<small><?php esc_attr_e('[:ua]Веб-дизайн та розробка сайтів в Україні[:uk]Веб-дизайн та розробка сайтів в Україні[:en]Top rated web development company[:]'); ?></small>
            </a>
        </p>
    </div>
</footer>

<script>
    (function (w, k) {
        w[k] = {
            companyId: '14419',
            widgetId: '1',
            hash: '9eb1bbe2-2856-41c2-b510-3dae0e1efa27',
            locale: 'ua',
        };

        var d = w.document,
            s = d.createElement('script');

        s.async = true;
        s.id = k + 'Script';
        s.src = 'https://static.salesdrive.me/chat-widget/assets/js/widget.js' + '?' + (Date.now() / 3600000 | 0);

        var head = d.head || d.getElementsByTagName("head")[0];
        head.appendChild(s);

    }(window, 'salesDriveChatButton'));
</script>

<?php wp_footer(); ?>
</body>
</html>
