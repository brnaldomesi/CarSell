<?php
	
	defined( 'ABSPATH' ) or die( 'Keep Silent' );
	
	/**
	 * Example:
	 *  new WVS_Customize_Heading( $wp_customize, 'section', esc_html__( 'Heading Options', 'text-domain' ) );
	 */
	
	if ( ! class_exists( 'WVS_Customize_Heading_Control' ) ):
		
		class WVS_Customize_Heading_Control extends WP_Customize_Control {
			
			public $type = 'wvs-heading';
			
			public function __construct( $manager, $id, $args = array() ) {
				parent::__construct( $manager, $id, $args );
			}
			
			public function enqueue() {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_style( 'wvs-customize-heading-control', woo_variation_swatches()->assets_uri( "/css/wvs-customize-heading-control$suffix.css" ) );
			}
			
			protected function render_content() {
				?>
				<?php if ( ! empty( $this->label ) ) : ?>
                    <h4 class="wvs-customize-heading-control-title"><?php echo esc_html( $this->label ); ?></h4>
				<?php endif;
			}
		}
	endif;
	
	if ( ! class_exists( 'WVS_Customize_Heading' ) ):
		
		class WVS_Customize_Heading {
			
			public function __construct( $wp_customize, $section, $title, $priority = NULL ) {
				
				static $customize_heading_control_id = 1;
				$this->add_settings( $wp_customize, $customize_heading_control_id );
				$this->add_controls( $wp_customize, $title, $section, $priority, $customize_heading_control_id );
				$customize_heading_control_id ++;
			}
			
			private function add_settings( $wp_customize, $id ) {
				
				$wp_customize->add_setting( sprintf( 'wvs-customize-heading-control-%d', $id ), array(
					'sanitize_callback' => 'sanitize_key'
				) );
			}
			
			private function add_controls( $wp_customize, $title, $section, $priority, $id ) {
				
				$wp_customize->add_control( new WVS_Customize_Heading_Control( $wp_customize, sprintf( 'wvs-customize-heading-control-%d', $id ), array(
					'label'    => $title,
					'section'  => $section,
					'priority' => $priority,
				) ) );
			}
		}
	
	endif;