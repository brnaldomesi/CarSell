<?php

/**
 * WooBuilder blocks Admin class
 */
class WooBuilder_Blocks_Admin {

	/** @var WooBuilder_Blocks_Admin Instance */
	private static $_instance = null;

	/* @var string $token Plugin token */
	public $token;

	/* @var string $url Plugin root dir url */
	public $url;

	/* @var string $path Plugin root dir path */
	public $path;

	/* @var string $version Plugin version */
	public $version;

	/**
	 * Constructor function.
	 * @access  private
	 * @since  1.0.0
	 */
	private function __construct() {
		$this->token   = WooBuilder_Blocks::$token;
		$this->url     = WooBuilder_Blocks::$url;
		$this->path    = WooBuilder_Blocks::$path;
		$this->version = WooBuilder_Blocks::$version;
	} // End instance()

	/**
	 * Main WooBuilder blocks Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @return WooBuilder_Blocks_Admin instance
	 * @since  1.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	} // End __construct()

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function enqueue() {
		global $post;

		if ( ! in_array( $post->post_type, [ 'product', 'woobuilder_template' ] ) ) {
			return;
		}

		$token = $this->token;
		$url   = $this->url;
		$ver   = $this->version;

		wp_enqueue_style( $token . '-css', $url . '/assets/admin.css', null, $ver );

		wp_enqueue_script( $token . '-utils', $url . '/assets/utils.js', [ 'jquery' ], $ver );

		wp_enqueue_script( $token . '-js', $url . '/assets/admin.js', [ 'jquery' ], $ver );

		wp_localize_script(
			$token . '-js',
			'woobuilderData',
			apply_filters( 'woobuilder_js_vars', [
					'post'                     => $post->ID,
					'switchToDefaultEditorUrl' => add_query_arg( 'toggle-woobuilder', 'false' ),
					'assets_url' => $url . '/assets/',
					'img_url' => $url . '/assets/img/',
				]
			)
		);
	}


	function rest_api_init() {

		$routes = WooBuilder_Blocks::blocks();

		foreach ( $routes as $route ) {
			$callback = [ $this, "api_$route" ];
			if ( ! is_callable( $callback ) ) {
				$callback = function () use ( $route ) {
					return $this->handle_product_endpoint( $route );
				};
			}

			register_rest_route( 'woobuilder_blocks/v1', "/$route", array(
				'methods'  => 'GET',
				'callback' => $callback,
			) );
		}
	}

	private function handle_product_endpoint( $endpoint ) {
		$public = WooBuilder_Blocks::instance()->public;

		$function = "render_$endpoint";

		$query = new WP_Query( [
			'p'           => $_REQUEST['post'],
			'post_type'   => 'product',
			'post_status' => array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash' ),
		] );

		$GLOBALS['wp_query'] = $query;

		global $product, $post;

		$query->the_post();
		$product = wc_get_product( $post );

		add_filter( 'woobuilder_product_meta', [ $this, 'admin_meta_notice' ] );
		add_action( 'woocommerce_product_get_rating_html', [ $this, 'rating_for_wp_admin' ], 10, 3 );

		if ( ! has_action( 'woocommerce_simple_add_to_cart' ) ) {
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			add_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
			add_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
			add_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
			add_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
			add_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
			add_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
		}

		if ( method_exists( $this, $function ) ) {
			return $this->$function( $_REQUEST );
		}

		return $public->$function( $_REQUEST );
	}

	public function rating_for_wp_admin( $html, $rating, $reviews_count ) {

		if ( ! $reviews_count ) {
			return $this->notice( 'Rating will be displayed here after reviews are done.' );
		}

		$html = '';

		for ( $i = 1; $i < 6; $i ++ ) {
			if ( $rating - $i > .8 ) {
				$html .= '<span class="dashicons dashicons-star-filled"></span>';
			} else if ( $rating - $i > .1 ) {
				$html .= '<span class="dashicons dashicons-star-half"></span>';
			} else {
				$html .= '<span class="dashicons dashicons-star-empty"></span>';
			}
		}
		return $html;
	}

	private function notice( $msg, $type = 'info' ) {
		return "<div class='notice notice-$type notice-large'>$msg</div>";
	}

	public function admin_meta_notice( $meta_html ) {
		if ( ! $meta_html ) {
			return $this->notice( 'Product categories, tags and SKU will be displayed here once added.' );
		}

		return $meta_html;
	}

	public function render_tabs() {
		return
			$this->notice( 'Product tabs will be displayed here on product page.' );
	}

	public function render_reviews() {
		return $this->notice( 'Product reviews will be displayed here on product page.' );
	}

	public function render_related_products() {
		return $this->notice( 'Related products will be displayed here on product page.' );
	}

	public function block_categories( $categories ) {
		$categories[] = [
			'slug'  => 'woobuilder',
			'title' => __( 'Woobuilder', 'woobuilder-blocks' ),
		];

		return $categories;
	}

	/**
	 * Enables Gutenberg on products
	 *
	 * @param bool $can_edit
	 * @param string $post_type
	 *
	 * @return bool Enable gutenberg
	 */
	function enable_gutenberg_products( $can_edit, $post_type ) {
		if ( 'woobuilder_template' == $post_type ) {
			return true;
		}

		if ( isset( $_GET['toggle-woobuilder'] ) ) {

			if ( ! $_GET['toggle-woobuilder'] || $_GET['toggle-woobuilder'] == 'false' ) {
				$_GET['toggle-woobuilder'] = false;
				delete_post_meta( get_the_ID(), 'woobuilder_template_applied' );
				delete_post_meta( get_the_ID(), 'woobuilder' );
			} else {
				add_filter( 'get_the_content', [ $this, 'maybe_apply_tpl' ] );
			}

			update_post_meta( get_the_ID(), 'woobuilder', $_GET['toggle-woobuilder'] );

			return $_GET['toggle-woobuilder'];
		}

		return 'product' === $post_type ? WooBuilder_Blocks::enabled() : $can_edit;
	}

