<?php

function mytheme_customize_register_post_list( $wp_customize ) {	
	$wp_customize->add_section(
		'post-list-layout-section' , array(
			'title'			=> __( 'Main Post List', 'mytheme' ),
			'panel'			=> 'page-layout-panel',
			'priority'		=> 30,
		)
	);
	
	$wp_customize->add_setting(
		'column-count' , array(
			'default'		=> '1'
		)
	);
	
	$wp_customize->add_control(
    	new WP_Customize_Range_Control(
        $wp_customize,
        'column-count',
        array(
				'label'          => __( 'Number of Columns', 'mytheme' ),
				'description'		=> __( 'Based on full screen width of 1920px. Actual number will depend on device and screen size.' ),
				'section'        => 'post-list-layout-section',
				'settings'       => 'column-count',
				'input_attrs' => array(
					'min' => 1,
					'max' => 10,
					'step' => 1,
				),
			)
		)
	);
	
	$wp_customize->add_setting(
		'column-margin' , array(
			'default'		=> '0'
		)
	);
	
	$wp_customize->add_control(
    	new WP_Customize_Range_Control(
        $wp_customize,
        'column-margin',
        array(
				'label'				=> __( 'Space Between Columns', 'mytheme' ),
				'description'		=> __( 'Mesured in pixels.' ),
				'section'			=> 'post-list-layout-section',
				'settings'			=> 'column-margin',
				'input_attrs'		=> array(
					'min'					=> 0,
					'max'					=> 40,
					'step'				=> 1,
				),
			)
		)
	);
	
	$wp_customize->add_setting(
		'post-list-show-author' , array(
			'default'		=> null
		)
	);
	
	$wp_customize->add_control(
		'post-list-show-author', 
		array(
			'label'    => __( 'Show Author', 'mytheme' ),
			'section'  => 'post-list-layout-section',
			'settings' => 'post-list-show-author',
			'type'     => 'radio',
			'choices'		=> array(
				'null'			=> 'Do not display',
				'text'			=> 'Regular text',
				'link'			=> 'Link to display authors posts',
			)
		)
	);
	
	$wp_customize->add_setting(
		'post-list-show-date' , array(
			'default'		=> true
		)
	);
	
	$wp_customize->add_control(
		'post-list-show-date', 
		array(
			'label'    => __( 'Show Date', 'mytheme' ),
			'section'  => 'post-list-layout-section',
			'settings' => 'post-list-show-date',
			'type'     => 'checkbox',
		)
	);
	
	$wp_customize->add_setting(
		'post-list-show-footer' , array(
			'default'		=> true
		)
	);
	
	$wp_customize->add_control(
		'post-list-show-footer', 
		array(
			'label'    => __( 'Show Footer Info', 'mytheme' ),
			'section'  => 'post-list-layout-section',
			'settings' => 'post-list-show-footer',
			'type'     => 'checkbox',
		)
	);
	
	$wp_customize->add_setting(
		'post-list-show-internal-link' , array(
			'default'		=> true
		)
	);
	
	$wp_customize->add_control(
		'post-list-show-internal-link', 
		array(
			'label'    => __( 'Show Links To Other Posts', 'mytheme' ),
			'section'  => 'post-list-layout-section',
			'settings' => 'post-list-show-internal-link',
			'type'     => 'checkbox',
		)
	);
	
	$wp_customize->add_setting(
		'post-list-show-external-link' , array(
			'default'		=> true
		)
	);
	
	$wp_customize->add_control(
		'post-list-show-external-link', 
		array(
			'label'    => __( 'Show Regular Links', 'mytheme' ),
			'section'  => 'post-list-layout-section',
			'settings' => 'post-list-show-external-link',
			'type'     => 'checkbox',
		)
	);
}

add_action( 'customize_register', 'mytheme_customize_register_post_list' );

function mytheme_customize_css_post_list() {	
	// Post synopsis margins.
	?>
		<style type="text/css">
			.post-synopsis {
				margin: 0px <?php echo esc_attr( get_theme_mod( 'column-margin' ) ) / 2; ?>px;
			}
			.column-wrapper {
				margin: 0px -<?php echo esc_attr( get_theme_mod( 'column-margin' ) ) / 2; ?>px;
			}
		</style>
	<?php
	
	// Column css.
	?>
		<style type="text/css">
			#one-columns {
				display: block;
			}
			<?php
				$column_count = esc_attr( get_theme_mod( 'column-count' ) );
				if ( 1 < $column_count ) :
					for ( $count = 2; $count <= $column_count; $count++ ) :
						?>
			#<?php echo num_to_text( $count ); ?>-columns {
				display: none;
			}
						<?php
					endfor;
					
					for ( $count = 2; $count <= $column_count; $count++ ) :
						$max_width = esc_attr( get_theme_mod( 'content-max-width' ) );
						$break_point = $max_width / $column_count * ( $count - 1 );
						?>
			@media screen and (min-width: <?php echo round( $break_point ); ?>px) {
				#<?php echo num_to_text( $count - 1 ); ?>-columns {
					display: none;
				}
	
				#<?php echo num_to_text( $count ); ?>-columns {
					display: block;
				}
			}	
						<?php
					endfor;
				endif;
			?>	
		</style>
	<?php
}

add_action( 'wp_head', 'mytheme_customize_css_post_list');

?>
