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
		<meta charset="UTF-8"></meta>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1, maximum-scale=1"></meta>
		
<?php 
	// Load wordpress header stuff.
	wp_head(); 
?>
		
	</head>
	<body data-uri="<?php echo get_template_directory_uri() ?>">
		<div id="wrapper">
			<div id="header">		
			</div>
