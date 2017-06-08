<?php

/*******************************************************************************

	Custom Meta Box for Post. Footer Info

*******************************************************************************/

function wporg_add_custom_boxes()
{
    $screens = ['post'];
    foreach ($screens as $screen) {
        add_meta_box(
            'footer_editor', // Unique ID
            'Post Footer', // Box title
            'footer_editor', // Content callback, must be of type callable
            $screen // Post type
        );
        
        add_meta_box(
            'footer_link',
            'Footer Link',
            'footer_link',
            $screen
        );
        
        add_meta_box(
            'footer_internal_link',
            'Footer Parent Post',
            'footer_internal_link',
            $screen
        );
        
        add_meta_box(
        	'featured_post',
        	'Featured Post',
        	'featured_post',
        	'post',
        	'side'
        );
    }
}
add_action('add_meta_boxes', 'wporg_add_custom_boxes');

//Displaying the footer meta box
function footer_editor($post) {        
	wp_nonce_field(basename(__FILE__), "custom-editor-nonce"); 
	  
	$content = get_post_meta($post->ID, 'custom_editor', true);

	//This function adds the WYSIWYG Editor 
	wp_editor ( 
		$content , 
		'custom_editor', 
		array ( "media_buttons" => true ) 
	);
}

//Displaying the footer link meta box
function footer_link($post) {
	wp_nonce_field(basename(__FILE__), "footer-link-nonce");          
?>	
	<p>
		<label for="footer-link-display">Display Text</label><br>
		<textarea row="4" name="footer-link-display" style="width: 100%;"><?php echo esc_attr( get_post_meta( $post->ID, 'footer-link-display', true ) ); ?></textarea>
	</p>
	<p>
		<label for="footer-link-href">Link URL</label><br>
		<input type="text" name="footer-link-href" value="<?php echo esc_attr( get_post_meta( $post->ID, 'footer-link-href', true ) ); ?>" style="width: 100%;"/>
	</p>
<?php
}

//Displaying the footer internal link meta box
function footer_internal_link($post) {
	wp_nonce_field(basename(__FILE__), "footer-internal-link-nonce");
	// Following script ensures that only one checkbox is checked at any one time.
	// Using checkboxes rather than radio box to allow no categories to be selected.          
?>	
	<script>
		jQuery(document).ready(function() {
			var checkboxes = jQuery('.category-link-checkbox');
			
			// Show active categories
			checkboxes.each(function() {
				if ( jQuery(this).attr('checked') ) {
					jQuery(this).siblings('select').css({'display': 'inline-block'}).removeAttr('disabled');
				}
			});
			
			
			checkboxes.on('change', function() {
				jQuery(this).parent().siblings('p').find('.category-link-checkbox').attr('checked', false);
				jQuery(this).parent().siblings('p').find('select').css({'display': 'none'});
				if ( jQuery(this).attr('checked') ) {
					jQuery(this).siblings('select').css({'display': 'inline-block'}).removeAttr('disabled');
					jQuery(this).attr('checked', 'true');
				} else {
					jQuery(this).siblings('select').css({'display': 'none'}).attr('disabled', 'disabled');
					jQuery(this).removeAttr('checked');
				}
			});
		});
	</script>
	<p>
		<label for="footer-internal-link-display">Display Text</label><br>
		<input type="text" name="footer-internal-link-display" value="<?php echo esc_attr( get_post_meta( $post->ID, 'footer-internal-link-display', true ) ); ?>" style="width: 100%;"/>
	</p>
	<label>Choose Target Post</label>
	<div>
<?php
	$categories = get_categories();

	// Parent category, used for checkbox.
	$active_category = esc_attr( get_post_meta( $post->ID, 'footer-internal-category-select', true ) );
	// Post link, used in options for dropdown box.
	$category_link_id = esc_attr( get_post_meta( $post->ID, 'category-link', true ) );
	
	foreach ( $categories as $category ):
		// A parent index of 0 indicated that it is a main category.
		if ( $category->parent == 0 ) :?>
		<p>
			<input type="checkbox" class="category-link-checkbox" name="footer-internal-category-select[]" value="<?php echo $category->name; ?>" 
			<?php if ( $active_category == $category->name ) {
				echo 'checked';
			} ?> /><?php echo $category->name; ?>
			<select name="category-link" style="display: none;" disabled="disabled">
<?php
			$category_posts = get_posts(
				array(
					'category_name' => $category->name
				)
			);
			foreach ( $category_posts as $post ) :?>
				<option value="<?php echo $post->ID; ?>" 
				<?php if ( $post->ID == $category_link_id ) {
					echo 'selected="selected"';
				} ?>><?php echo $post->post_title; ?></option>
			<?php endforeach;
?>
			</select>
		</p>
<?php endif;
		$count += 1;
	endforeach; ?>
	</div>
<?php
}
 
