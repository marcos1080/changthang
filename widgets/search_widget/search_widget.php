<?php
	class Chang_Search_Widget extends WP_Widget {

		/**
		 * Sets up the widgets name etc
		 */
		public function __construct() {
			$widget_ops = array(
				'classname' => 'changsearch',
				'description' => 'Search widget for the Chang theme.',
			);
			parent::__construct( 'changsearch', 'Search', $widget_ops );

			add_action('admin_enqueue_scripts', array( $this, 'search_load_admin_scripts' ) );
			add_action('wp_enqueue_scripts', array( $this, 'search_load_scripts' ) );
		}

		/**
		 * Outputs the content of the widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			// outputs the content of the widget
			$title = apply_filters( 'widget_title', $instance['title'] );
			// before and after widget arguments are defined by themes
			echo $args['before_widget'];
			// This is where you run the code and display the output

			if( $instance['show-title'] ) {
				echo $args['before_title'].__( $title ).$args['after_title'];
			}

			// Height
			$height = 'height: '.$instance['height'].'px;';

			// Padding
			$padding = (int) $instance['padding'];
			$padding = 'height: calc( 100% - '.( $padding * 2 ).'px ); padding: '.$padding.'px;';

			if( $instance['border-layout'] == 'search' ) {
				$margin = (int) $instance['icon-gap'] - 4;
				$padding = $padding.' margin-left: '.$margin.'px;';
			}

			// Border
			$border_radius = (int) $instance['height'] * ( (int) $instance['border-radius'] / 100 );
			$border_style = 'border-width: '.$instance['border-width'].'px; border-color: '.$instance['border-color'].'; border-radius: '.$border_radius.'px;';
			switch( $instance['border-layout'] ) {
				case 'divider':
					$border_outside = $border_style.' border-style: solid;';
					$border_search = $border_style.' border-style: none; border-right-style: solid; border-radius: 0;';
					break;

				case 'outside':
					$border_outside = $border_style.' border-style: solid;';
					$border_search = 'border-style: none;';
					break;

				case 'search':
					$border_outside = 'border-style: none;';
					$border_search = $border_style.' border-style: solid;';
					break;
			}

			// Colours
			if( $instance['use-search-background'] ) {
				$search_field_style = 'background-color: '.$instance['search-background'].';';
			} else {
				$search_field_style = '';
			}

			if( $instance['use-icon-background'] ) {
				$icon_style = 'background-color: '.$instance['icon-background'].';';
			} else {
				$icon_style = '';
			}

			if( $instance['use-search-font'] ) {
				$search_field_style .= ' color: '.$instance['search-font'].';';
			}

			// Get icon
			$icon = get_template_directory_uri()."/widgets/search_widget/icons/search_black.png";
			if( $instance['show-icon'] ) {
				if( isset( $instance['icon'] ) ) {
					$icon = wp_get_attachment_image_url( $instance['icon'] );
				}

				if( isset( $instance['icon-hover'] ) && $instance['show-hover'] ) {
					$hover = wp_get_attachment_image_url( $instance['icon-hover'] );
				}
			}

			?>
				<form 	role="search" 
						method="get" 
						class="search-form" 
						action="<?php echo home_url( '/' ); ?>">
					<div class="chang-search-wrapper" style="<?php echo $border_outside.' '.$height; ?>">
						<input 	type="search" 
								class="search-field" 
								placeholder="Search …" 
								value="" 
								name="s" 
								style="<?php echo $border_search; echo $search_field_style; ?>" />
						<div class="chang-search-submit" style="<?php echo $icon_style.' '.$padding; ?>">
							<input type="image" class="search-submit" src="<?php echo $icon; ?>" alt="Submit" />
							<?php if( isset( $hover ) ) : ?>
								<input 	type="image" 
										class="search-submit-hover" 
										src="<?php echo $hover; ?>" 
										alt="Submit" 
										style="<?php echo $padding; ?>" />
							<?php endif; ?>
						</div>
					</div>
				</form>
			<?php

			echo $args['after_widget'];
		}

		/**
		 * Outputs the options form on admin
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			// outputs the options form on admin
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'title_link' => '' ) );

			// Set title
			if ( isset( $instance[ 'title' ] ) ) {
				$title = strip_tags( $instance[ 'title' ] );
			} else {
				$title = __( 'Social Media' );
			}
			$show_title = empty( $instance['show-title'] ) ? true : $instance['show-title'];

			?>
				<div class="chang-search-wrapper">
					<label 	for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
					<input 	class="widefat" 
							id="<?php echo $this->get_field_id( 'title' ); ?>" 
							name="<?php echo $this->get_field_name( 'title' ); ?>" 
							type="text" 
							value="<?php echo esc_attr( $title ); ?>" />
					<input 	id="<?php echo $this->get_field_id( 'show-title' ); ?>" 
							name="<?php echo $this->get_field_name( 'show-title' ); ?>" 
							type="checkbox"
							<?php checked( $show_title, 'on' ); ?> />
					<label  for="<?php echo $this->get_field_id( 'show-title' ); ?>"><?php _e( 'Show Title' ); ?></label>
				</div>
			<?php

			// Display settings.

			// Height
			$height = empty( $instance['height'] ) ? 48 : $instance['height'];

			?>
				<div class="hide-if-no-js">
					<div class="chang-search-range-text"><?php echo $height; ?>px</div>
					<label  for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height' ); ?></label>
					<input 	type="range" 
							class="widefat chang-search-range"
							name="<?php echo $this->get_field_name( 'height' ); ?>"
							min="20" 
							max="80" 
							value="<?php echo $height; ?>"
							disabled />
				</div>
				<div class="hide-if-js">
					<input 	type="number" 
							name="<?php echo $this->get_field_name( 'height' ); ?>"
							min="20" 
							max="80" 
							value="<?php echo $height; ?>" />
					<label  for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height' ); ?></label>
				</div>
			<?php

			// Icon Padding
			$padding = ! isset( $instance['padding'] ) ? 2 : $instance['padding'];

			?>
				<p><?php _e( 'Icon Settings' ); ?></p>
				<div class="hide-if-no-js">
					<div class="chang-search-range-text"><?php echo $padding; ?>px</div>
					<label  for="<?php echo $this->get_field_id( 'padding' ); ?>"><?php _e( 'Icon Padding' ); ?></label>
					<input 	type="range" 
							class="widefat chang-search-range"
							name="<?php echo $this->get_field_name( 'padding' ); ?>"
							min="0" 
							max="10" 
							value="<?php echo $padding; ?>" 
							disabled/>
				</div>
				<div class="hide-if-js">
					<input 	type="number" 
							name="<?php echo $this->get_field_name( 'padding' ); ?>"
							min="0" 
							max="10" 
							value="<?php echo $padding; ?>" />
					<label  for="<?php echo $this->get_field_id( 'padding' ); ?>"><?php _e( 'Icon Padding' ); ?></label>
				</div>
			<?php

			// Custom Icon
			$icon = empty( $instance['icon'] ) ? '' : $instance['icon'];
			$icon_hover = empty( $instance['icon-hover'] ) ? '' : $instance['icon-hover'];
			$show_hover = empty( $instance['show-hover'] ) ? false : $instance['show-hover'];
			$show_icon = empty( $instance['show-icon'] ) ? false : $instance['show-icon'];

			?>
				<div class="chang-search-custom-icon-wrapper">
					<p><?php _e( 'Custom Icon' ); ?><span class="hide-if-js" style="color: red;"><?php _e( ' Will only work if javascript enabled' ); ?></span></p>
					<div class="chang-search-icon chang-search-icon-main">
						<label for="<?php echo $this->get_field_id( 'icon' ); ?>"
							<?php if( ! $show_icon ) : ?>
								class="chang-search-icon-disabled"
							<?php endif; ?>
							><?php _e( 'Icon' ); ?>
							<span class="chang-search-error-label chang-search-error-label-icon<?php if( ! isset( $instance['error']['icon'] ) ) { echo ' hide-item'; }; ?>"><?php _e( ' Cannot be Empty!' ); ?></span>
						</label>
						<input 	type="hidden" 
								id="<?php echo $this->get_field_id( 'icon' ); ?>" 
								class="icon-id" 
								name="<?php echo $this->get_field_name( 'icon' ); ?>" 
								value="<?php echo $icon ?>" /><br 
								required />
						<img 	class='chang-search-icon-preview<?php if( ! $show_icon ) { echo ' chang-search-icon-disabled'; } ?>' 
								src='<?php echo ( $icon == "" ) ? get_template_directory_uri()."/widgets/search_widget/icons/placeholder.png" : wp_get_attachment_image_url( $icon ); ?>' 
								required />
						<a class="upload-chang-search button-secondary"<?php if( ! $show_icon ) { echo ' disabled="disabled"'; } ?>><?php _e( "Upload" ); ?></a>
					</div>
					<div class="chang-search-icon chang-search-icon-hover">
						<label 	for="<?php echo $this->get_field_id( 'icon-hover' ); ?>"
							<?php if( ! $show_hover || ! $show_icon ) : ?>
								class="chang-search-icon-disabled"
							<?php endif; ?>
							><?php _e( 'Hover Icon' ); ?>
						</label>
						<input 	type="hidden" 
								id="<?php echo $this->get_field_id( 'icon-hover' ); ?>" 
								class="icon-id" 
								name="<?php echo $this->get_field_name( 'icon-hover' ); ?>" 
								value="<?php echo $icon_hover ?>" /><br />
						<img 	class='chang-search-icon-preview<?php if( ! $show_hover || ! $show_icon ) { echo ' chang-search-icon-disabled'; } ?>' 
								src='<?php echo ( $icon_hover == "" ) ? get_template_directory_uri()."/widgets/search_widget/icons/placeholder.png" : wp_get_attachment_image_url( $icon_hover ); ?>' />
						<div>
							<a class="upload-chang-search button-secondary"<?php if( ! $show_hover || ! $show_icon ) { echo ' disabled="disabled"'; } ?>><?php _e( "Upload" ); ?></a>
							<a class="clear-chang-search button-secondary<?php if( $icon_hover == '' ) { echo ' hide-item'; } ?>"<?php if( ! $show_hover || ! $show_icon ) { echo ' disabled="disabled"'; } ?>><?php _e( "Clear" ); ?></a>
						</div>
					</div>
					<input 	class="widefat chang-search-show-icon" 
							id="<?php echo $this->get_field_id( 'show-icon' ); ?>" 
							name="<?php echo $this->get_field_name( 'show-icon' ); ?>" 
							type="checkbox" <?php checked( $show_icon, 'on' ); ?> />
					<label 	for="<?php echo $this->get_field_id( 'show-icon' ); ?>"><?php _e( 'Show custom icon' ); ?></label>
					<input 	class="widefat chang-search-show-hover"
							<?php if( ! $show_icon ) { echo ' disabled="disabled"'; } ?> 
							id="<?php echo $this->get_field_id( 'show-hover' ); ?>" 
							name="<?php echo $this->get_field_name( 'show-hover' ); ?>" 
							type="checkbox" <?php checked( $show_hover, 'on' ); ?> />
					<label 	class="chang-search-show-hover-label<?php if( ! $show_icon ) { echo " chang-search-icon-disabled"; } ?>"
							for="<?php echo $this->get_field_id( 'show-hover' ); ?>"><?php _e( 'Show hover icon' ); ?></label>
				</div>
			<?php

			// Border
			$border_width = ! isset( $instance['border-width'] ) ? 1 : $instance['border-width'];
			$border_radius = ! isset( $instance['border-radius'] ) ? 0 : $instance['border-radius'];
			$border_color = ! isset( $instance['border-color'] ) ? '#000000' : $instance['border-color'];
			$border_layout = ! isset( $instance['border-layout'] ) ? 'divider' : $instance['border-layout'];
			$icon_gap = ! isset( $instance['icon-gap'] ) ? 4 : $instance['icon-gap'];
			$icon_gap_style = 56 + $icon_gap;
			$icon_gap_style = 'width: calc( 100% - '.$icon_gap_style.'px );';

			?>
				<p><?php _e( 'Border Settings' ); ?></p>
				<div class="hide-if-no-js">
					<div class="chang-search-range-text hide-if-no-js"><?php echo $border_width; ?>px</div>
					<label  for="<?php echo $this->get_field_id( 'border-width' ); ?>"><?php _e( 'Border Width' ); ?></label>
					<input 	type="range" 
							class="widefat chang-search-range"
							name="<?php echo $this->get_field_name( 'border-width' ); ?>"
							min="0" 
							max="5" 
							value="<?php echo $border_width; ?>"
							disabled />
				</div>
				<div class="hide-if-js">
					<input 	type="number" 
							name="<?php echo $this->get_field_name( 'border-width' ); ?>"
							min="0" 
							max="5" 
							value="<?php echo $border_width; ?>" />
					<label  for="<?php echo $this->get_field_id( 'border-width' ); ?>"><?php _e( 'Border Width' ); ?></label>
				</div>
				<div class="hide-if-no-js">
					<div class="chang-search-range-text hide-if-no-js"><?php echo $border_radius; ?>%</div>
					<label  for="<?php echo $this->get_field_id( 'border-radius' ); ?>"><?php _e( 'Border Radius, % of height' ); ?></label>
					<input 	type="range" 
							class="widefat chang-search-range-percent"
							name="<?php echo $this->get_field_name( 'border-radius' ); ?>"
							min="0" 
							max="50" 
							value="<?php echo $border_radius; ?>"
							disabled />
				</div>
				<div class="hide-if-js">
					<input 	type="number" 
							name="<?php echo $this->get_field_name( 'border-radius' ); ?>"
							min="0" 
							max="50" 
							value="<?php echo $border_radius; ?>" />
					<label  for="<?php echo $this->get_field_id( 'border-radius' ); ?>"><?php _e( 'Border Radius, % of height' ); ?></label>
				</div>
				<label  for="<?php echo $this->get_field_id( 'border-color' ); ?>"
						style="padding-bottom: 8px;"><?php _e( 'Border Color' ); ?></label><br />
				<input 	type="text" 
						id="<?php echo $this->get_field_id( 'border-color' ); ?>" 
						class="chang-color-picker" 
						name="<?php echo $this->get_field_name( 'border-color' ); ?>" 
						value="<?php echo $border_color; ?>" /><br />
				<label id="chang-layout-label" 
					   for="<?php echo $this->get_field_id( 'border-layout' ); ?>"><?php _e( 'Border Layout' ); ?></label>
				<table id="chang-layout-example">
					<tr>
						<td>
							<input 	type="radio" 
									id="<?php echo $this->get_field_id( 'border-layout' ); ?>"
									name="<?php echo $this->get_field_name( 'border-layout' ); ?>"
									value="divider" 
									<?php if( $border_layout == 'divider' ) : ?>
										checked
									<?php endif; ?> />
						</td>
						<td>
							<small>Border with divider between icon</small>
							<div id="chang-layout-divider" class="chang-layout-wrapper">
								<div class="chang-layout-search"></div>
								<img class="chang-layout-icon"
									 src="<?php echo get_template_directory_uri()."/widgets/search_widget/icons/placeholder.png"; ?>"
									 alt="Placeholder Icon" />
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<input 	type="radio" 
									id="<?php echo $this->get_field_id( 'border-layout' ); ?>"
									name="<?php echo $this->get_field_name( 'border-layout' ); ?>"
									value="outside" 
									<?php if( $border_layout == 'outside' ) : ?>
										checked
									<?php endif; ?> />
						</td>
						<td>
							<small>Border with no divider</small>
							<div id="chang-layout-outside" class="chang-layout-wrapper">
								<div class="chang-layout-search"></div>
								<img class="chang-layout-icon"
									 src="<?php echo get_template_directory_uri()."/widgets/search_widget/icons/placeholder.png"; ?>"
									 alt="Placeholder Icon" />
							</div>
						</td>
					</tr>
					<tr>
						<td>
						<input 	type="radio" 
									id="<?php echo $this->get_field_id( 'border-layout' ); ?>"
									name="<?php echo $this->get_field_name( 'border-layout' ); ?>"
									value="search"
									<?php if( $border_layout == 'search' ) : ?>
										checked
									<?php endif; ?> />
						</td>
						<td>
							<small>Border around search field only</small>
							<div id="chang-layout-search" class="chang-layout-wrapper">
								<div class="chang-layout-search" style="<?php echo $icon_gap_style; ?>"></div>
								<img class="chang-layout-icon"
									 src="<?php echo get_template_directory_uri()."/widgets/search_widget/icons/placeholder.png"; ?>"
									 alt="Placeholder Icon" />
							</div>
							<div id="chang-layout-search-gap" class="hide-if-no-js">
								<small class="chang-search-range-text hide-if-no-js"><?php echo $icon_gap; ?>px</small>
								<label  for="<?php echo $this->get_field_id( 'icon-gap' ); ?>"><small><?php _e( 'Search Field/Icon Gap' ); ?></small></label>
								<input 	type="range" 
										class="widefat chang-search-range-icon-gap"
										name="<?php echo $this->get_field_name( 'icon-gap' ); ?>"
										min="0" 
										max="20" 
										value="<?php echo $icon_gap; ?>"
										disabled />
							</div>
							<div class="hide-if-js">
								<label  for="<?php echo $this->get_field_id( 'icon-gap' ); ?>"><small><?php _e( 'Search Field/Icon Gap' ); ?></small></label>
								<input 	type="number" 
										name="<?php echo $this->get_field_name( 'icon-gap' ); ?>"
										min="0" 
										max="20" 
										value="<?php echo $icon_gap; ?>" />
							</div>
						</td>
					</tr>
				</table>
			<?php

			// Colours

			$search_color = ! isset( $instance['search-background'] ) ? '#ffffff' : $instance['search-background'];
			$use_search_background = empty( $instance['use-search-background'] ) ? false : $instance['use-search-background'];
			$search_font_color = ! isset( $instance['search-font'] ) ? '#000000' : $instance['search-font'];
			$use_search_font = empty( $instance['use-search-font'] ) ? false : $instance['use-search-font'];
			$icon_color = ! isset( $instance['icon-background'] ) ? '#ffffff' : $instance['icon-background'];
			$use_icon_background = empty( $instance['use-icon-background'] ) ? false : $instance['use-icon-background'];

			?>
				<p><?php _e( 'Color Settings' ); ?></p>
				<div class="chang-search-wrapper">
					<input 	class="widefat"
							id="<?php echo $this->get_field_id( 'use-search-background' ); ?>" 
							name="<?php echo $this->get_field_name( 'use-search-background' ); ?>" 
							type="checkbox" <?php checked( $use_search_background, 'on' ); ?> />
					<label 	for="<?php echo $this->get_field_id( 'use-search-background' ); ?>"><?php _e( 'Use custom color for search field' ); ?></label>
					<input 	type="text" 
							id="<?php echo $this->get_field_id( 'search-background' ); ?>" 
							class="chang-color-picker" 
							name="<?php echo $this->get_field_name( 'search-background' ); ?>" 
							value="<?php echo $search_color; ?>" /><br />
				</div>
				<div class="chang-search-wrapper">
					<input 	class="widefat"
							id="<?php echo $this->get_field_id( 'use-search-font' ); ?>" 
							name="<?php echo $this->get_field_name( 'use-search-font' ); ?>" 
							type="checkbox" <?php checked( $use_search_font, 'on' ); ?> />
					<label 	for="<?php echo $this->get_field_id( 'use-search-font' ); ?>"><?php _e( 'Use custom color for search field text' ); ?></label>
					<input 	type="text" 
							id="<?php echo $this->get_field_id( 'search-font' ); ?>" 
							class="chang-color-picker" 
							name="<?php echo $this->get_field_name( 'search-font' ); ?>" 
							value="<?php echo $search_font_color; ?>" /><br />
				</div>
				<div class="chang-search-wrapper">
					<input 	class="widefat"
							id="<?php echo $this->get_field_id( 'use-icon-background' ); ?>" 
							name="<?php echo $this->get_field_name( 'use-icon-background' ); ?>" 
							type="checkbox" <?php checked( $use_icon_background, 'on' ); ?> />
					<label 	for="<?php echo $this->get_field_id( 'use-icon-background' ); ?>"><?php _e( 'Use custom color for icon background' ); ?></label>
					<input 	type="text" 
							id="<?php echo $this->get_field_id( 'icon-background' ); ?>" 
							class="chang-color-picker" 
							name="<?php echo $this->get_field_name( 'icon-background' ); ?>" 
							value="<?php echo $icon_color; ?>" /><br />
				</div>
			<?php

			// Placeholder icon. Used by jQuery to create new empty item.
			?>
				<img 	src="<?php echo get_template_directory_uri()."/widgets/social_media_widget/icons/placeholder.png"; ?>" 
						style="display: none;" 
						id="chang-search-placeholder-icon" 
						alt="Placeholder Icon for Chang Search Widget" />
			<?php
		}

		/**
		 * Processing widget options on save
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			// processes widget options to be saved
			$instance['title'] = strip_tags( $new_instance['title'] );

			// Show the title
			if( isset( $new_instance['show-title'] ) ) {
				$instance['show-title'] = strip_tags( $new_instance['show-title'] );
			} else {
				$instance['show-title'] = false;
			}

			// Display

			// Height
			$instance['height'] = strip_tags( $new_instance['height'] );

			// Padding
			$instance['padding'] = strip_tags( $new_instance['padding'] );

			// Icon
			$instance['icon'] = strip_tags( $new_instance['icon'] );
			$instance['icon-hover'] = strip_tags( $new_instance['icon-hover'] );

			if( isset( $new_instance['show-hover'] ) ) {
				$instance['show-hover'] = strip_tags( $new_instance['show-hover'] );
			} else {
				$instance['show-hover'] = false;
			}

			if( isset( $new_instance['show-icon'] ) ) {
				$instance['show-icon'] = strip_tags( $new_instance['show-icon'] );
			} else {
				$instance['show-icon'] = false;
			}

			// Border
			$instance['border-width'] = strip_tags( $new_instance['border-width'] );
			$instance['border-radius'] = strip_tags( $new_instance['border-radius'] );
			$instance['border-color'] = strip_tags( $new_instance['border-color'] );
			$instance['border-layout'] = strip_tags( $new_instance['border-layout'] );
			$instance['icon-gap'] = strip_tags( $new_instance['icon-gap'] );

			// Colours
			$instance['search-background'] = strip_tags( $new_instance['search-background'] );
			if( isset( $new_instance['use-search-background'] ) ) {
				$instance['use-search-background'] = strip_tags( $new_instance['use-search-background'] );
			} else {
				$instance['use-search-background'] = false;
			}

			$instance['search-font'] = strip_tags( $new_instance['search-font'] );
			if( isset( $new_instance['use-search-font'] ) ) {
				$instance['use-search-font'] = strip_tags( $new_instance['use-search-font'] );
			} else {
				$instance['use-search-font'] = false;
			}

			$instance['icon-background'] = strip_tags( $new_instance['icon-background'] );
			if( isset( $new_instance['use-icon-background'] ) ) {
				$instance['use-icon-background'] = strip_tags( $new_instance['use-icon-background'] );
			} else {
				$instance['use-icon-background'] = false;
			}

			return $instance;
		}

		public function search_load_admin_scripts($hook) {
			if( $hook != 'widgets.php' )
				return;

			if ( !isset($_GET['editwidget-nojs'])) {
				wp_enqueue_script( 'widget-search-js', get_template_directory_uri().'/widgets/search_widget/js/widget-search.js');
			}

			wp_enqueue_style( 'wp-color-picker' );        
			wp_enqueue_script( 'wp-color-picker' ); 
			wp_enqueue_style( 'widget-search-css', get_template_directory_uri().'/widgets/search_widget/css/widget-search-admin.css');
		}

		public function search_load_scripts() {
			wp_enqueue_style( 'widget-search-css', get_template_directory_uri().'/widgets/search_widget/css/widget-search.css');
		}
	}

	// register social media icons widget
	function register_search_widget() {
		unregister_widget( 'WP_Widget_Search' );
		register_widget( 'Chang_Search_Widget' );
	}

	add_action( 'widgets_init', 'register_search_widget' );
?>
