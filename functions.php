<?php

add_theme_support( 'post-thumbnails' ); 
add_theme_support( 'custom-logo' );

#function remove_admin_login_header() {
#	remove_action('wp_head', '_admin_bar_bump_cb');
#}

#add_action('get_header', 'remove_admin_login_header');

function javascript_scripts() {
}

function mobile_scripts() {
}

function desktop_scripts() {
}

add_action( 'wp_enqueue_scripts', 'javascript_scripts' );

// Load mobile or desktop specific scripts
if( wp_is_mobile() ) {
	add_action( 'wp_enqueue_scripts', 'mobile_scripts' );
} else {
	add_action( 'wp_enqueue_scripts', 'desktop_scripts' );
}

if ( ! function_exists( 'breeturner_setup' ) ) :

function header_setup() {
	$header_image_args = array(
	'width'         => 0,
	'height'        => 0,
	'uploads'       => true,
	);
	add_theme_support( 'custom-header', $header_image_args );
}

header_setup();

endif;

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

/* Column builder classes and functions. */

/* Convert integer to text representation. Used for css tags. */
function num_to_text( $number ) {
	// Just going up to 6, don't think I'll need more. Adjust if neccessary.
	$text = '';
	
	switch ( $number ) {
		case 1: 
			$text = 'one';
			break;
		case 2: 
			$text = 'two';
			break;
		case 3: 
			$text = 'three';
			break;
		case 4: 
			$text = 'four';
			break;
		case 5: 
			$text = 'five';
			break;
		case 6: 
			$text = 'six';
			break;
	}
	
	return $text;
}

class Post_Wrapper {
	var $col_wrappers;
	
	function __construct( $num_of_cols ) {
		$this->col_wrappers = [];
		
		for( $count = 0; $count < $num_of_cols; $count++ ) {
			array_push( $this->col_wrappers, new Column_Wrapper( $count + 1 ) );
		}
	}
	
	function add_post( $post_ID ) {
		foreach( $this->col_wrappers as $wrapper ) {
			$wrapper->add_post( new Post( $post_ID ) );
		}
	}
	
	function print() {
		foreach( $this->col_wrappers as $wrapper ) {
			$wrapper->print();
		}
	}
}

class Column_Wrapper {
	var $num_of_cols;
	var $columns;

	function __construct( $num_of_cols ) {
		$this->num_of_cols = $num_of_cols;
		$this->columns = [];
		
		for( $count = 0; $count < $num_of_cols; $count++ ) {
			array_push( $this->columns, new Column( $count + 1 ) );
		}
	}
	
	function add_post( $post ) {
		// Get smallest column.
		$count = 0;
		$smallest = $this->columns[$count];
		
		for( ; $count < $this->num_of_cols; $count++ ) {
			if ( $this->columns[$count]->get_height() < $smallest->get_height() ) {
				$smallest = $this->columns[$count];
			}
		}
		$smallest->add_post( $post );
	}
	
	function print() {
		$num_of_cols = num_to_text( $this->num_of_cols );
		$label = 'columns';
		if ( $this->num_of_cols == 1 ) {
			$label = 'column';
		}
		echo '<div id="'.$num_of_cols.'-'.$label.'">';
		foreach( $this->columns as $column ) {
			$column->print();
		}
		echo '</div>';
	}
}

class Column {
	var $col_number;
	var $height;
	var $posts;
	
	function __construct( $col_number ) {
		$this->col_number = num_to_text( $col_number );
		$this->height = 0;
		$this->posts = [];
	}
	
	function get_height() {
		return $this->height;
	}
	
	function add_post( $post ) {
		array_push($this->posts, $post);
		if ( $post->has_thumbnail() == true ) {
			$this->height += 2;
		} else {
			$this->height += 1;
		}
	}
	
	function print() {
		echo '<ul class="column-'.$this->col_number.' posts">';
		foreach( $this->posts as $post ) {
			$post->print();
		}
		echo '</ul>';
	}
}

class Post {
	var $post;
	
	function __construct( $post ) {
		$this->post = new WP_Query( array( 'post_type' => 'post',
													  'p' => $post ) );
	}
	
	function has_thumbnail() {
		$this->post->the_post();
		$has_thumb = false;
		if( has_post_thumbnail() ) {
			$has_thumb = true;
		}
		
		$this->post->rewind_posts();
													  
		return $has_thumb;
	}
	
	function print() {
		$this->post->the_post();
		
		// Call blog-post.php to print html.
		get_template_part( 'blog', 'post' );
	}
}

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

?>