//Displaying the featured post meta box
function featured_post($post) {        
	wp_nonce_field(basename(__FILE__), "featured-post-nonce"); 
	  
?>
	<div>
		<p>Checking this box allows this post to be used as the featured image.</p>
		<p>This will replace any current post that is being used.</p>
		<p><input type="checkbox" name="featured-post-metabox" value="<?php echo $post->ID; ?>"<?php
			if ( $post->ID == get_theme_mod( 'featured-post-option' ) ) {
				echo ' checked="checked"';
			}
		?>/> Feature this post</p>
	</div>
<?php
}

//This function saves the data you put in the meta box
 function custom_editor_save_postdata($post_id) {
        
	// Footer editor
	if( isset( $_POST['custom-editor-nonce'] ) ) {
 
		//Not save if the user hasn't submitted changes
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		} 

		// Verifying whether input is coming from the proper form
		if ( ! wp_verify_nonce ( $_POST['custom-editor-nonce'], basename( __FILE__ ) ) ) {
			return;
		} 
	} 
 
	if (!empty($_POST['custom_editor'])) {
	  $data = $_POST['custom_editor'];
	  update_post_meta($post_id, 'custom_editor', $data);
	}
	
	// Footer link
	if( isset( $_POST['footer-link-nonce'] ) ) {
 
		//Not save if the user hasn't submitted changes
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		} 

		// Verifying whether input is coming from the proper form
		if ( ! wp_verify_nonce ( $_POST['footer-link-nonce'], basename( __FILE__ ) ) ) {
			return;
		} 
	}
	
	if (!empty($_POST['footer-link-display'])) {
	  $data = $_POST['footer-link-display'];
	  update_post_meta($post_id, 'footer-link-display', $data);
	}
	
	if (!empty($_POST['footer-link-href'])) {
	  $data = $_POST['footer-link-href'];
	  update_post_meta($post_id, 'footer-link-href', $data);
	}
	
	// Footer internal link
	if( isset( $_POST['footer-internal-link-nonce'] ) ) {
 
		//Not save if the user hasn't submitted changes
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		} 

		// Verifying whether input is coming from the proper form
		if ( ! wp_verify_nonce ( $_POST['footer-internal-link-nonce'], basename( __FILE__ ) ) ) {
			return;
		} 
	}
	
	if (!empty($_POST['footer-internal-link-display'])) {
	  $data = $_POST['footer-internal-link-display'];
	  update_post_meta($post_id, 'footer-internal-link-display', $data);
	}
	
	if (!empty($_POST['footer-internal-category-select'])) {
		$data = $_POST['footer-internal-category-select'][0];
	  	update_post_meta($post_id, 'footer-internal-category-select', $data);
	} else {
		// Case for removal of link.
		delete_post_meta( $post_id, 'footer-internal-category-select' );
	}
	
	if ( !empty( $_POST['category-link'] ) ) {
		$data = $_POST['category-link'];
	  	update_post_meta( $post_id, 'category-link', $data );
	} else {
		// Case for removal of link.
		delete_post_meta( $post_id, 'category-link' );
	}
	
	// Footer internal link
	if( isset( $_POST['featured-post-nonce'] ) ) {
 
		//Not save if the user hasn't submitted changes
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		} 

		// Verifying whether input is coming from the proper form
		if ( ! wp_verify_nonce ( $_POST['featured-post-nonce'], basename( __FILE__ ) ) ) {
			return;
		} 
		
		if ( !empty( $_POST['featured-post-metabox'] ) ) {
			$data = $_POST['featured-post-metabox'];
		  	set_theme_mod( 'featured-post-option', $data );
		} else {
			// Case for removal of link.
			set_theme_mod( 'featured-post-option', 'null' );
		}
	}
}
 
add_action('save_post', 'custom_editor_save_postdata');

?>
