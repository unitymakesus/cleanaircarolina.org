<?php
class ET_Builder_Module_Page_Widget extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Page Widget', 'et_builder' );
		$this->slug       = 'et_pb_cac_page_widget';
		$this->fb_support = TRUE;

		$this->whitelisted_fields = array(
			'src',
			'title_text',
			'excerpt',
			'url_',
			'url_new_window_',
			'animation',
			'admin_label',
			'module_id',
			'module_class',
			'max_width',
			'force_fullwidth',
			'always_center_on_mobile',
			'max_width_tablet',
			'max_width_phone',
			'max_width_last_edited',
		);

		$this->fields_defaults = array(
			'url_new_window_'          => array( 'off' ),
			'animation'               => array( 'left' ),
			'force_fullwidth'         => array( 'off' ),
			'always_center_on_mobile' => array( 'on' ),
		);

		$this->advanced_options = array(
			'border'                => array(),
			'custom_margin_padding' => array(
				'use_padding' => FALSE,
				'css'         => array(
					'important' => 'all',
				),
			),
		);
	}

	function get_fields() {
		// List of animation options
		$animation_options_list = array(
			'left'    => esc_html__( 'Left To Right', 'et_builder' ),
			'right'   => esc_html__( 'Right To Left', 'et_builder' ),
			'top'     => esc_html__( 'Top To Bottom', 'et_builder' ),
			'bottom'  => esc_html__( 'Bottom To Top', 'et_builder' ),
			'fade_in' => esc_html__( 'Fade In', 'et_builder' ),
			'off'     => esc_html__( 'No Animation', 'et_builder' ),
		);

		$animation_option_name       = sprintf( '%1$s-animation', $this->slug );
		$default_animation_direction = ET_Global_Settings::get_value( $animation_option_name );

		// If user modifies default animation option via Customizer, we'll need to change the order
		if ( 'left' !== $default_animation_direction && ! empty( $default_animation_direction ) && array_key_exists( $default_animation_direction, $animation_options_list ) ) {
			// The options, sans user's preferred direction
			$animation_options_wo_default = $animation_options_list;
			unset( $animation_options_wo_default[ $default_animation_direction ] );

			// All animation options
			$animation_options = array_merge(
				array( $default_animation_direction => $animation_options_list[ $default_animation_direction ] ),
				$animation_options_wo_default
			);
		} else {
			// Simply copy the animation options
			$animation_options = $animation_options_list;
		}

		$fields = array(
			'src'                     => array(
				'label'              => esc_html__( 'Image URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
			),
			'title_text'              => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This defines the title.', 'et_builder' ),
			),
			'excerpt'                 => array(
				'label'           => esc_html__( 'Excerpt', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This defines the excerpt.', 'et_builder' ),
			),
			'url_'                     => array(
				'label'           => esc_html__( 'Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your destination URL here.', 'et_builder' ),
			),
			'url_new_window_'          => array(
				'label'           => esc_html__( 'Url Opens', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'description'     => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
			),
			'animation'               => array(
				'label'           => esc_html__( 'Animation', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => $animation_options,
				'description'     => esc_html__( 'This controls the direction of the lazy-loading animation.', 'et_builder' ),
			),
			'max_width'               => array(
				'label'           => esc_html__( 'Image Max Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'mobile_options'  => TRUE,
				'validate_unit'   => TRUE,
			),
			'max_width_last_edited'   => array(
				'type'     => 'skip',
				'tab_slug' => 'advanced',
			),
			'force_fullwidth'         => array(
				'label'           => esc_html__( 'Force Fullwidth', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( "No", 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
			),
			'always_center_on_mobile' => array(
				'label'           => esc_html__( 'Always Center Image On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( "No", 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
			),
			'max_width_tablet'        => array(
				'type'     => 'skip',
				'tab_slug' => 'advanced',
			),
			'max_width_phone'         => array(
				'type'     => 'skip',
				'tab_slug' => 'advanced',
			),
			'disabled_on'             => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
			),
			'admin_label'             => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
			),
			'module_id'               => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class'            => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'et_pb_custom_css_regular',
			)
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = NULL, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$src                     = $this->shortcode_atts['src'];
		$title_text              = $this->shortcode_atts['title_text'];
		$excerpt                 = $this->shortcode_atts['excerpt'];
		$animation               = $this->shortcode_atts['animation'];
		$url                     = $this->shortcode_atts['url_'];
		$url_new_window          = $this->shortcode_atts['url_new_window_'];
		$max_width               = $this->shortcode_atts['max_width'];
		$max_width_tablet        = $this->shortcode_atts['max_width_tablet'];
		$max_width_phone         = $this->shortcode_atts['max_width_phone'];
		$max_width_last_edited   = $this->shortcode_atts['max_width_last_edited'];
		$force_fullwidth         = $this->shortcode_atts['force_fullwidth'];
		$always_center_on_mobile = $this->shortcode_atts['always_center_on_mobile'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( 'on' === $always_center_on_mobile ) {
			$module_class .= ' et_always_center_on_mobile';
		}

		if ( '' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width ) {
			$max_width_responsive_active = et_pb_get_responsive_status( $max_width_last_edited );

			$max_width_values = array(
				'desktop' => $max_width,
				'tablet'  => $max_width_responsive_active ? $max_width_tablet : '',
				'phone'   => $max_width_responsive_active ? $max_width_phone : '',
			);

			et_pb_generate_responsive_css( $max_width_values, '%%order_class%%', 'max-width', $function_name );
		}

		if ( 'on' === $force_fullwidth ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% img',
				'declaration' => 'width: 100%;',
			) );
		}

		if ( $this->fields_defaults['align'][0] !== $align ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'text-align: %1$s;',
					esc_html( $align )
				),
			) );
		}

		if ( 'center' !== $align ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'margin-%1$s: 0;',
					esc_html( $align )
				),
			) );
		}

		$output = sprintf(
			'<figure><img src="%1$s" alt="%2$s" /></figure>
			 <footer><h3 class="title">%3$s</h3><div class="excerpt">%4$s</div></footer>',
			esc_url( $src ),
			esc_attr( $alt ),
			esc_attr( $title_text ),
			esc_attr( $excerpt )
		);

		if ( '' !== $url ) {
			$output = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
				esc_url( $url ),
				$output,
				( 'on' === $url_new_window ? ' target="_blank"' : '' )
			);
		}

		$animation = '' === $animation ? ET_Global_Settings::get_value( 'et_pb_image-animation' ) : $animation;

		$output = sprintf(
			'<article%5$s class="et_pb_module et-waypoint page-callout%2$s%3$s%4$s%6$s">%1$s</article>',
			$output,
			esc_attr( " et_pb_animation_{$animation}" ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' ),
			( 'on' === $sticky ? esc_attr( ' et_pb_image_sticky' ) : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			'on' === $is_overlay_applied ? ' et_pb_has_overlay' : ''
		);

		return $output;
	}
}

