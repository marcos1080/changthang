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
	
	if (have_posts()) {
		while (have_posts()) {
			the_post();
			if( ! is_front_page() )	{
				echo "<h1>".get_the_title()."</h1>";
			}
			echo "<p>".the_content()."</p>";
		}
	} else {
		echo "<p>"._e('Sorry, no posts matched your criteria.')."</p>";
	}

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
