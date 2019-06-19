<?php
	
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
	
	if ( ! class_exists( 'WVS_Customizer' ) ):
		class WVS_Customizer {
			private $_settings_name;
			private $_fields;
			private $_plugin_class;
			private $_theme_feature_name;
			
			public function __construct( $theme_feature_name, $plugin_class, $settings_name, $fields ) {
				$this->_settings_name      = $settings_name;
				$this->_fields             = $fields;
				$this->_plugin_class       = $plugin_class;
				$this->_theme_feature_name = $theme_feature_name;
				
				add_action( 'customize_register', array( $this, 'includes' ) );
				add_action( 'customize_register', array( $this, 'add_panel' ) );
				add_action( 'customize_register', array( $this, 'add_section' ) );
				// add_action( 'customize_controls_print_styles', array( $this, 'add_styles' ) );
				// add_action( 'customize_controls_print_scripts', array( $this, 'add_scripts' ), 30 );
			}
			
			public function includes() {
				require_once $this->_plugin_class->include_path( 'class-wvs-customize-heading.php' );
				require_once $this->_plugin_class->include_path( 'class-wvs-customize-alpha-color-control.php' );
			}
			
			public function add_panel( $wp_customize ) {
				$wp_customize->add_panel( $this->_settings_name, array(
					'priority'   => 100,
					'capability' => 'manage_woocommerce',
					'title'      => esc_html__( 'WooCommerce Variation Swatches', 'woo-variation-swatches' ),
				) );
			}
			
			public function add_section( $wp_customize ) {
				
				// Theme Support
				$theme_support = FALSE;
				if ( current_theme_supports( $this->_theme_feature_name ) ) {
					$theme_support = get_theme_support( $this->_theme_feature_name );
				}
				
				foreach ( $this->_fields as $panel ) {
					$section_id = sprintf( '%s_%s', $this->_settings_name, $panel[ 'id' ] );
					
					$wp_customize->add_section( $section_id, array(
						'title' => $panel[ 'title' ],
						'panel' => $this->_settings_name,
					) );
					
					
					foreach ( $panel[ 'sections' ] as $section ) {
						
						if ( isset( $section[ 'pro' ] ) ) {
							continue;
						}
						
						if ( ! isset( $section[ 'customize_hidden' ] ) || ! $section[ 'customize_hidden' ] ) {
							new WVS_Customize_Heading( $wp_customize, $section_id, $section[ 'title' ] );
						}
						
						
						foreach ( $section[ 'fields' ] as $field ) {
							
							if ( isset( $field[ 'pro' ] ) ) {
								continue;
							}
							
							$setting_id = sprintf( '%s[%s]', $this->_settings_name, $field[ 'id' ] );
							
							$default_value = $field[ 'default' ];
							
							// Theme Support
							if ( $theme_support ) {
								$default_value = isset( $theme_support[ 0 ][ $field[ 'id' ] ] ) ? $theme_support[ 0 ][ $field[ 'id' ] ] : $field[ 'default' ];
							}
							
							// Add Settings
							$setting_options = array(
								'default'           => $default_value,
								'type'              => 'option',
								'capability'        => 'manage_woocommerce',
								'sanitize_callback' => 'sanitize_key', //
							);
							
							switch ( $field[ 'type' ] ) {
								case 'checkbox':
									$setting_options[ 'sanitize_callback' ] = array( $this, 'sanitize_checkbox' );
									break;
							}
							
							if ( isset( $field[ 'customize_sanitize_callback' ] ) && is_callable( $field[ 'customize_sanitize_callback' ] ) ) {
								$setting_options[ 'sanitize_callback' ] = $field[ 'customize_sanitize_callback' ];
							}
							
							$wp_customize->add_setting( $setting_id, $setting_options );
							
							// Add Control
							$control_options = array(
								'label'       => $field[ 'title' ],
								'description' => $field[ 'desc' ],
								'section'     => $section_id,
								'type'        => $field[ 'type' ]
							);
							
							switch ( $field[ 'type' ] ) {
								case 'radio':
								case 'select':
									$control_options[ 'choices' ] = $field[ 'options' ];
									break;
							}
							
							// active_callback
							if ( isset( $field[ 'customize_hidden' ] ) && $field[ 'customize_hidden' ] ) {
								$control_options[ 'active_callback' ] = '__return_false';
							}
							
							if ( isset( $section[ 'customize_hidden' ] ) && $section[ 'customize_hidden' ] ) {
								$control_options[ 'active_callback' ] = '__return_false';
							}
							
							
							if ( isset( $field[ 'customize_control_class' ] ) && class_exists( $field[ 'customize_control_class' ] ) ) {
								$class = $field[ 'customize_control_class' ];
								unset( $control_options[ 'type' ] );
								$wp_customize->add_control( new $class( $wp_customize, $setting_id, $control_options ) );
							} else {
								$wp_customize->add_control( $setting_id, $control_options );
							}
						}
					}
				}
			}
			
			public function sanitize_checkbox( $value ) {
				$filter = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
				
				return is_null( $filter ) ? 0 : $filter;
			}
		}
	endif;