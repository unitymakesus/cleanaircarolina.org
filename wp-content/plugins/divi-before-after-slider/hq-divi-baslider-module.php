<?php

/*
 * Plugin Name: Before After Image Slider Module for Divi
 * Plugin URI:  http://demo.cakewp.com/divi-beaslider
 * Description: Showcase your before & after photos in an awesome unique way with this easy to use divi module.
 * Author:      munirkamal
 * Version:     1.0
 * Author URI:  http://www.cakewp.com
 */

	
    add_action('plugins_loaded', 'cwp_hq_divi_baslider_init');
    
    if (!function_exists('cwp_hq_divi_baslider_init')){
    function cwp_hq_divi_baslider_init() {
        add_action('init', 'cwp_hq_divi_baslider_setup', 9999);
        
        wp_register_script( 
        	'cwp_eventMove', 
        	plugins_url( '/js/jquery.event.move.js', __FILE__ ), 
        	array ('jquery'),
        	false,
        	false 
        );
        wp_register_script( 
        	'cwp_twentytwenty', 
        	plugins_url( '/js/jquery.twentytwenty.js', __FILE__ ), 
        	array ('jquery', 'cwp_eventMove'),
        	false,
        	false 
        );
        wp_register_style( 
        	'cwp_twentytwenty_css', 
        	plugins_url( '/css/twentytwenty.css', __FILE__ ), 
        	false,
        	false,
        	false 
        );
        //add_action('admin_head', 'cwp_hq_divi_buttons_admin_head', 9999);
        if ( ! empty( $_GET['et_fb'] ) ) { 
			?>
			<style id="cwp-theme-customizer-css-fb-support">
				.et-waypoint {
				    opacity: 1 !important;
				}
			</style>
		
		<?php }
    }
	}
    
    
    if(!function_exists('cwp_hq_divi_baslider_setup')) {
    function cwp_hq_divi_baslider_setup() {
    
        if ( class_exists('ET_Builder_Module')) {
				
		class CWP_HQ_ET_Builder_Module_Baslider extends ET_Builder_Module {

	function init() {
		$this->name       = esc_html__( 'Before After Image Slider', 'et_builder' );
		$this->slug       = 'cwp_hq_et_pb_baslider';
		$this->fb_support = false;

		$this->whitelisted_fields = array(
			'src',
			'src2',
			'alt',
			'alt2',
			'before_label',
			'after_label',
			'animation',
			'orientation',
			'admin_label',
			'module_id',
			'module_class',
			'force_fullwidth',
		);

		$this->fields_defaults = array(
			'animation'               => array( 'off' ),
			'orientation'             => array( 'horizontal' ),
			'force_fullwidth'         => array( 'on' ),
		);

		$this->advanced_options = array(
			'border'                => array(),
			'custom_margin_padding' => array(
				'use_padding' => false,
				'css' => array(
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
				array( $default_animation_direction => $animation_options_list[$default_animation_direction] ),
				$animation_options_wo_default
			);
		} else {
			// Simply copy the animation options
			$animation_options = $animation_options_list;
		}

		$fields = array(
			'src' => array(
				'label'              => esc_html__( 'Before Image URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
			),
			'src2' => array(
				'label'              => esc_html__( 'After Image URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
			),
			'alt' => array(
				'label'           => esc_html__( 'Before Image Alternative Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This defines the HTML ALT text. A short description of your image can be placed here.', 'et_builder' ),
			),
			'alt2' => array(
				'label'           => esc_html__( 'After Image Alternative Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This defines the HTML ALT text. A short description of your image can be placed here.', 'et_builder' ),
			),
			'before_label' => array(
				'label'           => esc_html__( 'Before Label', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This Will change the Before Label Text.', 'et_builder' ),
			),
			'after_label' => array(
				'label'           => esc_html__( 'After Label', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This Will change the After Label Text.', 'et_builder' ),
			),
			'animation' => array(
				'label'             => esc_html__( 'Animation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => $animation_options,
				'description'       => esc_html__( 'This controls the direction of the lazy-loading animation.', 'et_builder' ),
			),
			
			'orientation' => array(
				'label'           => esc_html__( 'Sider Orientation', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'horizontal' => esc_html__( 'Horizontal', 'et_builder' ),
					'vertical'   => esc_html__( 'Vertical', 'et_builder' ),
				),
				'description'       => esc_html__( 'Here you can choose the slider bar orientation.', 'et_builder' ),
			),
			
			'force_fullwidth' => array(
				'label'             => esc_html__( 'Force Fullwidth', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( "No", 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'    => 'advanced',
			),
			'disabled_on' => array(
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
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		wp_enqueue_script('cwp_eventMove' );
		wp_enqueue_script('cwp_twentytwenty' );
		wp_enqueue_style('cwp_twentytwenty_css' );
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$src                     = $this->shortcode_atts['src'];
		$src2                    = $this->shortcode_atts['src2'];
		$alt                     = $this->shortcode_atts['alt'];
		$alt2                    = $this->shortcode_atts['alt2'];
		$before_label            = $this->shortcode_atts['before_label'];
		$after_label             = $this->shortcode_atts['after_label'];
		$animation               = $this->shortcode_atts['animation'];
		$orientation             = $this->shortcode_atts['orientation'];
		$force_fullwidth         = $this->shortcode_atts['force_fullwidth'];


		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );


		if ( 'on' === $force_fullwidth ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% img',
				'declaration' => 'width: 100%;',
			) );
		}

		
		$module_id_custom = trim(ET_Builder_Element::add_module_order_class( '', $function_name ));

		if ( '' !== $before_label ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '.'.$module_id_custom . ' .twentytwenty-before-label:before',
				'declaration' => 'content: "'.esc_html($before_label).'" !important;',
			) );
		}
		if ( '' !== $after_label ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '.'.$module_id_custom . ' .twentytwenty-after-label:before',
				'declaration' => 'content: "'.$after_label.'" !important;',
			) );
		}

		$output = sprintf(
			'<img src="%1$s" alt="%2$s" />
			 <img src="%3$s" alt="%4$s" />',
			esc_url( $src ),
			esc_attr( $alt ),
			esc_url( $src2 ),
			esc_attr( $alt2 )
		);


		$animation = '' === $animation ? ET_Global_Settings::get_value( 'et_pb_image-animation' ) : $animation;

		$output = sprintf(
			'<div id="%4$s" class="twentytwenty-container et_pb_module et-waypoint et_pb_image%2$s%3$s">
				%1$s
			</div>
			<script>
				
			jQuery(document).ready(function( $ ) {
			  $(window).load(function () {
					$(\'#%4$s\').eq(0).twentytwenty({
			  	default_offset_pct: 0.5,
			  	orientation: \'%5$s\',
			  });
					$(\'#%4$s\').eq(1).twentytwenty();
				});
			

			});
			</script>',
			$output,
			esc_attr( " et_pb_animation_{$animation}" ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' ),
			( '' !== $module_id ? sprintf( '%1$s', esc_attr( $module_id ) ) : $module_id_custom ),
			$orientation
		);
		

		return $output;
	}

		}
		$cwp_hq_et_pb_baslider = new CWP_HQ_ET_Builder_Module_Baslider;
		add_shortcode( 'cwp_hq_et_pb_baslider', array($cwp_hq_et_pb_baslider, '_shortcode_callback') );

	}
}
}
?>