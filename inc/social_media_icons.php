<?php

	/*
	 * Social Media Icon functionality for Chang.
	 *
	 * @package WordPress
	 * @subpackage Chang
	 * @since Chang 1.0
	 */

	// Option to hold the id of saved icons.
	add_option( 'social_media_icon_ids', [], '', 'yes' );
	
	// Used to show temp icon fields after add button pressed.
	add_action('init', 'myStartSession', 1);
	add_action('wp_logout', 'myEndSession');
	add_action('wp_login', 'myEndSession');

	function myStartSession() {
		if(!session_id()) {
			session_start();
			
			// If the last value of the temp id array contains a valid option id then update the main id array.
			if ( isset( $_SESSION['temp_id_array'] ) ) {
				$last_value = end( $_SESSION['temp_id_array'] );
				if ( get_option( 'social-media-icon-'.$last_value ) ) {
					update_option( 'social_media_icon_ids', $_SESSION['temp_id_array'] );
				}
			}
		}
	}

	function myEndSession() {
		 session_destroy ();
	}



	// Redirect to same page on add or delete.
	function redirect_to_social_media_icon_page() {
		$url = urldecode( $_POST['_wp_http_referer'] );
      wp_safe_redirect( $url );
      exit;
	}

	// Handle add button clicked.
	function add_social_media_icon() {
		// Get lowest empty id. Starts from 0 and increments by one.
		$id_array = get_option( 'social_media_icon_ids' );
		$candidate_id = 0;
		
		// Get lowest free id.
		while ( in_array( $candidate_id, $id_array ) ) {
			$candidate_id++;
		}
		
		array_push( $id_array, $candidate_id );
		$_SESSION['temp_id_array'] = $id_array;
		$_SESSION['add'] = true;
		
		// Go back to page.
		redirect_to_social_media_icon_page();
	}
	
	add_action( 'admin_post_add_social_media_icon', 'add_social_media_icon' );



	// Handle delete button clicked.
	function delete_social_media_icon() {
		// Get lowest empty id. Starts from 0 and increments by one.
		$id_array = get_option( 'social_media_icon_ids' );
		$id = $_REQUEST['id'];
		
		// Remove id from id array.
		if ( ( $key = array_search( $id, $id_array ) ) !== false ) {
			unset( $id_array[$key] );
		}
		update_option( 'social_media_icon_ids', $id_array );
		
		// Remove seting from database.
		unregister_setting( 'social-media-section-'.$id, 'social-media-icon-'.$id);
		delete_option( 'social-media-icon-'.$id );
		
		// Go back to page.
		redirect_to_social_media_icon_page();
	}
	
	add_action( 'admin_post_delete_social_media_icon', 'delete_social_media_icon' );



	function display_social_media_icon( $args ) {
		wp_enqueue_media();
		$option_name = 'social-media-icon-'.$args['id'];
		$icon_values = get_option( $option_name );
		?>
			<div id="social-media-icon-<?php echo $args['id']; ?>">
				<div style="display: inline-block;">
					<div class='icon-preview-wrapper' style="display: inline-block;">
						<img class='icon-preview' src='<?php echo wp_get_attachment_image_url( $icon_values['icon_id'] ); ?>' width='48' height='48' style='max-height: 48px; max-width: 48px;' required />
					</div>
					<div style="display: inline-block; vertical-align: top; margin-left: 5px;">
						<label style="display: block; margin-left: 2px;">Main</label>
						<input class="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
						<input type='hidden' name='<?php echo $option_name; ?>[icon_id]' class='icon_id' value='<?php echo $icon_values['icon_id']; ?>' />
					</div>
				</div>
				<div style="display: inline-block; margin-left: 20px;">
					<div class='icon-preview-wrapper' style="display: inline-block;">
						<img class='icon-preview' src='<?php echo wp_get_attachment_image_url( $icon_values['hover_id'] ); ?>' width='48' height='48' style='max-height: 48px; width: 48px;' />
					</div>
					<div style="display: inline-block; vertical-align: top; margin-left: 5px;">
						<label style="display: block; margin-left: 2px;">Hover (Optional)</label>
						<input class="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
						<input type='hidden' name='<?php echo $option_name; ?>[hover_id]' class='icon_id' value='<?php $icon_values['hover_id']; ?>' />
					</div>
				</div>
			</div>
		 <?php
	}
	
	function display_social_media_icon_title( $args ) {
		$option_name = 'social-media-icon-'.$args['id'];
		$icon_values = get_option( $option_name );
		?>
			<input type='text' id='social-media-icon-title-<?php echo $args['id']; ?>' name='<?php echo $option_name; ?>[title]' value='<?php echo $icon_values['title']; ?>' style='width: 100%;' required />
		<?php
	}
	
	function display_social_media_icon_url( $args ) {
		$option_name = 'social-media-icon-'.$args['id'];
		$icon_values = get_option( $option_name );
		?>
			<input type='text' id='social-media-icon-url-<?php echo $args['id']; ?>' name='<?php echo $option_name; ?>[url]' value='<?php echo $icon_values['url']; ?>' style='width: 100%;' required />
		<?php
	}

	function display_social_media_settings() {
		$id_array = get_option( 'social_media_icon_ids' );

		if ( isset( $_SESSION['temp_id_array'] ) ) {
			$id_array = $_SESSION['temp_id_array'];
		}
		
		foreach( $id_array as $id ) {
			add_settings_section('social-media-section-'.$id, 'Social Media Icon '.$id, null, 'social_media');
			add_settings_field('social-media-icon-'.$id, 'Icon', 'display_social_media_icon', 'social_media', 'social-media-section-'.$id, array( 'id' => $id ) );
			add_settings_field('social-media-icon-title-'.$id, 'Title', 'display_social_media_icon_title', 'social_media', 'social-media-section-'.$id, array( 'id' => $id ) );
			add_settings_field('social-media-icon-url-'.$id, 'Url', 'display_social_media_icon_url', 'social_media', 'social-media-section-'.$id, array( 'id' => $id ) );

			register_setting( 'social-media-section-'.$id, 'social-media-icon-'.$id ); 
		}
	}

	add_action("admin_init", "display_social_media_settings");

	function social_media_icon_page() {
		// Used by the add and delete buttons to return to this page.
		$redirect = urlencode( $_SERVER['REQUEST_URI'] );
		
		// Array used to display each form.
		$id_array = get_option( 'social_media_icon_ids' );
		if ( isset( $_SESSION['add'] ) ) {
			$id_array = $_SESSION['temp_id_array'];
		} else {
			unset( $_SESSION['temp_id_array'] );
		}
		
		// Used to style delete button.
		$delete_style = array( 
			'style' => 'margin: -28px 0px 0px 111px; background-color: rgb(232, 57, 18); color: white; border-color: rgb(130, 34, 13);'
		);
		
		?>
		<div class="wrap">
			<h2>Social Media Icons</h2>
			<?php foreach( $id_array as $id ) :?>
				<form method="post" action="options.php">
					<?php if ( $icon_values = get_option( 'social-media-icon-'.$id ) ) : ?>
						<h3><?php echo $icon_values['title']; ?></h3>
					<?php else : ?>
						<h3>New Icon</h3>
					<?php endif; ?>
					<?php settings_fields( 'social-media-section-'.$id ); ?>
						<table class="social-media-icon-table" style="margin: 20px 0px;">
							<?php do_settings_fields( 'social_media', 'social-media-section-'.$id ); ?>
						</table> 
					<?php submit_button( 'Save Changes', 'primary', 'submit', false, array( 'style' => 'display: block;' ) ); ?>
			 	</form>
	    	
	    		<?php // Only show delete button if there is an option registered with this id in the id array. ?>
	    		<?php if ( get_option( 'social-media-icon-'.$id ) ) : ?>
				 	<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" style="display: inline-block;">
				 		<input type="hidden" name="action" value="delete_social_media_icon">
				 		<input type="hidden" name="_wp_http_referer" value="<?php echo $redirect; ?>">
				 		<input type="hidden" name="id" value="<?php echo $id; ?>">
		  				<?php submit_button( 'Delete', 'delete', 'delete', false, $delete_style ); ?>
				 	</form>
			 	<?php endif; ?>
	    	<?php endforeach; ?>

	    	<?php // Only show new button if there is an option registered with the last id in the id array. ?>
	    	<?php if ( ! isset( $_SESSION['add'] ) ) : ?>
			 	<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
			 		<input type="hidden" name="action" value="add_social_media_icon">
			 		<input type="hidden" name="_wp_http_referer" value="<?php echo $redirect; ?>">
	  				<?php submit_button( 'New Icon' ); ?>
			 	</form>
	    	<?php endif; ?>
		</div>
		<?php
		
		unset( $_SESSION['add'] );
	}

	function add_theme_social_media_page()
	{		
		add_theme_page("Social Media Icons", "Social Media Icons", "edit_theme_options", "social_media", "social_media_icon_page");
	}

	add_action("admin_menu", "add_theme_social_media_page");

	// Add style for elements.
	function social_media_icon_css() {
		?>
		<style type="text/css">
			.social-media-icon-table th {
				text-align: left;
				min-width: 100px;
			}
			
			.wrap form .delete:hover {
				background-color: rgb(244, 76, 39) !important;
			}
		</style>
		<?php
	}
	
	add_action( 'admin_head', 'social_media_icon_css');
?>
