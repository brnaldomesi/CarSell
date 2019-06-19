<?php
	defined( 'ABSPATH' ) or die( 'Keep Silent' );
	
	/*    $wp_customize->add_control( new WVS_Customize_Alpha_Color_Control( $wp_customize, 'id', array(
			'label'       => esc_html__( 'Label', 'textdomain' ),
			'description' => esc_html__( 'Description', 'textdomain' ),
			'section'     => 'section'
		) ) );*/
	
	if ( ! class_exists( 'WVS_Customize_Alpha_Color_Control' ) ):
		class WVS_Customize_Alpha_Color_Control extends WP_Customize_Control {
			
			public $type = 'wvs-alpha-color';
			
			public function __construct( $manager, $id, $args = array() ) {
				parent::__construct( $manager, $id, $args );
			}
			
			public function enqueue() {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_script( 'wvs-customize-alpha-color-control', woo_variation_swatches()->assets_uri( "/js/wvs-customize-alpha-color-control{$suffix}.js" ), array( 'wp-color-picker-alpha' ), '', TRUE );
			}
			
			protected function render_content() {
				?>
				<?php if ( ! empty( $this->label ) ) : ?>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>
				
				<?php if ( ! empty( $this->description ) ) : ?>
                    <span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif; ?>

                <div class="customize-control-content">
                    <label>
                        <input <?php $this->link(); ?> class="color-picker-hex wvs-color-picker" data-alpha="true" type="text" value="<?php echo $this->value() ?>" data-default-color="<?php echo $this->value() ?>"/>
                    </label>
                </div>
				<?php
			}
		}
	endif;