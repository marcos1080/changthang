<?php
	function chang_customize_register_footer( $wp_customize ) {
		$wp_customize->add_section(
			'footer-layout-section' , array(
				'title'			=> __( 'Footer', 'mytheme' ),
				'panel'			=> 'page-layout-panel',
				'priority'		=> 20,
			)
		);
	
		$wp_customize->add_setting(
			'footer-vertical-margin' , array(
				'default'		=> '0'
			)
		);
	
		$wp_customize->add_control(
		 	new WP_Customize_Range_Control(
		     $wp_customize,
		     'footer-vertical-margin',
		     array(
					'label'          => __( 'Top and Bottom Footer Spacing', 'mytheme' ),
					'description'		=> __( 'Measured in pixels.' ),
					'section'        => 'footer-layout-section',
					'settings'       => 'footer-vertical-margin',
					'input_attrs' => array(
						'min' => 0,
						'max' => 60,
						'step' => 1,
					),
				)
			)
		);
	}

	add_action( 'customize_register', 'chang_customize_register_footer' );
	
	function chang_customize_css_footer() {
		// Vertical margin
		$vertical_margin = esc_attr( get_theme_mod( 'footer-vertical-margin' ) );
		?>
			<style type="text/css">
				#footer {
					margin-top: <?php echo $vertical_margin; ?>px;
					margin-bottom: <?php echo $vertical_margin; ?>px;
				}
			</style>
		<?php
	}
	
	add_action( 'wp_head', 'chang_customize_css_footer');
?>
