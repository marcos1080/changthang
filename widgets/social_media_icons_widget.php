<?php
	class Social_Media_Icon_Widget extends WP_Widget {

		/**
		 * Sets up the widgets name etc
		 */
		public function __construct() {
			$widget_ops = array( 
				'classname' => 'social_media_icons',
				'description' => 'Add social media icons to widget areas.',
			);
			parent::__construct( 'social_media_icons', 'Social Media Icons', $widget_ops );
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
			
			// Get all icon ids.
			$icon_ids = get_option( 'social_media_icon_ids' );
			?>
			<?php if ( $instance['menu'] ) : ?>
				<ul id="social-media-icons-menu">
				<?php foreach( $icon_ids as $id ) : ?>
					<?php $icon_values = get_option( 'social-media-icon-'.$id ); ?>
					<li id="social-media-icon-<?php echo $icon_values['title']; ?>" class="social-media-icon-wrapper">
						<a href="<?php echo $icon_values['url']; ?>" class="social-media-icon">
							<img src="<?php echo wp_get_attachment_image_url( $icon_values['icon_id'] ); ?>" alt="Icon for <?php echo $icon_values['title']; ?>" />
							<?php if ( $icon_values['hover_id'] != '' ) : ?>
								<img class="hover" src="<?php echo wp_get_attachment_image_url( $icon_values['hover_id'] ); ?>" alt="Hover icon for <?php echo $icon_values['title']; ?>" />
							<?php endif; ?>
						</a>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<h2 class="widget-title"><?php echo _e( $title ); ?></h2>
				<ul>
				<?php foreach( $icon_ids as $id ) : ?>
					<?php $icon_values = get_option( 'social-media-icon-'.$id ); ?>
					<li id="social-media-icon-<?php echo $icon_values['title']; ?>" class="social-media-icon-wrapper">
						<a href="<?php echo $icon_values['url']; ?>" class="social-media-icon">
							<img src="<?php echo wp_get_attachment_image_url( $icon_values['icon_id'] ); ?>" alt="Icon for <?php echo $icon_values['title']; ?>" />
							<?php if ( $icon_values['hover_id'] != '' ) : ?>
								<img class="hover" src="<?php echo wp_get_attachment_image_url( $icon_values['hover_id'] ); ?>" alt="Hover icon for <?php echo $icon_values['title']; ?>" />
							<?php endif; ?>
						</a>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php endif;
			echo $args['after_widget'];
		}

		/**
		 * Outputs the options form on admin
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			// outputs the options form on admin
			if ( isset( $instance[ 'title' ] ) ) {
				$title = $instance[ 'title' ];
			} else {
				$title = __( 'New title' );
			}
			
			if ( ! isset( $instance[ 'menu' ] ) ) {
				$instance[ 'menu' ] = false;
			}

			// Widget admin form
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<input class="widefat" id="<?php echo $this->get_field_id( 'menu' ); ?>" name="<?php echo $this->get_field_name( 'menu' ); ?>" type="checkbox" <?php checked( $instance[ 'menu' ], 'on' ); ?> />
				<label for="<?php echo $this->get_field_id( 'menu' ); ?>"><?php _e( 'Show In Menu' ); ?></label>
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
			$instance = $old_instance;
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['menu'] = $new_instance['menu'];
			return $instance;
		}
	}
	
	// register social media icons widget
	function register_social_media_icons_widget() {
		 register_widget( 'Social_Media_Icon_Widget' );
	}

	add_action( 'widgets_init', 'register_social_media_icons_widget' );
?>
