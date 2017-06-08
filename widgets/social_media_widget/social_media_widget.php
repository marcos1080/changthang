<?php
	class SocialMedia_Widget extends WP_Widget {

		/**
		 * Sets up the widgets name etc
		 */
		public function __construct() {
			$widget_ops = array(
				'classname' => 'changsocialmedia',
				'description' => 'Add social media icons to widget areas.',
			);
			parent::__construct( 'changsocialmedia', 'Social Media Icons', $widget_ops );

			add_action('admin_enqueue_scripts', array( $this, 'socialmedia_load_admin_scripts' ) );
			add_action('wp_enqueue_scripts', array( $this, 'socialmedia_load_scripts' ) );
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

			$size = $instance['size'];
			$gap = $instance['gap'];
			$align = $instance['align'];
			if( $align == 'left' ) {
				$align_string = 'text-align: left;';
				$margin_string = "margin-right: {$gap}px; margin-bottom: {$gap}px;";
			} elseif ( $align == 'center' ) {
				// Deals with the annoyying 4 px whitespace node that stops margin collapsing.
				$gap_sides = $gap / 2 - 2;
				$align_string = 'text-align: center;';
				$margin_string = "margin-left: {$gap_sides}px; margin-right: {$gap_sides}px; margin-bottom: {$gap}px;";
			} else {
				$align_string = 'text-align: right;';
				$gap_left = (int) $gap - 4;
				$margin_string = "margin-left: {$gap_left}px; margin-bottom: {$gap}px;";
			}

			?>
				<ul id="social-media-icons-menu" style="max-height: <?php echo $size; ?>px; <?php echo $align_string; ?>">
					<?php 
						if( isset( $instance['item-order'] ) ) {
							foreach( $instance['item-order'] as $key => $id ) {
								if( $instance['show-icon-'.$id] ) : ?>
									<li id="social-media-icon-<?php echo $key; ?>" 
										class="social-media-icon-wrapper"
										style="<?php echo $margin_string; ?>">
										<a 	href="<?php echo $instance['url-'.$id]; ?>" 
											class="social-media-icon" 
											style="max-height: <?php echo $size; ?>px; max-width: <?php echo $size; ?>px;">
											<img 	src="<?php echo wp_get_attachment_image_url( $instance['icon-'.$id] ); ?>" 
													alt="Icon for <?php echo $instance['name-'.$id]; ?>" 
													style="max-height: <?php echo $size; ?>px; max-width: <?php echo $size; ?>px;"/>
											<?php if ( $instance['show-hover-'.$id] && $instance['hover-'.$id] != '' ) : ?>
												<img 	class="hover" 
														src="<?php echo wp_get_attachment_image_url( $instance['hover-'.$id] ); ?>" 
														alt="Hover icon for <?php echo $instance['name-'.$id]; ?>" 
														style="max-height: <?php echo $size; ?>px; max-width: <?php echo $size; ?>px;"/>
											<?php endif; ?>
										</a>
									</li>
								<?php
								endif;
							}
						}
					?>
				</ul>
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

			// Show error is set.
			if( isset( $instance['error'] ) ) {
				?>
					<div class="error">
						<h3><?php _e( 'An error has been encountered on the Social Media widget' ); ?></h3>
						<p><?php _e( 'Items that are in error will show up as red in the widget. Please double check the values entered into the fields.'); ?></p>
					</div>
				<?php
			}

			// Set title
			if ( isset( $instance[ 'title' ] ) ) {
				$title = strip_tags( $instance[ 'title' ] );
			} else {
				$title = __( 'Social Media' );
			}
			$show_title = empty( $instance['show-title'] ) ? true : $instance['show-title'];

			?>
				<p>
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
				</p>
			<?php

			// Set initial item order. Copyright then item 1.
			if ( ! isset( $instance[ 'item-order' ] ) ) {
				$instance[ 'item-order' ] = [0];
			}


			$amount = count( $instance[ 'item-order' ] );

			wp_enqueue_media();
			$show_add_button = true;

			?>
			<p>Icon Items:</p>
			<div class="social-media-sortable-list">
				<?php
				foreach( $instance['item-order'] as $index => $value ) :
					$is_open = empty( $instance['open-'.$value] ) ? 'false' : $instance['open-'.$value];
					$icon = empty( $instance['icon-'.$value] ) ? '' : $instance['icon-'.$value];
					$hover = empty( $instance['hover-'.$value] ) ? '' : $instance['hover-'.$value];
					$show_hover = empty( $instance['show-hover-'.$value] ) ? false : $instance['show-hover-'.$value];
					$name = empty( $instance['name-'.$value] ) ? '' : $instance['name-'.$value];
					$item_title = $name == '' ? 'Unnamed Icon' : $name;
					$url = empty( $instance['url-'.$value] ) ? '' : $instance['url-'.$value];
					$show_icon = empty( $instance['show-icon-'.$value] ) ? true : $instance['show-icon-'.$value];

					$valid = true;
					if( $icon == '' || $name == '' || isset( $instance['error'][$value]['url'] ) ) {
						$valid = false;
						$show_add_button = false;
					}

					?>
					<div 	id="social-media-item-<?php echo $value; ?>" 
							class="social-media-list-item<?php if( isset( $instance['error'][$value] ) ) { echo ' social-media-error'; } if( ! $valid ) { echo ' social-media-invalid'; } ?>">
						<h5 class="social-media-list-item-title"><span class="item-title"><?php _e( $item_title ); ?></span><a class="social-media-action hide-if-no-js"></a></h5>
						<div class="social-media-list-item-fields">
							<div class="social-media-icon-wrapper">
								<div class="social-media-icon social-media-icon-main">
									<label for="<?php echo $this->get_field_id( 'icon-'.$value ); ?>"><?php _e( 'Icon' ); ?>
										<span class="social-media-error-label social-media-error-label-icon<?php if( ! isset( $instance['error'][$value]['icon'] ) ) { echo ' hide-item'; }; ?>"><?php _e( ' Cannot be Empty!' ); ?></span>
									</label>
									<input 	type="hidden" 
											id="<?php echo $this->get_field_id( 'icon-'.$value ); ?>" 
											class="icon-id" 
											name="<?php echo $this->get_field_name( 'icon-'.$value ); ?>" 
											value="<?php echo $icon ?>" /><br 
											required />
									<img 	class='social-media-icon-preview' 
											src='<?php echo ( $icon == "" ) ? get_template_directory_uri()."/widgets/social_media_widget/icons/placeholder.png" : wp_get_attachment_image_url( $icon ); ?>' 
											required />
									<a class="upload-social-media button-secondary"><?php _e( "Upload" ); ?></a>
								</div>
								<div class="social-media-icon social-media-icon-hover">
									<label 	for="<?php echo $this->get_field_id( 'hover-'.$value ); ?>"
										<?php if( ! $show_hover ) : ?>
											class="social-media-icon-disabled"
										<?php endif; ?>
										><?php _e( 'Hover Icon' ); ?>
									</label>
									<input 	type="hidden" 
											id="<?php echo $this->get_field_id( 'hover-'.$value ); ?>" 
											class="icon-id" 
											name="<?php echo $this->get_field_name( 'hover-'.$value ); ?>" 
											value="<?php echo $hover ?>" /><br />
									<img 	class='social-media-icon-preview<?php if( ! $show_hover ) { echo ' social-media-icon-disabled'; } ?>' 
											src='<?php echo ( $hover == "" ) ? get_template_directory_uri()."/widgets/social_media_widget/icons/placeholder.png" : wp_get_attachment_image_url( $hover ); ?>' />
									<div>
										<a class="upload-social-media button-secondary"<?php if( ! $show_hover ) { echo ' disabled="disabled"'; } ?>><?php _e( "Upload" ); ?></a>
										<a class="clear-social-media button-secondary"<?php if( ! $show_hover ) { echo ' disabled="disabled"'; } ?>><?php _e( "Clear" ); ?></a>
									</div>
								</div>
								<input 	class="widefat social-media-show-hover" 
											id="<?php echo $this->get_field_id( 'show-hover-'.$value ); ?>" 
											name="<?php echo $this->get_field_name( 'show-hover-'.$value ); ?>" 
											type="checkbox" <?php checked( $show_hover, 'on' ); ?> />
								<label for="<?php echo $this->get_field_id( 'show-hover-'.$value ); ?>"><?php _e( 'Show hover icon' ); ?></label>
							</div>
							<label for="<?php echo $this->get_field_id( 'name-'.$value ); ?>"><?php _e( 'Icon Name' ); ?></label>
							<input 	class="widefat social-media-name <?php if( isset( $instance['error'][$value]['name'] ) ) { echo 'social-media-error-input'; } ?>" 
									id="<?php echo $this->get_field_id( 'name-'.$value ); ?>" 
									name="<?php echo $this->get_field_name( 'name-'.$value ); ?>" 
									type="text" 
									value="<?php echo esc_attr( $name ); ?>" 
									<?php if( isset( $instance['error'][$value]['name'] ) ) : ?>
										placeholder="<?php echo $instance['error'][$value]['name']; ?>"
									<?php endif; ?> 
									required />
							<label for="<?php echo $this->get_field_id( 'url-'.$value ); ?>"><?php _e( 'Url' ); ?>
								<span class="social-media-error-label social-media-error-label-url<?php if( ! isset( $instance['error'][$value]['url']['invalid'] ) ) { echo ' hide-item'; }; ?>"><?php _e( ' Invalid!' ); ?></span>
							</label>
							<input 	class="widefat social-media-url <?php if( isset( $instance['error'][$value]['url'] ) ) { echo 'social-media-error-input'; } ?>" 
									id="<?php echo $this->get_field_id( 'url-'.$value ); ?>" 
									name="<?php echo $this->get_field_name( 'url-'.$value ); ?>" 
									type="text" 
									value="<?php echo esc_attr( $url ); ?>" 
									<?php if( isset( $instance['error'][$value]['url']['placeholder'] ) ) : ?>
										placeholder="<?php echo $instance['error'][$value]['url']['placeholder']; ?>"
									<?php endif; ?>
									required />
							<input 	class="widefat social-media-show-icon-checkbox" 
									id="<?php echo $this->get_field_id( 'show-item-'.$value ); ?>" 
									name="<?php echo $this->get_field_name( 'show-item-'.$value ); ?>" 
									type="checkbox" <?php checked( $show_icon, 'on' ); ?> />
							<label for="<?php echo $this->get_field_id( 'show-item-'.$value ); ?>"><?php _e( 'Show this icon' ); ?></label>
							<a class="social-media-remove hide-if-no-js <?php if( $amount == 1 ) { echo 'hide-item'; } ?>"><p><?php echo __( "Remove" ); ?></p></a>
							<input 	class="social-media-open" 
									id="<?php echo $this->get_field_id( 'open-'.$value ); ?>" 
									name="<?php echo $this->get_field_name( 'open-'.$value ); ?>" 
									type="hidden" 
									value="<?php echo $is_open; ?>" />
						</div>
					</div>
					<?php
				endforeach;
				?>
			</div>
			<?php

			// Show add button
			?>
				<div class="add-button-row hide-if-no-js <?php if( ! $show_add_button ) { echo ' hide-item'; } ?>">
					<a class="add-social-media button-secondary"><?php _e( "Add Icon" ); ?></a>
				</div>
			<?php

			// Display settings
			$icon_size = empty( $instance['size'] ) ? '48' : $instance['size'];
			$icon_gap = empty( $instance['gap'] ) ? '48' : $instance['gap'];
			if ( isset( $instance[ 'align' ] ) ) {
				$align = strip_tags( $instance[ 'align' ] );
			} else {
				$align = 'left';
			}

			?>
				<p>Display Layout Settings:</p>
				<div>
					<div class="social-media-range-text"><?php echo $icon_size; ?>px</div>
					<label 	for="<?php echo $this->get_field_id('size'); ?>">Icon size</label>
					<input 	class="widefat social-media-range" 
							id="<?php echo $this->get_field_id('size'); ?>" 
							name="<?php echo $this->get_field_name('size'); ?>" 
							type="range" 
							min="20" 
							max="100" 
							step="1" 
							value="<?php echo $icon_size; ?>" />
				</div>
				<div>
					<div class="social-media-range-text"><?php echo $icon_gap; ?>px</div>
					<label 	for="<?php echo $this->get_field_id('gap'); ?>">Space between icons</label>
					<input 	class="widefat social-media-range" 
							id="<?php echo $this->get_field_id('gap'); ?>" 
							name="<?php echo $this->get_field_name('gap'); ?>"
							type="range" 
							min="0" 
							max="40" 
							step="1" 
							value="<?php echo $icon_gap; ?>" />
				</div>
				<label 	for="<?php echo $this->get_field_id('align'); ?>" 
						style="padding-bottom: 20px;">Icon Alignment</label>
				<select id="<?php echo $this->get_field_id('align'); ?>" 
						name="<?php echo $this->get_field_name('align'); ?>" 
						style="margin-bottom: 20px;">
					<option value='left'<?php if( $align == 'left' ) { echo ' selected'; } ?>><?php _e( 'Left' ); ?></option>
					<option value='center'<?php if( $align == 'center' ) { echo ' selected'; } ?>><?php _e( 'Center' ); ?></option>
					<option value='right'<?php if( $align == 'right' ) { echo ' selected'; } ?>><?php _e( 'Right' ); ?></option>
				</select>
			<?php

			// Reordering, deleteion and adding on a non JS browser.
			if ( isset( $_GET['editwidget'] ) && $_GET['editwidget'] ) : ?>
				<table class='widefat'>
					<thead><tr><th><?php _e("Item"); ?></th><th><?php _e("Position/Action"); ?></th></tr></thead>
					<tbody>
						<?php foreach( $instance['item-order'] as $index => $value ) : ?>
							<tr>
								<td><?php _e( 'Item '.$value ); ?></td>
								<td>
									<select id="<?php echo $this->get_field_id('position-'.$value); ?>" name="<?php echo $this->get_field_name('position-'.$value); ?>">
										<option><?php _e('&mdash; Select &mdash;'); ?></option>
										<?php for( $i = 1; $i <= $amount; $i++ ) {
											if ( $i == $index + 1 ) {
												echo "<option value='$i' selected>$i</option>";
											} else {
												echo "<option value='$i'>$i</option>";
											}
										} ?>
										<?php 
											// Only show delete option if not the copyright item or only one user added item present.
											if( $amount > 1 ) : 
										?>
												<option value="-1"><?php _e('&mdash; Delete &mdash;'); ?></option>
										<?php 
											endif; 
										?>
									</select>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<div class="sllw-row">
					<input 	type="checkbox" 
							name="<?php echo $this->get_field_name('new-item'); ?>" 
							id="<?php echo $this->get_field_id('new-item'); ?>" /> <label for="<?php echo $this->get_field_id('new-item'); ?>"><?php  _e("Add New Item"); ?></label>
				</div>
			<?php endif; ?>

				<input 	type="hidden" 
						id="<?php echo $this->get_field_id('total'); ?>" 
						class="item-total" 
						name="<?php echo $this->get_field_name('total'); ?>" 
						value="<?php echo $amount ?>" />
				<input 	type="hidden" 
						id="<?php echo $this->get_field_id('item-order'); ?>" 
						class="item-order" 
						name="<?php echo $this->get_field_name('item-order'); ?>" 
						value="<?php echo implode( ',', $instance[ 'item-order' ] ); ?>" />
			<?php

			// Placeholder icon. Used by jQuery to create new empty item.
			?>
				<img 	src="<?php echo get_template_directory_uri()."/widgets/social_media_widget/icons/placeholder.png"; ?>" 
						style="display: none;" 
						id="social-media-placeholder-icon" 
						alt="Placeholder Icon for Social Media Widget" />
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
			$instance['title'] = strip_tags($new_instance['title']);
			if( strip_tags( $new_instance['show-title'] ) ) {
				$instance['show-title'] = $new_instance['show-title'];
			} else {
				$instance['show-title'] = false;
			}

			$total = $new_instance['total'];
			// Used to create new item.
			$new_item = empty( $new_instance['new-item'] ) ? false : strip_tags( $new_instance['new-item'] );

			// Presence of position-0 indcates the non javascript form
			// The id at the end of the name is the item type. 0 is copyright and the rest are user added items.
			if( isset( $new_instance['position-0'] ) ) {
				// Non JS
				for( $i = 0; $i < $new_instance['total']; $i++ ) {
					// -1 indicates the item is to be deleted, so ignore.
					if( $new_instance['position-'.$i] != -1 ) {
						// Keep
						$position[$i] = $new_instance['position-'.$i];
					} else {
						// Ignore.
						$total --;
					}
				}

				if( $position ) {
					// Orders the position array by value. 
					// If there are double ups it doesn't matter, array will still have an order. Will use the ordered keys to save fields.
					asort( $position );
					$order = array_keys( $position );

					// If the new item checkbox was checked then add new item id using the total as the key.
					if( strip_tags( $new_instance['new-item'] ) ) {
						// Adding to the end will show it last.
						array_push( $order, $total );
					}
				}

			} else {
				// JS
				$order = explode( ',', $new_instance['item-order'] );
				foreach( $order as $key => $value ) {
					$id = (int ) end( explode( '-', $value ) );
					$order[$key] = $id;
				}
			}

			if( $order ) {
				foreach ( $order as $i => $id ) {
					// Current open state for item, prevents closing upon ajax refresh.
					$instance['open-'.( $id )] = empty( $new_instance['open-'.$id]) ? 'false' : strip_tags( $new_instance['open-'.$id] );

					// Validate fields.
					$icon = strip_tags( trim( $new_instance['icon-'.$id] ) );
					if( $icon == '' ) {
						$instance['error'][$id]['icon'] = __( ' Cannot be Empty!' );
					}

					$name = strip_tags( trim( $new_instance['name-'.$id] ) );
					if( $name == '' ) {
						$instance['error'][$id]['name'] = __( 'Cannot leave field empty' );
					}

					$url = strip_tags( trim( $new_instance['url-'.$id] ) );

					if ( filter_var( $url, FILTER_VALIDATE_URL ) === false && ! empty( $url ) ) {
					    $instance['error'][$id]['url']['invalid'] = __( 'Invalid' );
					} 

					if ( $url == '' ) {
						$instance['error'][$id]['url']['placeholder'] = __( 'Cannot leave field empty' );
					}

					// Save fields
					if( strip_tags( $new_instance['show-hover-'.$id] ) ) {
						$instance['show-hover-'.$id] = $new_instance['show-hover-'.$id];
					} else {
						$instance['show-hover-'.$id] = false;
					}

					$instance['icon-'.$id] = $icon;
					$instance['hover-'.$id] = strip_tags( trim( $new_instance['hover-'.$id] ) );
					$instance['name-'.$id] = $name;
					$instance['url-'.$id] = $url;

					if( strip_tags( $new_instance['show-item-'.$id] ) ) {
						$instance['show-icon-'.$id] = $new_instance['show-item-'.$id];
					} else {
						$instance['show-icon-'.$id] = false;
					}
				}
			}

			$instance['size'] = strip_tags( trim( $new_instance['size'] ) );
			$instance['gap'] = strip_tags( trim( $new_instance['gap'] ) );
			$instance['align'] = strip_tags( trim( $new_instance['align'] ) );
			$instance['item-order'] = $order;
			$instance['total'] = $total;

			return $instance;
		}

		public function socialmedia_load_admin_scripts($hook) {
			if( $hook != 'widgets.php' )
				return;

			if ( !isset($_GET['editwidget-nojs'])) {
				wp_enqueue_script( 'widget-socialmedia-js', get_template_directory_uri().'/widgets/social_media_widget/js/widget-socialmedia.js');
			}

			wp_enqueue_style( 'widget-socialmedia-css', get_template_directory_uri().'/widgets/social_media_widget/css/widget-socialmedia-admin.css');
		}

		public function socialmedia_load_scripts() {
			wp_enqueue_style( 'widget-socialmedia-css', get_template_directory_uri().'/widgets/social_media_widget/css/widget-socialmedia.css');
		}
	}

	// register social media icons widget
	function register_socialmedia_widget() {
		 register_widget( 'SocialMedia_Widget' );
	}

	add_action( 'widgets_init', 'register_socialmedia_widget' );
?>
