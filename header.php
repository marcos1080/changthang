<?php		
	/* Check for javascript presence. Only triggered if the splash screen is not
		removed and the link is clicked to navigate to the non-js site
	*/
   if( isset( $_GET['javascript'] ) ) {
   	if ($_GET['javascript'] == 'none' ) {
   		$_SESSION['javascript'] = false;
   	}
   }
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo get_bloginfo( 'name' ).' - '.get_bloginfo( 'description' ); ?></title>
		<meta charset="<?php bloginfo( 'charset' ); ?>"></meta>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1, maximum-scale=1"></meta>
<?php 
	// Load wordpress header stuff.
	wp_head(); 
?>
		
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<div id="title">
					<?php
						// Show custom logo if present.
						if ( has_custom_logo() ) {
							$custom_logo_id = get_theme_mod( 'custom_logo' );
							$logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
							echo '					<img id="logo" src="'. esc_url( $logo[0] ) .'">'."\n";
						}
					?>
					<div id="site-name">
						<h1><?php echo get_bloginfo( 'name' ); ?></h1>
					</div>
				</div>
				<?php get_template_part( 'menu' ); ?>
			</div>
