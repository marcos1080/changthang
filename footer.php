			<div id="footer">
				<?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
					<div id="footer-widget-area" class="widget-area" >
						<?php dynamic_sidebar( 'sidebar-2' ); ?>
					</div><!-- .widget-area -->
				<?php endif; ?>
			</div>
		</div>
	<?php wp_footer(); ?>
	</body>
</html>
