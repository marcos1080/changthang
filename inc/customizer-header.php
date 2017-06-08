<?php
	function chang_customize_register_header( $wp_customize ) {
		$wp_customize->add_section(
			'header-layout-section' , array(
				'title'			=> __( 'Header', 'mytheme' ),
				'panel'			=> 'page-layout-panel',
				'priority'		=> 20,
			)
		);
	
		$wp_customize->add_setting(
			'title-vertical-margin' , array(
				'default'		=> '1'
			)
		);
	
		$wp_customize->add_control(
		 	new WP_Customize_Range_Control(
		     $wp_customize,
		     'title-vertical-margin',
		     array(
					'label'          => __( 'Top and Bottom Title Spacing', 'mytheme' ),
					'description'		=> __( 'Measured in pixels.' ),
					'section'        => 'header-layout-section',
					'settings'       => 'title-vertical-margin',
					'input_attrs' => array(
						'min' => 1,
						'max' => 40,
						'step' => 1,
					),
				)
			)
		);
	
		$wp_customize->add_setting(
			'menu-height' , array(
				'default'		=> '1'
			)
		);
	
		$wp_customize->add_control(
		 	new WP_Customize_Range_Control(
		     $wp_customize,
		     'menu-height',
		     array(
					'label'          => __( 'Menu Height', 'mytheme' ),
					'description'		=> __( 'Measured in pixels.' ),
					'section'        => 'header-layout-section',
					'settings'       => 'menu-height',
					'input_attrs' => array(
						'min' => 25,
						'max' => 100,
						'step' => 1,
					),
				)
			)
		);
		
		$wp_customize->add_setting(
			'menu-item-padding' , array(
				'default'		=> '1'
			)
		);
	
		$wp_customize->add_control(
		 	new WP_Customize_Range_Control(
		     $wp_customize,
		     'menu-item-padding',
		     array(
					'label'          => __( 'Menu Item Gap', 'mytheme' ),
					'description'		=> __( 'Measured in pixels.' ),
					'section'        => 'header-layout-section',
					'settings'       => 'menu-item-padding',
					'input_attrs' => array(
						'min' => 0,
						'max' => 100,
						'step' => 1,
					),
				)
			)
		);
		
		$wp_customize->add_setting(
			'social-icons-max-height' , array(
				'default'		=> '30'
			)
		);
	
		$wp_customize->add_control(
		 	new WP_Customize_Range_Control(
		     $wp_customize,
		     'social-icons-max-height',
		     array(
					'label'          => __( 'Social Media Icons Max Height', 'mytheme' ),
					'description'		=> __( 'Measured in pixels.' ),
					'section'        => 'header-layout-section',
					'settings'       => 'social-icons-max-height',
					'input_attrs' => array(
						'min' => 30,
						'max' => 100,
						'step' => 1,
					),
				)
			)
		);
	}

	add_action( 'customize_register', 'chang_customize_register_header' );
	
	function chang_customize_css_header() {
		// Title
		$title_margin = esc_attr( get_theme_mod( 'title-vertical-margin' ) );
		?>
			<style type="text/css">
				#title {
					margin-top: <?php echo $title_margin; ?>px;
					margin-bottom: <?php echo $title_margin; ?>px;
				}
			</style>
		<?php
		
		// Menu height.
		$menu_height = esc_attr( get_theme_mod( 'menu-height' ) );
		?>
			<style type="text/css">
				#menu {
					height: <?php echo $menu_height; ?>px;
				}
				
				#menu.no-js .menu-level-1 > .children,
				#menu.no-js .widget > *:last-child {
					margin-top: <?php echo $menu_height; ?>px;
				}
				
				#menu .widget > *:first-child {
					margin-top: 0px;
				}
			</style>
		<?php
		
		// Menu item distance between items.
		$menu_item_padding = esc_attr( get_theme_mod( 'menu-item-padding' ) );
		?>
			<style type="text/css">
				.menu-level-1,
				.widget {
					padding-left: <?php echo $menu_item_padding; ?>px;
					padding-right: <?php echo $menu_item_padding; ?>px;
				}
			</style>
		<?php
		
		// Menu item distance between items.
		$social_icon_max_height = esc_attr( get_theme_mod( 'social-icons-max-height' ) );
		?>
			<style type="text/css">
				#social-media-icons-menu {
					max-height: <?php echo $social_icon_max_height; ?>px;
				}
				
				.social-media-icon {
					max-height: <?php echo $social_icon_max_height; ?>px;
					max-width: <?php echo $social_icon_max_height; ?>px;
				}
				
				.social-media-icon img {
					max-height: <?php echo $social_icon_max_height; ?>px;
					max-width: <?php echo $social_icon_max_height; ?>px;
				}
			</style>
		<?php
	}
	
	add_action( 'wp_head', 'chang_customize_css_header');
?>
