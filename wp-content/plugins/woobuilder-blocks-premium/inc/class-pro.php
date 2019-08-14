<?php

class WooBuilder_Pro {

	/** @var WooBuilder_Pro Instance */
	private static $_instance;
	/** @var string Content from tpl */
	private $matching_tpl;

	/**
	 * Returns instance of current calss
	 * @return WooBuilder_Pro Instance
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {
		add_filter( 'init', [ $this, 'init' ] );
		add_filter( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_filter( 'woocommerce_taxonomy_objects_product_cat', [ $this, 'add_product_templates' ] );
		add_filter( 'woocommerce_taxonomy_objects_product_tag', [ $this, 'add_product_templates' ] );
		add_filter( 'woobuilder_templates', [ $this, 'templates' ] );
		add_filter( 'enqueue_block_editor_assets', [ $this, 'woobuilder_inline_js' ] );
		add_filter( 'wp_ajax_woobuilder_save_template', [ $this, 'ajax_save_template' ] );
		add_filter( 'wp_head', [ $this, 'maybe_apply_template' ] );
		add_filter( 'manage_woobuilder_template_posts_columns', [ $this, 'custom_columns' ] );
		add_action( 'manage_woobuilder_template_posts_custom_column' , [ $this, 'custom_column_data' ], 10, 2 );
		add_action( 'admin_print_styles' , [ $this, 'admin_styles' ], 10, 2 );
	}

	/**
	 * Admin styles to hide taxonomies under woobuilder templates post type.
	 */
	public function admin_styles() {
		?>
		<style>
			a.page-title-action[href*='post-new.php?post_type=woobuilder_template'],
			#adminmenu .wp-submenu a[href*="post_type=woobuilder_template"][href*="edit-tags.php?taxonomy=product_"] {
				display: none;
			}
            body.edit-php.post-type-woobuilder_template a.page-title-action {
                display: none;
            }
        </style>
		<?php
	}

	/**
	 * Adds custom columns on woobuilcer templates post type.
	 * @param array $columns
	 * @return array
	 */
	public function custom_columns( $columns ) {
		$date = $columns['date'];

		unset( $columns['date'] );
		$columns['tpl-cats'] = 'Categories';
		$columns['tpl-tags'] = 'Tags';
		$columns['date'] = $date;
		return $columns;
	}

	/**
	 * Add content to custom columns on woobuilder templates post type columns.
	 * @param string $column
	 * @param int $post_id
	 */
	public function custom_column_data( $column, $post_id ) {
		switch ( $column ) {
			case 'tpl-cats' :
				echo implode( ', ', wp_list_pluck( get_the_terms( $post_id, 'product_cat' ), 'name' ) );
				break;
			case 'tpl-tags' :
				echo implode( ', ', wp_list_pluck( get_the_terms( $post_id, 'product_tag' ), 'name' ) );
				break;
		}
	}

	/**
	 * Adds Woobuilder templates to product cats and tags
	 * @param array $post_types
	 * @return array
	 */
	public function add_product_templates( $post_types ) {
		$post_types[] = 'woobuilder_template';

		return $post_types;
	}

	/**
	 * Registers WooBuilder template post type
	 */
	public function init() {
		register_post_type( 'woobuilder_template', [
			'public'       => false,
			'label'        => 'Product templates',
			'labels' => array(
				'name'               => __( 'Product templates', 'woobuilder-blocks' ),
				'singular_name'      => __( 'Product template', 'woobuilder-blocks' ),
				'menu_name'          => __( 'Product templates', 'woobuilder-blocks' ),
				'name_admin_bar'     => __( 'Product template', 'woobuilder-blocks' ),
				'add_new'            => __( 'Add New', 'woobuilder-blocks' ),
				'add_new_item'       => __( 'Add New Product template', 'woobuilder-blocks' ),
				'new_item'           => __( 'New Product template', 'woobuilder-blocks' ),
				'edit_item'          => __( 'Edit Product template', 'woobuilder-blocks' ),
				'view_item'          => __( 'View Product template', 'woobuilder-blocks' ),
				'all_items'          => __( 'All Templates', 'woobuilder-blocks' ),
				'search_items'       => __( 'Search Product templates', 'woobuilder-blocks' ),
				'parent_item_colon'  => __( 'Parent Product templates:', 'woobuilder-blocks' ),
				'not_found'          => __( 'No product templates found.', 'woobuilder-blocks' ),
				'not_found_in_trash' => __( 'No product templates found in Trash.', 'woobuilder-blocks' ),
			),
			'show_ui'      => true,
			'show_in_menu' => 'edit.php?post_type=product',
			'show_in_admin_bar' => false,
		] );
	}

	/**
	 * Adds metabox for woobuilder post type help
	 */
	public function add_meta_boxes() {
		if( 'woobuilder_template' === get_post_type() ) {
			add_meta_box(
				'woobuilder_template_metabox',
				'Product template',
				[ $this, 'render_meta_box' ],
				null,
				'advanced',
				'low'
			);
		}
	}

	/**
	 * Render post meta box for WooBuilder templates
	 * @param WP_Post $post
	 */
	public function render_meta_box( $post ) {
		?>
		<style>
			#woobuilder_template_metabox h2.woobuilder-template-helptext {
				font-size: 18px;
				font-weight: 300;
				padding: 1em 0 0;
			}

			#woobuilder_template_metabox .woobuilder-template-links {
				color: inherit;
				text-decoration: none;
				border-bottom: 1px dotted;
			}

			#woobuilder_template_metabox h3 {
				margin: 1.6em 0 .5em;
			}

			#woobuilder_template_metabox ul {
				margin-top: 0;
			}
			#product_catdiv,
			#tagsdiv-product_tag {
				transition: all .5s;
			}
			#product_catdiv:target,
			#tagsdiv-product_tag:target {
				transform: scale( 1.06 );
				box-shadow: 1px 2px 5px rgba(0, 0, 0, 0.25);
			}
			.post-type-woobuilder_template #postdivrich {
				display: none;
			}
		</style>
		<h2 class="woobuilder-template-helptext">Select the
			<a class="woobuilder-template-links" href="#product_catdiv">Product Categories</a> and
			<a class="woobuilder-template-links" href="#tagsdiv-product_tag">Product Tags</a>
			you would like to apply this template to...</h2>

			<?php echo get_the_term_list(
				$post->ID, 'product_cat',
				'<h3>This template will apply to product with any of the following categories:</h3><ul class="ul-disc"><li>',
				'</li><li>',
				'</li></ul>'
			); ?>

			<?php echo get_the_term_list(
				$post->ID, 'product_tag',
				'<h3>This template will apply to product with any of the following tags:</h3><ul class="ul-disc"><li>',
				'</li><li>',
				'</li></ul>'
			); ?>
		<?php
	}

	/**
	 * AJAX handler to save templates
	 */
	public function ajax_save_template() {

		$title = $_POST['title'];
		$post_id = wp_insert_post( [
			'post_title'   => $title,
			'post_content' => $_POST['tpl'],
			'post_type'    => 'woobuilder_template',
			'post_status'  => 'publish',
		] );

		die( "Successfully saved template '$title'." );
	}

	private $template_weight = [
		'product_cat' => 2,
	];

	/**
	 * Gets templates matching specified taxonomy terms for current post
	 * @param string $taxonomy
	 * @param array $templates
	 * @param array $tpl_html
	 */
	private function get_templates( $taxonomy, &$templates, &$tpl_html ) {
		$terms = get_the_terms( get_the_ID(), $taxonomy );

		if ( $terms ) {
			$terms = wp_list_pluck( $terms, 'term_id' );

			$tpl_matched = get_posts( [
				'post_type' => 'woobuilder_template',
				'tax_query' => [
					[
						'terms'    => $terms,
						'taxonomy' => $taxonomy,
					],
					'relation' => 'OR',
				],
				'orderby'  => 'ID',
				'order'    => 'desc',
			] );

			if ( $tpl_matched ) {
				foreach ( $tpl_matched as $p ) {
					$tpl_html[ $p->ID ] = $p->post_content;
					$templates[ $p->ID ] += isset( $this->template_weight[ $taxonomy ] ) ? $this->template_weight[$taxonomy] : 1;
				}
			}
		}
	}

	/**
	 * @return mixed|string HTML for matched template
	 */
	public function get_matching_template() {
		$tpl_html = $templates = [];

		$this->get_templates( 'product_cat', $templates, $tpl_html );

		$this->get_templates( 'product_tag', $templates, $tpl_html );
		arsort( $templates, SORT_NUMERIC );

		if ( ! $templates ) {
			return '';
		}
		foreach ( $templates as $tpl_id => $score ) {
			return $tpl_html[ $tpl_id ];
		}
	}

	/**
	 * Applies template when match found.
	 */
	public function maybe_apply_template() {
		if ( is_product() && ! WooBuilder_Blocks::enabled() ) {
			$this->matching_tpl = $this->get_matching_template();
			if ( $this->matching_tpl ) {
				remove_action( 'woobuilder_render_product', 'the_content' );
				add_action( 'woobuilder_render_product', [ $this, 'render_template' ] );
				add_action( 'woobuilder_render_product', [ wc()->structured_data, 'generate_product_data' ] );

				add_filter( 'wc_get_template_part', [ WooBuilder_Blocks::instance()->public, 'wc_get_template_part' ], 1001, 3 );
				add_filter( 'woocommerce_product_tabs', [ $this, 'product_tabs' ], 11, 3 );
			}
		}
	}

	public function product_tabs( $tabs ) {
		$tabs['description'] = array(
			'title'    => __( 'Description', 'woocommerce' ),
			'priority' => 10,
			'callback' => 'woocommerce_product_description_tab',
		);

		return $tabs;
	}

	/**
	 * Adds hook to render the template
	 */
	public function render_template() {
		echo apply_filters( 'the_content', apply_filters( 'get_the_content', $this->matching_tpl ) );
	}

	public function woobuilder_inline_js( $vars ) {
		$url = admin_url( 'admin-ajax.php?action=woobuilder_save_template' );
		wp_add_inline_script( 'woobuilder-blocks-js', <<<JS
			jQuery( function() {
				wp.plugins.registerPlugin( 'woobuilder-pro', {
					render: function() {
						var el = wp.element.createElement;
						return el(
							wp.editPost.PluginPostStatusInfo,
							{
								className: 'woobuilder-save-template'
							},
							el(
								'a',
								{
									id: 'woobuilder-save-template-btn',
									className: 'button-link-delete components-button editor-post-trash is-button is-default is-large',
									style    : {
										color: '#0073aa'
									},
									onClick  : function () {
										var name = prompt( 'What would you like to call this template?' );
										if ( name ) {
											document.getElementById( 'woobuilder-save-template-btn' ).classList.add( 'is-busy' );
											console.log( name );
				
											jQuery.post(
												'$url', {
													title: name,
													tpl: wp.data.select( "core/editor" ).getEditedPostContent(),
												}, function( resp ) {
													alert( resp );
													document.getElementById( 'woobuilder-save-template-btn' ).classList.remove( 'is-busy' );
												}
											);
										}
									},
								},
								'Save as template'
							),
						)
					},
				} );
			} );
JS
		 );
	}

	public function templates( $templates ) {

		$template_posts = get_posts( [
			'post_type' => 'woobuilder_template',
		] );

		/** @var WP_Post $tpl_post */
		foreach ( $template_posts as $tpl_post ) {
			$templates[ $tpl_post->ID ] = [
				'title' => $tpl_post->post_title,
				'tpl' => $tpl_post->post_content,
			];
		}

		return $templates;
	}

}

WooBuilder_Pro::instance();