	public function block_editor_settings( $settings ) {
		$template = WooBuilder_Blocks::template( WooBuilder_Blocks::template_id() );

		if ( $template ) {
//			$settings['template'] = $template['tpl'];
		}

		return $settings;
	}

	public function rest_request_after_callbacks( $response, $handler, $request ) {
		if ( '/wp/v2/' . get_post_type() . '/' . get_the_ID() == $request->get_route() && isset( $_GET['toggle-woobuilder'] ) ) {
			if ( ! get_post_meta( get_the_ID(), 'woobuilder_template_applied', 'single' ) ) {
				$template = WooBuilder_Blocks::template( $_GET['toggle-woobuilder'] );
				if ( ! empty( $template['tpl'] ) ) {
					$response->data['content']['raw'] = $template['tpl'];
					update_post_meta( get_the_ID(), 'woobuilder_template_applied', 1 );
				}
			}
		}

		return $response;
	}

	public function admin_footer( $post ) {
		include 'tpl/admin-template-picker.php';
	}

	/**
	 * Adds admin only actions
	 * @action admin_init
	 *
	 * @param WP_Post $post
	 */
	public function product_meta_fields( $post ) {
		if ( 'product' !== $post->post_type ) {
			return;
		}
		?>
		<div class="clear misc-pub-section">
			<a href="#woobuilder-enable-dialog" class="button button-primary" id="toggle-woobuilder">
				<?php _e( 'Enable WooBuilder Blocks', $this->token ); ?></a>
			<?php

			//			if ( 'auto-draft' === get_post_status() ) {
			$templates = WooBuilder_Blocks::templates();

			if ( $templates && 'auto-draft' != get_post_status() ) {
				?>
				<a href="#woobuilder-template-picker" class="button button-link" id="pick-woobuilder-tpl">
					<?php _e( 'Use WooBuilder with template', $this->token ); ?></a>
				<?php
			}
			//			}
			?>
		</div>
		<?php
	}

	public function save_post( $post ) {

	}

	private function maybe_apply_tpl( $content ) {
		$template = WooBuilder_Blocks::template( $_GET['toggle-woobuilder'] );

		if ( $template ) {
			return $template['tpl'];
		}

		return $content;
	}
}