<?php
	function chang_customize_register_page( $wp_customize ) {
		class WP_Customize_Range_Control extends WP_Customize_Control
		{
			 public $type = 'custom_range';
			 public function enqueue()
			 {
				  wp_enqueue_script(
				      'cs-range-control',
				      get_template_directory_uri() . '/javascript/range-control.js',
				      array('jquery'),
				      false,
				      true
				  );
			 }
			 public function render_content()
			 {
				  ?>
				  <label>
				      <?php if ( ! empty( $this->label )) : ?>
				          <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
				      <?php endif; ?>
				      <input data-input-type="range" type="range" <?php $this->input_attrs(); ?> value="<?php echo esc_attr($this->value()); ?>" <?php $this->link(); ?> />
				      <div class="cs-range-value" style="display: inline-block; vertical-align: top;"><?php echo esc_attr($this->value()); ?></div>
				      <?php if ( ! empty( $this->description )) : ?>
				          <span class="description customize-control-description"><?php echo $this->description; ?></span>
				      <?php endif; ?>
				  </label>
				  <?php
			 }
		}
	
		$wp_customize->add_panel(
			'page-layout-panel' , array(
				'title'      => __( 'Page Layouts', 'mytheme' ),
			)
		);
	}

	add_action( 'customize_register', 'chang_customize_register_page' );
?>