new ET_Builder_Module_Page_Widget;


//class ET_Builder_Module_Big_Number extends ET_Builder_Module {
//	function init() {
//		$this->name       = esc_html__( 'Big Number', 'et_builder' );
//		$this->slug       = 'et_pb_big_number';
////		$this->fb_support = TRUE;
//
//		$this->whitelisted_fields = array(
//			'number',
//			'background_layout',
//			'admin_label',
//			'module_id',
//			'module_class',
//		);
//
//		$this->fields_defaults = array(
//			'number'            => array( '30' ),
//			'background_layout' => array( 'light' ),
//		);
//	}
//
//	function get_fields() {
//		$fields = array(
//			'number' => array(
//				'label'           => esc_html__( 'Number', 'et_builder' ),
//				'type'            => 'text',
//				'option_category' => 'basic_option',
//				'value_type'      => 'float',
//				'description'     => esc_html__( "Enter your number here.", 'et_builder' ),
//			),
////			'background_layout' => array(
////				'label'           => esc_html__( 'Text Color', 'et_builder' ),
////				'type'            => 'select',
////				'option_category' => 'color_option',
////				'options'         => array(
////					'light' => esc_html__( 'Dark', 'et_builder' ),
////					'dark'  => esc_html__( 'Light', 'et_builder' ),
////				),
////				'description' => esc_html__( 'Here you can choose whether your title text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
////			),
////			'disabled_on' => array(
////				'label'           => esc_html__( 'Disable on', 'et_builder' ),
////				'type'            => 'multiple_checkboxes',
////				'options'         => array(
////					'phone'   => esc_html__( 'Phone', 'et_builder' ),
////					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
////					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
////				),
////				'additional_att'  => 'disable_on',
////				'option_category' => 'configuration',
////				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
////			),
////			'admin_label' => array(
////				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
////				'type'        => 'text',
////				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
////			),
////			'module_id' => array(
////				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
////				'type'            => 'text',
////				'option_category' => 'configuration',
////				'tab_slug'        => 'custom_css',
////				'option_class'    => 'et_pb_custom_css_regular',
////			),
////			'module_class' => array(
////				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
////				'type'            => 'text',
////				'option_category' => 'configuration',
////				'tab_slug'        => 'custom_css',
////				'option_class'    => 'et_pb_custom_css_regular',
////			),
//		);
//		return $fields;
//	}
//
//	function shortcode_callback( $atts, $content = null, $function_name ) {
//		$number            = $this->shortcode_atts['number'];
//		$module_id         = $this->shortcode_atts['module_id'];
//		$module_class      = $this->shortcode_atts['module_class'];
//		$background_layout = $this->shortcode_atts['background_layout'];
//
//		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
//
////		wp_enqueue_script( 'fittext' );
//
//		$number = str_ireplace( '%', '', $number );
//
//		$class = " et_pb_module et_pb_bg_layout_{$background_layout}";
//
//		$output = sprintf(
//			'<div%1$s class="et_pb_big_number%2$s%3$s">
//				%4$s
//			</div>',
//			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
//			esc_attr( $class ),
//			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
//			esc_attr( $number )
//		);
//
//		return $output;
//	}
//}
//new ET_Builder_Module_Big_Number;
