<?php

function mytheme_customize_register_featured_post( $wp_customize ) {
	$wp_customize->add_section(
		'featured-post-section' , array(
			'title'      => __( 'Featured Post', 'mytheme' ),
			'priority'   => 30,
		)
	);
	
   $wp_customize->add_setting( 'show-featured-post' , array(
		'default'   => false,
	) );
	
	$wp_customize->add_control(
		'featured-post', 
		array(
			'label'    => __( 'Enable Featured Post', 'mytheme' ),
			'section'  => 'featured-post-section',
			'settings' => 'show-featured-post',
			'type'     => 'checkbox',
		)
	);
	
	$wp_customize->add_setting(
		'featured-post-option',
		array(
			'default' => 'null',
			'capability'     => 'edit_theme_options',
		)
	);
	
	// Create args array
	$posts = get_posts(
		array(
			'numberposts' => 20,
			'orderby' => 'date',
     		'order' => 'DESC'
		)
	);
	
	$args['null'] = 'None';
	foreach ( $posts as $post ) {
		$args[$post->ID] = $post->post_title;
	}
	
	$wp_customize->add_control(
		'featured-post-selector',
		array(
			'label'				=> __( 'Choose Featured Post', 'mytheme' ),
			'description'		=>	__( "Lists the most recent posts. If the desired post isn't listed you can set the featured post when editing a post." ),
			'section'			=> 'featured-post-section',
			'settings'			=> 'featured-post-option',
			'type'				=> 'select',
			'choices'			=> $args,
		)
	);
    
	$wp_customize->add_setting(
		'featured-post-layout' , array(
			'default'		=> '1'
		)
	);
	
	$wp_customize->add_control(
		'featured-post-layout',
		array(
			'label'          => __( 'Choose Layout', 'mytheme' ),
			'section'        => 'featured-post-section',
			'settings'       => 'featured-post-layout',
			'type'           => 'select',
			'choices'        => array(
				'1'			=> 'Image above, Copy below',
				'2'			=>	'Image left, Copy wraps',
				'3'			=> 'Image right, Copy wraps',
			),
		)
	);
}

add_action( 'customize_register', 'mytheme_customize_register_featured_post' );

function mytheme_customize_css_featured_post() {	
}

add_action( 'wp_head', 'mytheme_customize_css_featured_post');

?>
