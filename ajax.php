<?php
	/*
		Processes a post request made from the postajax.js script called in the
		words page.
		
		Request parameters are set in the javascript file and processed here.
	*/
	
/*******************************************************************************

	Functions
	
*******************************************************************************/

	// 
	function return_posts( $args, $heading = null ) {
		$query = new WP_Query( $args );
		$num_of_pages = $query->max_num_pages;
		$postdata = [];
	
		// Set the heading for the results.
		if( $heading != null ) {
			if( $heading == 'Recent' ) {
				$postdata['heading'] = 'Recent Posts';
			} else {
				$postdata['heading'] = "Search Results for \"" . ucfirst( $heading  ) . "\"";
			}
		}
	
		// If there are posts resulting from the query then create posts object.
		if( $query->have_posts() ) {
			$count = 0;
			
			while ( $query->have_posts() ) {
				$query->the_post();
				$postdata['data'][$count]['href'] = get_permalink();
				if( has_post_thumbnail() ) {
					$postdata['data'][$count]['thumb'] = get_the_post_thumbnail();
				}
				$postdata['data'][$count]['title'] = get_the_title();
				$postdata['data'][$count]['date']['day'] = get_the_date( "j", "", "", false );
				$postdata['data'][$count]['date']['month'] = get_the_date( "M", "", "", false );
				$postdata['data'][$count]['excerpt'] = get_the_excerpt();
				
				$count++;
			}

			// This parameter lets the return function from the javascript post
			// file know that the posts are not from the first page and to add them
			// to the bottom of the posts element rather than build a new one.
			if( $args['paged'] == '1' ) {
				$postdata['add'] = false;
			} else {
				$postdata['add'] = true;
			}
		
			// If there are more pages after the current one then set the 'next'
			// parameter.
			if( $args['paged'] < $num_of_pages ) {
				$postdata['next'] = $args['paged'] + 1;
			}
			
			// Add filter information.
			if ( $heading == 'Recent' ) {
				$postdata['filter'] = 'recent';
			} else if ( isset( $args['cat'] ) ) {
				$postdata['filter'] = 'category';
				$postdata['category'] = $args['cat'];
			} else if ( isset( $args['date_query'] ) ) {
				$postdata['filter'] = 'archive';
				$postdata['month'] = $args['date_query']['month'];
				$postdata['year'] = $args['date_query']['year'];
			} else if ( isset( $args['s'] ) ) {
				$postdata['filter'] = 'search';
				$postdata['searchString'] = $args['s'];
			}
		
			// Encode to JSON string.
			echo json_encode( $postdata );
		} else {
			// No posts found for the query.
			echo json_encode( array( 'no_posts' => $heading ) );
		}
	
		// ??
		unset( $requested_posts );
	}
	
/*******************************************************************************

	Main
	
	Detect if an AJAX request has been made and return the relevant posts.
	
	The logic here creates an argument array for the return_posts function then 
	passes that array to the function.
	
	Notes:
		paged is a great attribute that returns the posts from a specified paged
		if there are more than one page of posts. THe number of posts in a page is
		set in the wordpress admin page.
	
*******************************************************************************/
	
	// Check if request is for data
	if( isset( $_POST['ajax'] ) ) {
	
	   // AJAX request. Place request parameters into variable.
		$data = $_POST['ajax'];
		
		// Category selected, return all matching posts.
		if( isset( $data['category'] ) ) {
			
			$catID = get_cat_ID( $data['category'] );
			
			$args = array( 
				'cat' => $catID,
				'paged' => $data['paged']
			);
			
			return_posts( $args, ucfirst( $data['category'] ) );
		}
		
		// Return most recent posts.
		if( isset( $data['recent'] ) ) {
			$args = array( 
				'post_type' => 'post',
				'paged' => $data['paged']
			);
			
			return_posts( $args, 'Recent' );
		}
		
		// Returns the posts from a specific month and year.
		if( isset( $data['date'] ) ) {
			$year = $data['date']['year'];
			$month = $data['date']['month'];
			$text = $data['date']['text'];
			
			$args = array( 
				'date_query' => array(
					'year' => $year,
					'month' => $month,
				),
				'paged' => $data['paged']
			);
			
			return_posts( $args, $text );
		}
		
		// Returns all posts that contain a search term.
		if( isset( $data['search'] ) ) {
			$args = array(
				'post_type' => 'post',
				's' => $data['search'],
				'paged' => $data['paged']
			);
			
			return_posts( $args, $data['search'] );
		}
	}
?>
