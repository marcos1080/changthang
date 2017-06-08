<?php 
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	// Check to see if request is an AJAX request.
		
	if( !isset( $_POST['ajax'] ) ) : 
?>

<?php	get_header(); ?>
	<div id="main">
<?php if ( is_front_page() ) : ?>
		<div id="content" class="home">
<?php else : ?>
		<div id="content">
<?php endif; ?>

<?php // Show banner image if home page.
	if ( is_front_page() ) :
		if ( get_header_image() ) : ?>
			<div class="header-image">			
				<img src="<?php echo header_image(); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
			</div>
	<?php endif;
	endif;

	// Featured post.
	if ( true == get_theme_mod( 'show-featured-post' ) ) {
		$featured_post = get_theme_mod( 'featured-post-option' );
		if ( $featured_post != 'null' ) {
			$layout = get_theme_mod( 'featured-post-layout' );
			if ( $layout == '1' ) {
				get_template_part( 'featured', 'post_layout_1' );
			} elseif ( $layout == '2' ) {
				get_template_part( 'featured', 'post_layout_2' );
			} elseif ( $layout == '3' ) {
				get_template_part( 'featured', 'post_layout_3' );
			}
		}
	}
	
	// List recent posts.
	$wrapper = new Post_Wrapper( esc_attr( get_theme_mod( 'column-count' ) ) );
	
	if (have_posts()) {
		while (have_posts()) {
			the_post();
			
			$wrapper->add_post( get_the_ID() );
		}
	} else {
		echo "<p>"._e('Sorry, no posts matched your criteria.')."</p>";
	}
	
	$wrapper->print();
	
	// Comments.
	if( !is_front_page() ) {
		echo comments_template();
	}
	?>
		</div>
	</div>
	<div id="delimiter">
	</div>
<?php	get_footer(); ?>

<?php else :
	// AJAX request, load functions to process request.
	get_template_part( 'ajax' );
	endif; 
?>
