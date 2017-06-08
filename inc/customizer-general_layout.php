<?php
	function chang_customize_register_general( $wp_customize ) {
		$wp_customize->add_section(
			'general-layout-section' , array(
				'title'			=> __( 'General Layout', 'mytheme' ),
				'panel'			=> 'page-layout-panel',
				'priority'		=> 10,
			)
		);
	
		$wp_customize->add_setting(
			'content-max-width' , array(
				'default'		=> '960'
			)
		);

		$wp_customize->add_control(
		 	new WP_Customize_Range_Control(
		     $wp_customize,
		     'content-max-width',
		     array(
					'label'          => __( 'Max Content Width', 'mytheme' ),
					'description'		=> __( 'Set the maximum width the content of the page can be. Measured in pixels.' ),
					'section'        => 'general-layout-section',
					'settings'       => 'content-max-width',
					'input_attrs' => array(
						'min' => 960,
						'max' => 1900,
						'step' => 20,
					),
				)
			)
		);
	
		$wp_customize->add_setting(
			'content-side-margin' , array(
				'default'		=> '10'
			)
		);
	
		$wp_customize->add_control(
		 	new WP_Customize_Range_Control(
		     $wp_customize,
		     'content-side-margin',
		     array(
					'label'          => __( 'Side Margin', 'mytheme' ),
					'description'		=> __( 'Set the size of the gap between the page content and the side of the window.. Measured in pixels.' ),
					'section'        => 'general-layout-section',
					'settings'       => 'content-side-margin',
					'input_attrs' => array(
						'min' => 0,
						'max' => 100,
						'step' => 1,
					),
				)
			)
		);
		
		$wp_customize->add_setting(
			'content-vertical-margin' , array(
				'default'		=> '10'
			)
		);
	
		$wp_customize->add_control(
		 	new WP_Customize_Range_Control(
		     $wp_customize,
		     'content-vertical-margin',
		     array(
					'label'          => __( 'Top & Bottom Margin', 'mytheme' ),
					'description'		=> __( 'Set the size of the gap between the page content and the top and bottom of the window. Measured in pixels.' ),
					'section'        => 'general-layout-section',
					'settings'       => 'content-vertical-margin',
					'input_attrs' => array(
						'min' => 0,
						'max' => 100,
						'step' => 1,
					),
				)
			)
		);
	}

	add_action( 'customize_register', 'chang_customize_register_general' );
	
	function chang_customize_css_general() {
		// Max Width
		$max_width = esc_attr( get_theme_mod( 'content-max-width' ) );
		?>
			<style type="text/css">
				#wrapper {
					max-width: <?php echo $max_width; ?>px;
				}
			</style>
		<?php
		// Content
		$content_margin = esc_attr( get_theme_mod( 'content-side-margin' ) );
		?>
			<style type="text/css">
				#menu,
				#content {
					margin-left: <?php echo $content_margin; ?>px;
					margin-right: <?php echo $content_margin; ?>px;
				}
			</style>
		<?php
		// Top margin
		$content_vertical_margin = esc_attr( get_theme_mod( 'content-vertical-margin' ) );
		?>
			<style type="text/css">
				#wrapper {
					margin-top: <?php echo $content_vertical_margin; ?>px;
					margin-bottom: <?php echo $content_vertical_margin; ?>px;
				}
			</style>
		<?php
	}
	
	add_action( 'wp_head', 'chang_customize_css_general');
?>
