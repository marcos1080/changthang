<?php
	
	// Register Options.	
	function display_copyright_element() {
		?>
		 	<input type="text" name="copyright-name" id="copyright-name" value="<?php echo get_option('copyright-name'); ?>" />
		 <?php
	}
	
	function theme_settings_page() {
	?>
	    <div class="wrap">
	    <h1>Theme Settings</h1>
	    <form method="post" action="options.php">
	        <?php
	            settings_fields("section");
	            do_settings_sections("theme-options");      
	            submit_button(); 
	        ?>          
	    </form>
		</div>
	<?php
	}

	function add_theme_menu_item()
	{
		add_menu_page("Theme Settings", "Theme Settings", "manage_options", "theme-settings", "theme_settings_page", null, 99);
	}

	add_action("admin_menu", "add_theme_menu_item");
	
	function display_theme_settings_fields() {
		add_settings_section("section", "All Settings", null, "theme-options");
	
		add_settings_field("copyright-name", "Copyright Name in Footer", "display_copyright_element", "theme-options", "section");

		register_setting("section", "copyright-name");
	}

	add_action("admin_init", "display_theme_settings_fields");
?>
