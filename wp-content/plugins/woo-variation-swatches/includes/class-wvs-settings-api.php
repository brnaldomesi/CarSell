<?php
	
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
	
	// 1. add settings: priority 1
	// 2. initial class: priority 2
	// 3. store defaults: priority 3
	// 4. get defaults / do whatever you want to do
	
	if ( ! class_exists( 'WVS_Settings_API' ) ):
		
		class WVS_Settings_API {
			
			private $setting_name           = 'woo_variation_swatches';
			private $transient_setting_name = '_temp_woo_variation_swatches_options';
			private $theme_feature_name     = 'woo-variation-swatches';
			private $slug;
			private $plugin_class;
			private $defaults               = array();
			
			private $fields = array();
			
			public function __construct() {
				
				$this->plugin_class = woo_variation_swatches();
				
				$this->settings_name = apply_filters( 'wvs_settings_name', $this->setting_name );
				
				$this->slug = sprintf( '%s-settings', sanitize_key( $this->plugin_class->dirname() ) );
				
				$this->fields = apply_filters( 'wvs_settings', $this->fields );
				
				add_action( 'admin_menu', array( $this, 'add_menu' ) );
				
				add_action( 'init', array( $this, 'set_defaults' ), 8 );
				
				add_action( 'admin_init', array( $this, 'settings_init' ), 90 );
				
				// add_action( 'pre_update_option', array( $this, 'before_update' ), 10, 3 );
				
				add_action( "pre_update_option_{$this->settings_name}", array( $this, 'before_update' ), 10, 3 );
				
				add_filter( 'plugin_action_links_' . $this->plugin_class->basename(), array( $this, 'plugin_action_links' ) );
				
				if ( apply_filters( 'show_wvs_settings_link_on_admin_bar', false ) ):
					add_action( 'wp_before_admin_bar_render', array( $this, 'add_admin_bar' ), 999 );
				endif;
				
				add_action( 'admin_footer', array( $this, 'admin_inline_js' ) );
				
				if ( apply_filters( 'show_wvs_settings_on_customizer', false ) ):
					new WVS_Customizer( $this->theme_feature_name, $this->plugin_class, $this->settings_name, $this->fields );
				endif;
				
				do_action( 'wvs_setting_api_init', $this );
			}
			
			public function before_update( $value, $option, $old_value ) {
				//if ( $this->settings_name === $option ) {
				// Here We will do magic :D
				// delete_transient( $this->transient_setting_name );
				
				//}
				
				return $value;
			}
			
			public function admin_inline_js() {
				?>
                <script type="text/javascript">
                    jQuery(function ($) {
                        $('#<?php echo $this->slug ?>-wrap').on('click', '.nav-tab', function (event) {
                            event.preventDefault();
                            var target = $(this).data('target');
                            $(this).addClass('nav-tab-active').siblings().removeClass('nav-tab-active');
                            $('#' + target).show().siblings().hide();
                            $('#_last_active_tab').val(target)
                        });
                    });
                </script>
				<?php
			}
			
			public function add_menu() {
				
				if ( empty( $this->fields ) ) {
					return '';
				}
				
				$page_title = esc_html__( 'WooCommerce Variation Swatches Settings', 'woo-variation-swatches' );
				$menu_title = esc_html__( 'Swatches Settings', 'woo-variation-swatches' );
				add_menu_page( $page_title, $menu_title, 'edit_theme_options', $this->slug, array( $this, 'settings_form' ), 'dashicons-admin-generic', 31 );
			}
			
			public function add_admin_bar() {
				
				if ( empty( $this->fields ) ) {
					return '';
				}
				
				global $wp_admin_bar;
				
				$url        = admin_url( sprintf( 'admin.php?page=%s', $this->slug ) );
				$menu_title = esc_html__( 'Swatches Settings', 'woo-variation-swatches' );
				
				$args = array(
					'id'    => $this->settings_name,
					'title' => $menu_title,
					'href'  => $url,
					'meta'  => array(
						'class' => sprintf( '%s-admin-toolbar', $this->slug )
					)
				);
				$wp_admin_bar->add_menu( $args );
				
				if ( ! is_admin() && class_exists( 'WooCommerce' ) && ( is_singular( 'product' ) || is_shop() ) ) {
					$wp_admin_bar->add_menu( array(
						                         'id'     => 'wvs-clear-transient',
						                         'title'  => esc_html__( 'Clear transient', 'woo-variation-swatches' ),
						                         'href'   => esc_url( remove_query_arg( array( 'variation_id', 'remove_item', 'add-to-cart', 'added-to-cart' ), add_query_arg( 'wvs_clear_transient', '' ) ) ),
						                         'parent' => $this->settings_name,
						                         'meta'   => array(
							                         'class' => sprintf( '%s-admin-toolbar-cache', $this->slug )
						                         )
					                         ) );
					
					
				}
				
				do_action( 'wvs_admin_bar_menu', $wp_admin_bar, $this->settings_name );
			}
			
			public function plugin_action_links( $links ) {
				
				if ( empty( $this->fields ) ) {
					return $links;
				}
				
				$url          = admin_url( sprintf( 'admin.php?page=%s', $this->slug ) );
				$plugin_links = array( sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html__( 'Settings', 'woo-variation-swatches' ) ) );
				
				return array_merge( $plugin_links, $links );
			}
			
			private function set_default( $key, $type, $value ) {
				$this->defaults[ $key ] = array( 'id' => $key, 'type' => $type, 'value' => $value );
			}
			
			private function get_default( $key ) {
				return isset( $this->defaults[ $key ] ) ? $this->defaults[ $key ] : null;
			}
			
			public function get_defaults() {
				return $this->defaults;
			}
			
			public function set_defaults() {
				foreach ( $this->fields as $tab_key => $tab ) {
					$tab = apply_filters( 'wvs_settings_tab', $tab );
					
					foreach ( $tab[ 'sections' ] as $section_key => $section ) {
						
						$section = apply_filters( 'wvs_settings_section', $section, $tab );
						
						$section[ 'id' ] = ! isset( $section[ 'id' ] ) ? $tab[ 'id' ] . '-section' : $section[ 'id' ];
						
						$section[ 'fields' ] = apply_filters( 'wvs_settings_fields', $section[ 'fields' ], $section, $tab );
						
						foreach ( $section[ 'fields' ] as $field ) {
							if ( isset( $field[ 'pro' ] ) ) {
								continue;
							}
							$field[ 'default' ] = isset( $field[ 'default' ] ) ? $field[ 'default' ] : null;
							$this->set_default( $field[ 'id' ], $field[ 'type' ], $field[ 'default' ] );
						}
					}
				}
			}
			
			public function get_option( $option ) {
				$default = $this->get_default( $option );
				// $all_defaults = wp_list_pluck( $this->get_defaults(), 'value' );
				
				$options = get_option( $this->settings_name );
				
				$is_new = ( ! is_array( $options ) && is_bool( $options ) );
				
				// Theme Support
				if ( current_theme_supports( $this->theme_feature_name ) ) {
					$theme_support      = get_theme_support( $this->theme_feature_name );
					$default[ 'value' ] = isset( $theme_support[ 0 ][ $option ] ) ? $theme_support[ 0 ][ $option ] : $default[ 'value' ];
				}
				
				if ( $is_new ) {
					// return ( $default[ 'type' ] === 'checkbox' ) ? ( ! ! $default[ 'value' ] ) : $default[ 'value' ];
					return $default[ 'value' ];
				} else {
					// return ( $default[ 'type' ] === 'checkbox' ) ? ( isset( $options[ $option ] ) ? TRUE : FALSE ) : ( isset( $options[ $option ] ) ? $options[ $option ] : $default[ 'value' ] );
					return isset( $options[ $option ] ) ? $options[ $option ] : $default[ 'value' ];
				}
			}
			
			public function update_option( $key, $value ) {
				$options         = get_option( $this->settings_name );
				$options[ $key ] = $value;
				update_option( $this->settings_name, $options );
			}
			
			public function sanitize_callback( $options ) {
				
				foreach ( $this->get_defaults() as $opt ) {
					if ( $opt[ 'type' ] === 'checkbox' && ! isset( $options[ $opt[ 'id' ] ] ) ) {
						$options[ $opt[ 'id' ] ] = 0;
					}
				}
				
				return $options;
			}
			
			public function settings_init() {
				
				register_setting( $this->settings_name, $this->settings_name, array( $this, 'sanitize_callback' ) );
				
				foreach ( $this->fields as $tab_key => $tab ) {
					
					$tab = apply_filters( 'wvs_settings_tab', $tab );
					
					foreach ( $tab[ 'sections' ] as $section_key => $section ) {
						
						$section = apply_filters( 'wvs_settings_section', $section, $tab );
						
						//print_r( $section); die;
						
						$section[ 'id' ] = ! isset( $section[ 'id' ] ) ? $tab[ 'id' ] . '-section-' . $section_key : $section[ 'id' ];
						
						// Adding Settings section id
						$this->fields[ $tab_key ][ 'sections' ][ $section_key ][ 'id' ] = $section[ 'id' ];
						
						add_settings_section( $tab[ 'id' ] . $section[ 'id' ], $section[ 'title' ], function () use ( $section ) {
							if ( isset( $section[ 'desc' ] ) && ! empty( $section[ 'desc' ] ) ) {
								echo '<div class="inside">' . $section[ 'desc' ] . '</div>';
							}
						}, $tab[ 'id' ] . $section[ 'id' ] );
						
						$section[ 'fields' ] = apply_filters( 'wvs_settings_fields', $section[ 'fields' ], $section, $tab );
						
						foreach ( $section[ 'fields' ] as $field ) {
							
							if ( isset( $field[ 'pro' ] ) ) {
								$field[ 'id' ]    = uniqid( 'pro' );
								$field[ 'type' ]  = '';
								$field[ 'title' ] = '';
							}
							
							//$field[ 'label_for' ] = $this->settings_name . '[' . $field[ 'id' ] . ']';
							$field[ 'label_for' ] = $field[ 'id' ] . '-field';
							$field[ 'default' ]   = isset( $field[ 'default' ] ) ? $field[ 'default' ] : null;
							
							// $this->set_default( $field[ 'id' ], $field[ 'default' ] );
							
							if ( $field[ 'type' ] == 'checkbox' || $field[ 'type' ] == 'radio' ) {
								unset( $field[ 'label_for' ] );
							}
							
							add_settings_field( $this->settings_name . '[' . $field[ 'id' ] . ']', $field[ 'title' ], array( $this, 'field_callback' ), $tab[ 'id' ] . $section[ 'id' ], $tab[ 'id' ] . $section[ 'id' ], $field );
							
						}
					}
				}
			}
			
			public function make_implode_html_attributes( $raw_attributes, $except = array( 'type', 'id', 'name', 'value' ) ) {
				$attributes = array();
				foreach ( $raw_attributes as $name => $value ) {
					if ( in_array( $name, $except ) ) {
						continue;
					}
					$attributes[] = esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
				}
				
				return implode( ' ', $attributes );
			}
			
			public function field_callback( $field ) {
				
				switch ( $field[ 'type' ] ) {
					case 'radio':
						$this->radio_field_callback( $field );
						break;
					
					case 'checkbox':
						$this->checkbox_field_callback( $field );
						break;
					
					case 'select':
						$this->select_field_callback( $field );
						break;
					
					case 'number':
						$this->number_field_callback( $field );
						break;
					
					case 'color':
						$this->color_field_callback( $field );
						break;
					
					case 'post_select':
						$this->post_select_field_callback( $field );
						break;
					
					case 'pro':
						$this->pro_field_callback( $field );
						break;
					
					default:
						$this->text_field_callback( $field );
						break;
				}
				
				do_action( 'wvs_settings_field_callback', $field );
			}
			
			public function checkbox_field_callback( $args ) {
				
				$value = (bool) $this->get_option( $args[ 'id' ] );
				// $size  = isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ? $args[ 'size' ] : 'regular';
				
				$attrs = isset( $args[ 'attrs' ] ) ? $this->make_implode_html_attributes( $args[ 'attrs' ] ) : '';
				
				$html = sprintf( '<fieldset><label><input %1$s type="checkbox" id="%2$s-field" name="%4$s[%2$s]" value="%3$s" %5$s/> %6$s</label></fieldset>', $attrs, $args[ 'id' ], true, $this->settings_name, checked( $value, true, false ), esc_attr( $args[ 'desc' ] ) );
				
				echo $html;
			}
			
			public function radio_field_callback( $args ) {
				// $size    = isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ? $args[ 'size' ] : 'regular';
				$options = apply_filters( "wvs_settings_{$args[ 'id' ]}_radio_options", $args[ 'options' ] );
				$value   = esc_attr( $this->get_option( $args[ 'id' ] ) );
				
				$attrs = isset( $args[ 'attrs' ] ) ? $this->make_implode_html_attributes( $args[ 'attrs' ] ) : '';
				
				
				$html = '<fieldset>';
				$html .= implode( '<br />', array_map( function ( $key, $option ) use ( $attrs, $args, $value ) {
					return sprintf( '<label><input %1$s type="radio" id="%2$s-field" name="%4$s[%2$s]" value="%3$s" %5$s/> %6$s</label>', $attrs, $args[ 'id' ], $key, $this->settings_name, checked( $value, $key, false ), $option );
				}, array_keys( $options ), $options ) );
				$html .= $this->get_field_description( $args );
				$html .= '</fieldset>';
				
				echo $html;
			}
			
			public function select_field_callback( $args ) {
				$options = apply_filters( "wvs_settings_{$args[ 'id' ]}_select_options", $args[ 'options' ] );
				$value   = esc_attr( $this->get_option( $args[ 'id' ] ) );
				$options = array_map( function ( $key, $option ) use ( $value ) {
					return "<option value='{$key}'" . selected( $key, $value, false ) . ">{$option}</option>";
				}, array_keys( $options ), $options );
				$size    = isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ? $args[ 'size' ] : 'regular';
				
				$attrs = isset( $args[ 'attrs' ] ) ? $this->make_implode_html_attributes( $args[ 'attrs' ] ) : '';
				
				$html = sprintf( '<select %5$s class="%1$s-text" id="%2$s-field" name="%4$s[%2$s]">%3$s</select>', $size, $args[ 'id' ], implode( '', $options ), $this->settings_name, $attrs );
				$html .= $this->get_field_description( $args );
				
				echo $html;
			}
			
			public function get_field_description( $args ) {
				if ( ! empty( $args[ 'desc' ] ) ) {
					$desc = sprintf( '<p class="description">%s</p>', $args[ 'desc' ] );
				} else {
					$desc = '';
				}
				
				return $desc;
			}
			
			public function post_select_field_callback( $args ) {
				
				$options = apply_filters( "wvs_settings_{$args[ 'id' ]}_post_select_options", $args[ 'options' ] );
				
				$value = esc_attr( $this->get_option( $args[ 'id' ] ) );
				
				$options = array_map( function ( $option ) use ( $value ) {
					return "<option value='{$option->ID}'" . selected( $option->ID, $value, false ) . ">$option->post_title</option>";
				}, $options );
				
				$size = isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ? $args[ 'size' ] : 'regular';
				$html = sprintf( '<select class="%1$s-text" id="%2$s-field" name="%4$s[%2$s]">%3$s</select>', $size, $args[ 'id' ], implode( '', $options ), $this->settings_name );
				$html .= $this->get_field_description( $args );
				echo $html;
			}
			
			public function text_field_callback( $args ) {
				$value = esc_attr( $this->get_option( $args[ 'id' ] ) );
				$size  = isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ? $args[ 'size' ] : 'regular';
				
				$attrs = isset( $args[ 'attrs' ] ) ? $this->make_implode_html_attributes( $args[ 'attrs' ] ) : '';
				
				$html = sprintf( '<input %5$s type="text" class="%1$s-text" id="%2$s-field" name="%4$s[%2$s]" value="%3$s"/>', $size, $args[ 'id' ], $value, $this->settings_name, $attrs );
				$html .= $this->get_field_description( $args );
				
				echo $html;
			}
			
			public function pro_field_callback( $args ) {
				
				$is_html = isset( $args[ 'html' ] );
				
				if ( $is_html ) {
					$html = $args[ 'html' ];
				} else {
					$image = esc_url( $args[ 'screen_shot' ] );
					$link  = esc_url( $args[ 'product_link' ] );
					
					
					$width = isset( $args[ 'width' ] ) ? $args[ 'width' ] : '70%';
					
					$html = sprintf( '<a target="_blank" href="%s"><img style="width: %s" src="%s" /></a>', $link, $width, $image );
					$html .= $this->get_field_description( $args );
				}
				
				
				echo $html;
			}
			
			public function color_field_callback( $args ) {
				$value = esc_attr( $this->get_option( $args[ 'id' ] ) );
				// $size  = isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ? $args[ 'size' ] : 'regular';
				$alpha = isset( $args[ 'alpha' ] ) && $args[ 'alpha' ] === true ? ' data-alpha="true"' : '';
				$html  = sprintf( '<input type="text" %1$s class="wvs-color-picker" id="%2$s-field" name="%4$s[%2$s]" value="%3$s"  data-default-color="%3$s" />', $alpha, $args[ 'id' ], $value, $this->settings_name );
				$html  .= $this->get_field_description( $args );
				
				echo $html;
			}
			
			public function number_field_callback( $args ) {
				$value = esc_attr( $this->get_option( $args[ 'id' ] ) );
				$size  = isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ? $args[ 'size' ] : 'small';
				
				$min    = isset( $args[ 'min' ] ) && ! is_null( $args[ 'min' ] ) ? 'min="' . $args[ 'min' ] . '"' : '';
				$max    = isset( $args[ 'max' ] ) && ! is_null( $args[ 'max' ] ) ? 'max="' . $args[ 'max' ] . '"' : '';
				$step   = isset( $args[ 'step' ] ) && ! is_null( $args[ 'step' ] ) ? 'step="' . $args[ 'step' ] . '"' : '';
				$suffix = isset( $args[ 'suffix' ] ) && ! is_null( $args[ 'suffix' ] ) ? ' <span>' . $args[ 'suffix' ] . '</span>' : '';
				
				$attrs = isset( $args[ 'attrs' ] ) ? $this->make_implode_html_attributes( $args[ 'attrs' ] ) : '';
				
				
				$html = sprintf( '<input %9$s type="number" class="%1$s-text" id="%2$s-field" name="%4$s[%2$s]" value="%3$s" %5$s %6$s %7$s /> %8$s', $size, $args[ 'id' ], $value, $this->settings_name, $min, $max, $step, $suffix, $attrs );
				$html .= $this->get_field_description( $args );
				
				echo $html;
			}
			
			public function settings_form() {
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
				}
				?>
                <div id="<?php echo $this->slug ?>-wrap" class="wrap settings-wrap">

                    <h1><?php echo get_admin_page_title() ?></h1>

                    <form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ) ?>" enctype="multipart/form-data">
						<?php
							settings_errors();
							settings_fields( $this->settings_name );
						?>
						
						<?php $this->options_tabs(); ?>

                        <div id="settings-tabs">
							<?php foreach ( $this->fields as $tab ):
								
								if ( ! isset( $tab[ 'active' ] ) ) {
									$tab[ 'active' ] = false;
								}
								$is_active = ( $this->get_last_active_tab() == $tab[ 'id' ] );
								?>

                                <div id="<?php echo $tab[ 'id' ] ?>"
                                     class="settings-tab wvs-setting-tab"
                                     style="<?php echo ! $is_active ? 'display: none' : '' ?>">
									<?php foreach ( $tab[ 'sections' ] as $section ):
										$this->do_settings_sections( $tab[ 'id' ] . $section[ 'id' ] );
									endforeach; ?>
                                </div>
							
							<?php endforeach; ?>
                        </div>
						<?php
							$this->last_tab_input();
							submit_button();
						?>
                    </form>
                </div>
				<?php
			}
			
			private function last_tab_input() {
				printf( '<input type="hidden" id="_last_active_tab" name="%s[_last_active_tab]" value="%s">', $this->settings_name, $this->get_last_active_tab() );
			}
			
			public function options_tabs() {
				?>
                <h2 class="nav-tab-wrapper wp-clearfix">
					<?php foreach ( $this->fields as $tabs ): ?>
                        <a data-target="<?php echo $tabs[ 'id' ] ?>" <?php echo $this->get_options_tab_pro_attr( $tabs ) ?> class="wvs-setting-nav-tab nav-tab <?php echo $this->get_options_tab_css_classes( $tabs ) ?> " href="#<?php echo $tabs[ 'id' ] ?>"><?php echo $tabs[ 'title' ] ?></a>
					<?php endforeach; ?>
                </h2>
				<?php
			}
			
			private function get_options_tab_pro_attr( $tabs ) {
				return ( isset( $tabs[ 'is_pro' ] ) && $tabs[ 'is_pro' ] ) ? sprintf( 'data-pro-text="%s"', apply_filters( 'wvs_settings_tab_pro_text', 'Pro' ) ) : false;
			}
			
			private function get_options_tab_css_classes( $tabs ) {
				$classes = array();
				
				$classes[] = ( $this->get_last_active_tab() == $tabs[ 'id' ] ) ? 'nav-tab-active' : '';
				
				// $classes[] = ( $this->get_options_tab_pro_attr( $tabs ) ) ? 'pro-tab' : '';
				
				return implode( ' ', array_unique( apply_filters( 'get_options_tab_css_classes', $classes ) ) );
			}
			
			
			private function get_last_active_tab() {
				
				$last_tab = trim( $this->get_option( '_last_active_tab' ) );
				
				if ( isset( $_GET[ 'tab' ] ) && ! empty( $_GET[ 'tab' ] ) ) {
					$last_tab = trim( $_GET[ 'tab' ] );
				}
				
				$default_tab = '';
				foreach ( $this->fields as $tabs ) {
					if ( isset( $tabs[ 'active' ] ) && $tabs[ 'active' ] ) {
						$default_tab = $tabs[ 'id' ];
						break;
					}
				}
				
				return ! empty( $last_tab ) ? $last_tab : $default_tab;
				
			}
			
			private function do_settings_sections( $page ) {
				global $wp_settings_sections, $wp_settings_fields;
				
				if ( ! isset( $wp_settings_sections[ $page ] ) ) {
					return;
				}
				
				foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
					if ( $section[ 'title' ] ) {
						echo "<h2>{$section['title']}</h2>\n";
					}
					
					if ( $section[ 'callback' ] ) {
						call_user_func( $section[ 'callback' ], $section );
					}
					
					if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section[ 'id' ] ] ) ) {
						continue;
					}
					
					echo '<table class="form-table">';
					$this->do_settings_fields( $page, $section[ 'id' ] );
					echo '</table>';
				}
			}
			
			public function array2html_attr( $attributes, $do_not_add = array() ) {
				
				$attributes = wp_parse_args( $attributes, array() );
				
				if ( ! empty( $do_not_add ) and is_array( $do_not_add ) ) {
					foreach ( $do_not_add as $att_name ) {
						unset( $attributes[ $att_name ] );
					}
				}
				
				
				$attributes_array = array();
				
				foreach ( $attributes as $key => $value ) {
					
					if ( is_bool( $attributes[ $key ] ) and $attributes[ $key ] === true ) {
						return $attributes[ $key ] ? $key : '';
					} elseif ( is_bool( $attributes[ $key ] ) and $attributes[ $key ] === false ) {
						$attributes_array[] = '';
					} else {
						$attributes_array[] = $key . '="' . $value . '"';
					}
				}
				
				return implode( ' ', $attributes_array );
			}
			
			private function build_dependency( $require_array ) {
				$b_array = array();
				foreach ( $require_array as $k => $v ) {
					$b_array[ '#' . $k . '-field' ] = $v;
				}
				
				return 'data-depends="[' . esc_attr( wp_json_encode( $b_array ) ) . ']"';
			}
			
			private function do_settings_fields( $page, $section ) {
				global $wp_settings_fields;
				
				if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
					return;
				}
				
				foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {
					/*$class = '';

					if ( ! empty( $field[ 'args' ][ 'class' ] ) ) {
						$class = ' class="' . esc_attr( $field[ 'args' ][ 'class' ] ) . '"';
					}*/
					
					$custom_attributes = $this->array2html_attr( isset( $field[ 'args' ][ 'attributes' ] ) ? $field[ 'args' ][ 'attributes' ] : array() );
					
					$wrapper_id = ! empty( $field[ 'args' ][ 'id' ] ) ? esc_attr( $field[ 'args' ][ 'id' ] ) . '-wrapper' : '';
					$dependency = ! empty( $field[ 'args' ][ 'require' ] ) ? $this->build_dependency( $field[ 'args' ][ 'require' ] ) : '';
					
					printf( '<tr id="%s" %s %s>', $wrapper_id, $custom_attributes, $dependency );
					
					if ( isset( $field[ 'args' ][ 'pro' ] ) ) {
						echo '<td colspan="2" style="padding: 0; margin: 0">';
						$this->pro_field_callback( $field[ 'args' ] );
						echo '</td>';
					} else {
						
						if ( ! empty( $field[ 'args' ][ 'label_for' ] ) ) {
							echo '<th scope="row"><label for="' . esc_attr( $field[ 'args' ][ 'label_for' ] ) . '">' . $field[ 'title' ] . '</label></th>';
						} else {
							echo '<th scope="row">' . $field[ 'title' ] . '</th>';
						}
						
						echo '<td>';
						call_user_func( $field[ 'callback' ], $field[ 'args' ] );
						echo '</td>';
					}
					
					echo '</tr>';
				}
			}
		}
	endif;