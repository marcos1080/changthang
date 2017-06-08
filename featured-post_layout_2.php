<?php
	// Fetured Post layout.
	
	$post = get_post( get_theme_mod( 'featured-post-option' ) );
	$footer = "<p>".nl2br( get_post_meta( $post->ID, 'custom_editor', true ) )."</p>\n";
	
	$footer_link = esc_attr( get_post_meta( $post->ID, 'footer-link-href', true ) );
	if ( $footer_link ) {
		$footer_link_display = nl2br( get_post_meta( $post->ID, 'footer-link-display', true ) );
		$footer_link = '<a href="'.$footer_link.'"><p>'.$footer_link_display."</p></a>\n";
	}
	
	$category_link = esc_attr( get_post_meta( $post->ID, 'category-link', true ) );
	if ( $category_link ) {
		$category_link_display = nl2br( get_post_meta( $post->ID, 'footer-internal-link-display', true ) );
		$category_link = '<a href="'.get_permalink( $category_link ).'"><p>'.$category_link_display."</p></a>\n";
	}
?>

			<div id="featured-post-layout-2">
				<div id="featured-post-image">
					<?php echo get_the_post_thumbnail( $post->ID ); ?>
				</div>
				<time datetime="<?php $post->post_date; ?>"></time>
				<h2><?php echo $post->post_title; ?></h2>
<?php
	// Display the content. Using setup_postdata and wp_reset_postdata in order
	// use the_content(). This allows inserting of paragraphs etc.
	setup_postdata( $post );
	the_content();
	wp_reset_postdata( $post );
?>
				<div id="featured-post-footer">
					<?php echo $footer; ?>
					<?php echo $category_link; ?>
					<?php echo $footer_link; ?>
				</div>
			</div>
