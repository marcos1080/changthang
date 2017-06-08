<?php
	// Recursively display each child category
	function display_menu_category_list( $category_id, $menu_level ) {
		$categories = get_categories( 'parent='.$category_id );
		if ( $categories ) : ?>
							<ul id="cat-<?php echo $category_id; ?>" class="children">
								<?php	foreach( $categories as $category ) : ?>
								<li class="menu-level-<?php echo $menu_level; ?>">
									<a href="<?php echo get_category_link( $category->cat_ID ); ?>"><?php echo $category->name; ?></a>
									<?php display_menu_category_list( $category->cat_ID, $menu_level + 1 ); ?>
								</li>
								<?php	endforeach;	?>
							</ul>
<?php	endif;
	}
	
	// Get menu items for the primary menu
	if ( has_nav_menu( 'primary' ) ) {
		$menu_name = 'primary';
		$locations = get_nav_menu_locations();
		$menu_id = $locations[ $menu_name ] ;
		$items = wp_get_nav_menu_items( $menu_id );
	}
?>
				<div id="menu" class="no-js">
					<?php if ( has_nav_menu( 'primary' ) ) : ?>
						<ul id="primary-menu">
						<?php foreach( $items as $item ) : ?>
							<li class="menu-level-1">
								<a href='<?php echo $item->url; ?>'><h2><?php echo $item->title; ?></h2></a>
								<?php	if ( $item->object == 'category' ) {
											$menu_level = 2;
											display_menu_category_list( $item->object_id, $menu_level );
										} ?>
							</li>
						<?php	endforeach;	?>
						</ul>
					<?php endif; ?>
					
					<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
						<div id="widget-area" class="widget-area" >
							<?php dynamic_sidebar( 'sidebar-1' ); ?>
						</div><!-- .widget-area -->
					<?php endif; ?>
				</div>
