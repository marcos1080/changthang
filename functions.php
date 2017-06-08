<?php

add_theme_support( 'post-thumbnails' ); 
add_theme_support( 'custom-logo' );

function remove_admin_login_header() {
	remove_action('wp_head', '_admin_bar_bump_cb');
}

add_action('get_header', 'remove_admin_login_header');

function scripts() {
	// Load our main stylesheet.
	wp_enqueue_style( 'common-style', get_stylesheet_uri() );
	
	// Load specific stylesheets
	//$column_count = esc_attr( get_theme_mod( 'column-count' ) );
	//wp_enqueue_style( $column_count.'-columns', get_template_directory_uri().'/css/'.$column_count.'-column.css' );

	// Menu
	switch( esc_attr( get_theme_mod( 'menu-layout' ) ) ) {
		case 'accordion':
			wp_enqueue_script( 'menu-script',
				get_template_directory_uri() . '/javascript/menu-accordion.js',
				array( 'jquery' )
			);
			wp_enqueue_style( 'menu-accordion', get_template_directory_uri().'/css/menu-accordion.css' );
			break;
		case 'all':
			wp_enqueue_script( 'menu-script',
				get_template_directory_uri() . '/javascript/menu-submenu-tree.js',
				array( 'jquery' )
			);
			wp_enqueue_style( 'menu-accordion', get_template_directory_uri().'/css/menu-submenu-tree.css' );
			break;
		case 'side':
			break;
	}
	
}

function mobile_scripts() {
}

function desktop_scripts() {
}

add_action( 'wp_enqueue_scripts', 'scripts' );

function admin_scripts() {
	wp_enqueue_script( 'media-selector-script',
		get_template_directory_uri() . '/javascript/media-selector.js',
		array( 'jquery' )
	);
}

add_action( 'admin_enqueue_scripts', 'admin_scripts' );

// Load mobile or desktop specific scripts
if( wp_is_mobile() ) {
	add_action( 'wp_enqueue_scripts', 'mobile_scripts' );
} else {
	add_action( 'wp_enqueue_scripts', 'desktop_scripts' );
}

if ( ! function_exists( 'chang_setup' ) ) :

function chang_setup() {
	$header_image_args = array(
	'width'         => 0,
	'height'        => 0,
	'uploads'       => true,
	);
	add_theme_support( 'custom-header', $header_image_args );
	
	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu',      'chang' ),
		'social'  => __( 'Social Links Menu', 'chang' ),
	) );
}

endif;
add_action( 'after_setup_theme', 'chang_setup' );

/**
 * Register widget area.
 *
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
function chang_widgets_init() {
	register_sidebar( 
		array(
			'name'          => __( 'Widget Area', 'chang' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Add widgets here to appear in your menu.', 'chang' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	
	register_sidebar( 
		array(
			'name'          => __( 'Footer Widget Area', 'chang' ),
			'id'            => 'sidebar-2',
			'description'   => __( 'Add widgets here to appear in your footer.', 'chang' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}

add_action( 'widgets_init', 'chang_widgets_init' );

// Load css for footer.
function footer_css() {
	// Get number of widgets in the footer.
	$sidebars_widgets = wp_get_sidebars_widgets();
	$number_of_widgets = count( (array) $sidebars_widgets[ 'sidebar-2' ] );
	?>
		<style type="text/css">
			#footer-widget-area .widget {
				width: <?php echo ( 100 / $number_of_widgets ).'%'; ?>;
			}
		</style>	
	<?php
}

add_action( 'wp_head', 'footer_css' );

function custom_comments( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    switch( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback' : ?>
            <li <?php comment_class(); ?> id="comment<?php comment_ID(); ?>">
            <div class="back-link">< ?php comment_author_link(); ?></div>
        <?php break;
        default : ?>
            <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
            <article <?php comment_class(); ?> class="comment">
 
 				<!-- Comment author, time and date -->
				<p class="comment-meta"><?php comment_author(); ?>: 
												<?php comment_time(); ?>,
												<?php comment_date(); ?></p>
												
				<!-- Comment body -->
				<p class="comment-text">
					<?php echo nl2br(get_comment_text()); ?>
				</p>
				
            <div class="reply"><?php 
            comment_reply_link( array_merge( $args, array( 
            'reply_text' => 'Reply',
            'depth' => $depth,
            'max_depth' => $args['max_depth'] 
            ) ) ); ?>
            </div><!-- .reply -->
 
            </article><!-- #comment-<?php comment_ID(); ?> -->
        <?php // End the default styling of comment
        break;
    endswitch;
}

/**
 * Filter the except length to 20 characters.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function wpdocs_custom_excerpt_length( $length ) {
	return 20;
}

add_filter( 'excerpt_length', 'wpdocs_custom_excerpt_length', 999 );



/* functions for navigation */
function top_post_nav( $prev, $next ) {
	echo '<div class="mobile-post-nav">';
	echo '	'.$prev;
	echo '</div>';
	
	post_nav( $prev, $next );
}

function bottom_post_nav( $prev, $next ) {
	echo '<div class="mobile-post-nav">';
	echo '	'.$next;
	echo '</div>';
	
	post_nav( $prev, $next );
}


function post_nav( $prev, $next ) {
	echo '<div class="post-nav">';
	echo '	<div class="post-nav-prev">';
	echo '		'.$prev;
	echo '	</div>';
	echo '	<div class="post-nav-next">';
	echo '		'.$next;
	echo '	</div>';
	echo '</div>';
}

/*******************************************************************************

	Load column builder funtions. Used for non-js browsing.

*******************************************************************************/

get_template_part( 'functions', 'column_builder' );

/*******************************************************************************

	Custom Meta Box for Post. Footer Info

*******************************************************************************/

get_template_part( 'functions', 'metaboxes' );

/*******************************************************************************

	Load theme options page.

*******************************************************************************/

add_action('admin_init', get_template_part( 'functions', 'theme_options' ) );
add_action('admin_init', get_template_part( 'inc/social_media_icons' ) );

/*******************************************************************************

	Load theme widgets.

*******************************************************************************/

add_action('admin_init', get_template_part( 'widgets/social_media_icons_widget' ) );
add_action('admin_init', get_template_part( 'widgets/attribution_widget/attribution_widget' ) );
add_action('admin_init', get_template_part( 'widgets/social_media_widget/social_media_widget' ) );
add_action('admin_init', get_template_part( 'widgets/search_widget/search_widget' ) );

/*******************************************************************************

	Load customizer options.

*******************************************************************************/

add_action('admin_init', get_template_part( 'inc/customizer', 'page_layout' ) );
add_action('admin_init', get_template_part( 'inc/customizer', 'general_layout' ) );
add_action('admin_init', get_template_part( 'inc/customizer', 'menu' ) );
add_action('admin_init', get_template_part( 'inc/customizer', 'featured_post' ) );
add_action('admin_init', get_template_part( 'inc/customizer', 'post_list' ) );
add_action('admin_init', get_template_part( 'inc/customizer', 'header' ) );
add_action('admin_init', get_template_part( 'inc/customizer', 'footer' ) );

?>
