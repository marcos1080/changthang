<?php
	function chang_customize_register_menu( $wp_customize ) {
		$wp_customize->add_section(
			'menu-layout-section' , array(
				'title'			=> __( 'Menu', 'mytheme' ),
				'panel'			=> 'page-layout-panel',
				'priority'		=> 20,
			)
		);
	
		$wp_customize->add_setting(
			'menu-layout' , array(
				'default'		=> 'accordion'
			)
		);
	
		$wp_customize->add_control(
		'menu-layout', 
		array(
			'label'    => __( 'Primary Menu Layout', 'mytheme' ),
			'section'  => 'menu-layout-section',
			'settings' => 'menu-layout',
			'type'     => 'radio',
			'choices'		=> array(
				'accordion'		=> 'Accordion sub menus',
				'side'			=> 'Sidebar',
				'all'			=> 'Show all sub menu entries',
			)
		)
	);
	}

	add_action( 'customize_register', 'chang_customize_register_menu' );
	
	function chang_customize_css_menu() {
		
	}
	
	add_action( 'wp_head', 'chang_customize_css_menu');
?>
