<?php
	/* Creates a post html element. Used by column building class to insert 
		formatted post element.
	*/
?>
<article class="post post-synopsis">
<?php if( has_post_thumbnail() ) {
	echo get_the_post_thumbnail();
} ?>
	<div class="article-body">
		<h2><?php echo get_the_title(); ?></h2>
		<p class="excerpt"><?php echo get_the_excerpt(); ?></p>
	</div>
	<div class="article-footer">
		<?php 
			$show_author = esc_attr( get_theme_mod( 'post-list-show-author' ) );
			if ( 'text' == $show_author ) : 
		?>
		<p class="article-author"><?php echo get_the_author(); ?></p>
		<?php 
			elseif ( 'link' == $show_author ) : 
		?>
		<a href="<?php echo get_author_posts_url( get_the_author_id() ); ?>" class="article-author"><p><?php echo get_the_author(); ?></p></a>
		<?php 
			endif; 
		?>
		
		<?php if ( esc_attr( get_theme_mod( 'post-list-show-date' ) ) ) : ?>
		<p class="article-date"><?php echo get_the_date( 'M j Y' ); ?></p>
		<?php endif; ?>
<?php
	// Show footer metabox if data present.
	$content = get_post_meta( get_the_ID(), 'custom_editor', true );
	if ( $content && esc_attr( get_theme_mod( 'post-list-show-footer' ) ) ) {
		echo $content;
	}
	
	// Show footer link metabox.
	$link = get_post_meta( get_the_ID(), 'footer-link-href', true );
	if ( $link && esc_attr( get_theme_mod( 'post-list-show-external-link' ) )) : ?>
		<a href="<?php echo $link; ?>"><p><?php echo nl2br( get_post_meta( get_the_ID(), 'footer-link-display', true ) ); ?></p></a>
	<?php endif;
	
	// Show internal post link.
	$post_link = get_post_meta( get_the_ID(), 'category-link', true );
	if ( $post_link && esc_attr( get_theme_mod( 'post-list-show-internal-link' ) ) ) : ?>
		<a href="<?php echo get_permalink( $post_link ); ?>"><p><?php echo nl2br( get_post_meta( get_the_ID(), 'footer-internal-link-display', true ) ); ?></p></a>
	<?php endif;
?>
	</div>
</article>
