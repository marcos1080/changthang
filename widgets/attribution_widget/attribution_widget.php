<?php
	class Attribution_Widget extends WP_Widget {

		/**
		 * Sets up the widgets name etc
		 */
		public function __construct() {
			$widget_ops = array(
				'classname' => 'attribution',
				'description' => 'Add attribution fields to widget areas.',
			);
			parent::__construct( 'attribution', 'Attribution Fields', $widget_ops );

			add_action('admin_enqueue_scripts', array($this,'attribution_load_scripts'));
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

			if ( isset( $instance[ 'copyright-name' ] ) && $instance[ 'copyright-name' ] != '' ) {
				$copyright = $instance[ 'copyright-name' ];
			}

			if ( isset( $instance[ 'copyright-url' ] ) && $instance[ 'copyright-url' ] != '' && empty( $instance['error'][0]['url'] ) ) {
				$copyright_url = $instance[ 'copyright-url' ];
			}

			$show_copyright = false;
			if ( isset( $instance[ 'show-copyright' ] ) && $instance[ 'show-copyright' ] != '' ) {
				$show_copyright = $instance[ 'show-copyright' ];
			}

			$show_wordpress = false;
			if ( isset( $instance[ 'show-wordpress' ] ) && $instance[ 'show-wordpress' ] != '' ) {
				$show_wordpress = $instance[ 'show-wordpress' ];
			}

			if( $instance['show-title'] ) {
				echo $args['before_title'].__( $title ).$args['after_title'];
			}

			?>
				<ul>
					<?php 
						if( isset( $instance['item-order'] ) ) {
							foreach( $instance['item-order'] as $key => $id ) {
								if( $id == 0 ) {
									// Case is copyright item
									if ( $copyright && $show_copyright ) {
										$date = date('Y');
										if ( isset( $copyright_url ) ) {
											echo "<li>&copy$date <a href='$copyright_url'>$copyright</a></li>\n";
										} else {
											echo "<li>&copy $date $copyright</li>\n";
										}
									}
								} else {
									// Other items
									if( $instance['display-'.$id] != '' && $instance['show-item-'.$id] ) {
										if( $instance['url-'.$id] != '' && empty( $instance['error'][$id]['url'] ) ) {
											if( $instance['link-'.$id] != '' && empty( $instance['error'][$id]['link'] ) ) {
												// Valid link part found, replace link part with href in display text.
												$link = "<a href='{$instance['url-'.$id]}'>{$instance['link-'.$id]}</a>";
												$display_text = $instance['display-'.$id];
												$display_text = str_replace( $instance['link-'.$id], $link, $display_text );
												echo "<li>{$display_text}</li>\n";
											} else{
												// Valid url found but no or invalid link part.
												echo "<li><a href='{$instance['url-'.$id]}'>{$instance['display-'.$id]}</a></li>\n";
											}
										} else {
											// No or invalid url found.
											echo "<li>{$instance['display-'.$id]}</li>\n";
										}
									}
								}
							}
						}

						if ( $show_wordpress ) {
							echo "<li>".__( 'Powered By' )." <a href='http://wordpress.org'>Wordpress</a></li>\n";
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
						<h3><?php _e( 'An error has been encountered on the Attribution Fields widget' ); ?></h3>
						<p><?php _e( 'Items that are in error will show up as red in the widget. Please double check the values entered into the fields.'); ?></p>
					</div>
				<?php
			}

			// Set title
			if ( isset( $instance[ 'title' ] ) ) {
				$title = strip_tags( $instance[ 'title' ] );
			} else {
				$title = __( 'Attribution' );
			}

			$show_title = empty( $instance['show-title'] ) ? true : $instance['show-title'];

			?>
				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
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

			// Copyright fields
			if ( ! isset( $instance[ 'copyright-name' ] ) ) {
				$instance[ 'copyright-name' ] = '';
			}

			if ( ! isset( $instance[ 'copyright-url' ] ) ) {
				$instance[ 'copyright-url' ] = '';
			}

			if ( ! isset( $instance[ 'show-copyright' ] ) ) {
				$instance[ 'show-copyright' ] = true;
			}

			// Set initial item order. Copyright then item 1.
			if ( ! isset( $instance[ 'item-order' ] ) ) {
				$instance[ 'item-order' ] = [0,1];
			}


			$amount = count( $instance[ 'item-order' ] );
			$show_add_button = true;

			?>
			<div class="attribution-description">
				<small>	Items with no display text will not show up on screen.<br />
						The "Link Part" and "Url" fields are optional. The link part is used to create a clickable link on a small matching part of the display text.<br />
						If there is no "Link Part" supplied or does not match up to any part of the display text then the whole line of display text becomes the link.
				</small>
			</div>
			<div class="attribution-sortable-list">
				<?php
				foreach( $instance['item-order'] as $index => $value ) :
					$is_open = empty( $instance['open-'.$value] ) ? 'false' : $instance['open-'.$value];

					if( $value == 0 ) :
						// Case is copyright item
						?>
						<div 	id="attribution-copyright" 
								class="attribution-list-item <?php if( isset( $instance['error'][$value] ) ) { echo 'attribution-error'; } ?>">
							<h5 class="list-item-title"><span class="item-title"><?php _e( 'Copyright' ); ?></span><a class="attribution-action hide-if-no-js"></a></h5>
							<div class="list-item-fields">
								<p style="margin-top: 0px;">
									<label for="<?php echo $this->get_field_id( 'copyright-name' ); ?>"><?php _e( 'Name of copyright holder' ); ?></label>
									<input 	class="widefat <?php if( isset( $instance['error'][$value]['name'] ) ) { echo 'attribution-error-input'; } ?>" 
											id="<?php echo $this->get_field_id( 'copyright-name' ); ?>" 
											name="<?php echo $this->get_field_name( 'copyright-name' ); ?>" 
											type="text" 
											<?php if( isset( $instance['error'][$value]['name'] ) ) : ?>
												placeholder="<?php echo $instance['error'][$value]['name']; ?>"
											<?php endif; ?>
											value="<?php echo esc_attr( $instance[ 'copyright-name' ] ); ?>" />
								</p>
								<p>
									<label for="<?php echo $this->get_field_id( 'copyright-url' ); ?>"><?php _e( 'Url for copyright holder ( Optional )' ); ?></label>
									<?php if( isset( $instance['error'][$value]['url'] ) ) : ?>
										<br /><label class="attribution-error-label"><?php echo $instance['error'][$value]['url']; ?></label>
									<?php endif; ?>
									<input 	class="widefat <?php if( isset( $instance['error'][$value]['url'] ) ) { echo 'attribution-error-input'; } ?>" 
											id="<?php echo $this->get_field_id( 'copyright-url' ); ?>" 
											name="<?php echo $this->get_field_name( 'copyright-url' ); ?>" 
											type="text" 
											value="<?php echo esc_attr( $instance[ 'copyright-url' ] ); ?>" />
								</p>
								<input 	class="widefat" 
										id="<?php echo $this->get_field_id( 'show-copyright' ); ?>" 
										name="<?php echo $this->get_field_name( 'show-copyright' ); ?>" 
										type="checkbox" <?php checked( $instance[ 'show-copyright' ], 'on' ); ?> />
								<label for="<?php echo $this->get_field_id( 'show-copyright' ); ?>"><?php _e( 'Show copyright item' ); ?></label>
								<input 	class="attribution-open" 
										id="<?php echo $this->get_field_id( 'open-'.$value ); ?>" 
										name="<?php echo $this->get_field_name( 'open-'.$value ); ?>" 
										type="hidden" 
										value="<?php echo $is_open; ?>" />
							</div>
						</div>
					<?php
					else :
						// Case is user added item.
						$display = empty( $instance['display-'.$value] ) ? '' : $instance['display-'.$value];
						$link_part = empty( $instance['link-'.$value] ) ? '' : $instance['link-'.$value];
						$link_url = empty( $instance['url-'.$value] ) ? '' : $instance['url-'.$value];
						$show_item = empty( $instance['show-item-'.$value] ) ? true : $instance['show-item-'.$value];
						if( isset( $instance['error'][$value] ) ) {
							$show_item = false;
						}

						$valid = true;
						if( $display == '' || isset( $instance['error'][$value]['link'] ) || isset( $instance['error'][$value]['url'] ) ) {
							$valid = false;
							$show_add_button = false;
						}

						?>
						<div 	id="attribution-item-<?php echo $value; ?>" 
								class="attribution-list-item attribution-list-item-user-added <?php if( isset( $instance['error'][$value] ) ) { echo 'attribution-error'; } if( ! $valid ) { echo ' social-media-invalid'; }?>">
							<h5 class="list-item-title"><span class="item-title"><?php _e( 'Item '.$value ); ?></span><a class="attribution-action hide-if-no-js"></a></h5>
							<div class="list-item-fields">
								<p>
									<label for="<?php echo $this->get_field_id( 'display-'.$value ); ?>"><?php _e( 'Display Text' ); ?></label>
									<input 	class="widefat attribution-list-item-display<?php if( isset( $instance['error'][$value]['display'] ) ) { echo ' attribution-error-input'; } ?>" 
											id="<?php echo $this->get_field_id( 'display-'.$value ); ?>" 
											name="<?php echo $this->get_field_name( 'display-'.$value ); ?>" 
											type="text" 
											value="<?php echo esc_attr( $display ); ?>" 
											<?php if( isset( $instance['error'][$value]['display'] ) ) : ?>
												placeholder="<?php echo $instance['error'][$value]['display']; ?>"
											<?php endif; ?>/>
								</p>
								<p>
									<label for="<?php echo $this->get_field_id( 'link-'.$value ); ?>"><?php _e( 'Link Part Of Text ( Optional )' ); ?></label>
									<span class="attribution-error-label attribution-error-label-link<?php if( ! isset( $instance['error'][$value]['link'] ) ) { echo ' hide-item'; }; ?>"><?php _e( ' No matching text above' ); ?></span>
									<input 	class="widefat attribution-list-item-link<?php if( isset( $instance['error'][$value]['link'] ) ) { echo ' attribution-error-input'; } ?>"
											id="<?php echo $this->get_field_id( 'link-'.$value ); ?>" 
											name="<?php echo $this->get_field_name( 'link-'.$value ); ?>" 
											type="text" 
											value="<?php echo esc_attr( $link_part ); ?>" />
								</p>
								<p>
									<label for="<?php echo $this->get_field_id( 'url-'.$value ); ?>"><?php _e( 'Url ( Optional )' ); ?></label>
									<span class="attribution-error-label attribution-error-label-url<?php if( ! isset( $instance['error'][$value]['url'] ) ) { echo ' hide-item'; }; ?>"><?php _e( ' Invalid' ); ?></span>
									<input 	class="widefat attribution-list-item-url<?php if( isset( $instance['error'][$value]['url'] ) ) { echo ' attribution-error-input'; } ?>" 
											id="<?php echo $this->get_field_id( 'url-'.$value ); ?>" 
											name="<?php echo $this->get_field_name( 'url-'.$value ); ?>" 
											type="text" 
											value="<?php echo esc_attr( $link_url ); ?>" />
								</p>
								<p>
									<input 	class="widefat attribution-show-icon-checkbox" 
											id="<?php echo $this->get_field_id( 'show-item-'.$value ); ?>" 
											name="<?php echo $this->get_field_name( 'show-item-'.$value ); ?>" 
											type="checkbox" <?php checked( $show_item, 'on' ); ?> 
											<?php if( ! $show_item ) { echo 'disabled'; } ?>/>
									<label for="<?php echo $this->get_field_id( 'show-item-'.$value ); ?>"><?php _e( 'Show this item' ); ?></label>
								</p>
								<a class="attribution-remove hide-if-no-js <?php if( $value == 0 || $amount == 2 ) { echo 'hide-item'; } ?>"><?php echo __( "Remove" ); ?></a>
								<input 	class="attribution-open" 
										id="<?php echo $this->get_field_id( 'open-'.$value ); ?>" 
										name="<?php echo $this->get_field_name( 'open-'.$value ); ?>" 
										type="hidden" 
										value="<?php echo $is_open; ?>" />
							</div>
						</div>
					<?php
					endif;
				endforeach;
				?>
			</div>
			<?php

			// Reordering, deleteion and adding on a non JS browser.
			if ( isset( $_GET['editwidget'] ) && $_GET['editwidget'] ) : ?>
				<table class='widefat'>
					<thead><tr><th><?php _e("Item"); ?></th><th><?php _e("Position/Action"); ?></th></tr></thead>
					<tbody>
						<?php foreach( $instance['item-order'] as $index => $value ) : ?>
							<tr>
								<?php if( $value == 0 ) : ?>
									<td><?php _e( 'Copyright' ); ?></td>
								<?php else: ?>
									<td><?php _e( 'Item '.$value ); ?></td>
								<?php endif; ?>
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
											if( $value != 0 && $amount > 2 ) : 
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

			// Show add button
			$valid = true;
			if( $icon == '' || $name == '' || isset( $instance['error'][$value]['url'] ) ) {
				$valid = false;
			}

			?>
				<div class="add-button-row hide-if-no-js<?php if( ! $show_add_button ) { echo ' hide-item'; } ?>">
					<a class="add-attribution button-secondary"><?php _e( "Add Item" ); ?></a>
				</div>
			<?php

			// Show wordpress entry.
			if ( ! isset( $instance[ 'show-wordpress' ] ) ) {
				$instance[ 'show-wordpress' ] = true;
			}
			?>
				<p>
					<input 	class="widefat" 
							id="<?php echo $this->get_field_id( 'show-wordpress' ); ?>" 
							name="<?php echo $this->get_field_name( 'show-wordpress' ); ?>" 
							type="checkbox" <?php checked( $instance[ 'show-wordpress' ], 'on' ); ?> />
					<label for="<?php echo $this->get_field_id( 'show-wordpress' ); ?>"><?php _e( 'Show: "Powered By Wordpress"' ); ?></label>
				</p>
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
					$id = end( explode( '-', $value ) );
					if( $id == 'category') {
						$id = 0;
					} else {
						$id = ( int ) $id ;
					}
					$order[$key] = $id;
				}
			}

			if( $order ) {
				foreach ( $order as $i => $id ) {
					// Current open state for item, prevents closing upon ajax refresh.
					$instance['open-'.( $id )] = empty( $new_instance['open-'.$id]) ? 'false' : strip_tags( $new_instance['open-'.$id] );

					if( $id == 0 ) {
						// Validate Copyright fields.
						$name = strip_tags( trim( $new_instance['copyright-name'] ) );
						$url = strip_tags( trim( $new_instance['copyright-url'] ) );
						if( ! empty( $url ) && empty( $name ) ) {
							$instance['error'][$id]['name'] = __( 'No name, will not display link' );
							$instance['copyright-name'] = '';
						} else {
							$instance['copyright-name'] = $name;
						}

						if ( filter_var( $url, FILTER_VALIDATE_URL ) === false && ! empty( $url ) ) {
						    $instance['error'][$id]['url'] = __( 'Invalid Url' );
						}

						$instance['copyright-url'] = $url;
					} else {
						// Validate user added fields.
						$display = strip_tags( trim( $new_instance['display-'.$id] ) );
						$link_part = strip_tags( trim( $new_instance['link-'.$id] ) );
						$link_url = strip_tags( trim( $new_instance['url-'.$id] ) );

						if( ( ! empty( $link_part ) || ! empty( $link_url ) ) && empty( $display ) ) {
							$instance['error'][$id]['display'] = __( 'No text, will not display link' );
							$instance['display-'.$id] = '';
						} else {
							$instance['display-'.$id] = $display;
						}

						if( ! empty( $link_part ) ) {
							if( strpos( $display, $link_part ) === false ) {
								$instance['error'][$id]['link'] = __( 'No matching text in the display field' );
							}
						}

						$instance['link-'.$id] = $link_part;

						if ( filter_var( $link_url, FILTER_VALIDATE_URL ) === false && ! empty( $link_url ) ) {
						    $instance['error'][$id]['url'] = __( 'Invalid Url' );
						}

						$instance['url-'.$id] = $link_url;

						if( strip_tags( $new_instance['show-item-'.$id] ) ) {
							$instance['show-item-'.$id] = $new_instance['show-item-'.$id];
						} else {
							$instance['show-item-'.$id] = false;
						}
					}
				}
			}

			$instance['item-order'] = $order;
			$instance['total'] = $total;

			if( !empty( $new_instance['show-copyright'] ) ) {
				$instance['show-copyright'] = $new_instance['show-copyright'];
			}

			if( !empty( $new_instance['show-wordpress'] ) ) {
				$instance['show-wordpress'] = $new_instance['show-wordpress'];
			}

			return $instance;
		}

		public function attribution_load_scripts($hook) {
			if( $hook != 'widgets.php')
				return;

			if ( !isset($_GET['editwidget-nojs'])) {
				wp_enqueue_script( 'widget-attribution-js', get_template_directory_uri().'/widgets/attribution_widget/js/widget-attribution.js');
			}

			wp_enqueue_style( 'widget-attribution-css', get_template_directory_uri().'/widgets/attribution_widget/css/widget-attribution.css');
		}
	}

	// register social media icons widget
	function register_attribution_widget() {
		 register_widget( 'Attribution_Widget' );
	}

	add_action( 'widgets_init', 'register_attribution_widget' );
?>
