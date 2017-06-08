<?php 
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	// Check to see if request is an AJAX request.
?>

<?php	get_header(); ?>
	<div id="main">
		<div id="content" class="category">
		<h2>Category: <?php single_cat_title(); ?></h2>
<?php
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
	?>
		</div>
	</div>
	<div id="delimiter">
	</div>
<?php	get_footer(); ?>
