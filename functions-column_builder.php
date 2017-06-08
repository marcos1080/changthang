<?php
	/* Column builder classes and functions. */

	/* Convert integer to text representation. Used for css tags. */
	function num_to_text( $number ) {
		// Just going up to 10, don't think I'll need more. Adjust if neccessary.
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
			case 7: 
				$text = 'seven';
				break;
			case 8: 
				$text = 'eight';
				break;
			case 9: 
				$text = 'nine';
				break;
			case 10: 
				$text = 'ten';
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
			echo '<div id="'.$num_of_cols.'-'.$label.'" class="column-wrapper">';
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
			echo '<ul class="column-'.$this->col_number.' column posts">';
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
			get_template_part( 'post', 'synopsis' );
		}
	}
?>
