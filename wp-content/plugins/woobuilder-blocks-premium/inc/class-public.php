<?php

/**
 * WooBuilder blocks public class
 */
class WooBuilder_Blocks_Public {

	/** @var WooBuilder_Blocks_Public Instance */
	private static $_instance = null;

	/* @var string $token Plugin token */
	public $token;

	/* @var string $url Plugin root dir url */
	public $url;

	/* @var string $path Plugin root dir path */
	public $path;

	/* @var string $version Plugin version */
	public $version;
	private $product_description;

	/**
	 * WooBuilder blocks public class instance
	 * @return WooBuilder_Blocks_Public instance
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor function.
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct() {
		$this->token   = WooBuilder_Blocks::$token;
		$this->url     = WooBuilder_Blocks::$url;
		$this->path    = WooBuilder_Blocks::$path;
		$this->version = WooBuilder_Blocks::$version;
	}

	// region WooBuilder product frontend setup

	public function setup_product_render() {
		add_action( 'woobuilder_render_product', 'the_content' );
		add_action( 'woobuilder_render_product', [ wc()->structured_data, 'generate_product_data' ] );
	}

	/**
	 * Sets up WooBuilder for single product when enabled.
	 */
	public function maybe_setup_woobuilder_product() {
		if ( WooBuilder_Blocks::enabled() ) {
			// Priority more than storefront pro 999
			add_filter( 'wc_get_template_part', array( $this, 'wc_get_template_part' ), 1001, 3 );
			add_filter( 'woocommerce_product_tabs', array( $this, 'product_tabs' ), 11, 3 );
		}
	}

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 * @since 1.0.0
	 */
	public function wc_get_template_part( $template, $slug, $name ) {
		if (
			'content' == $slug &&
			'single-product' == $name
		) {
			return dirname( __FILE__ ) . '/tpl/single-product.php';
		}

		return $template;
	}

	/**
	 * Removes description tab to avoid potential recursion.
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function product_tabs( $tabs ) {

		unset( $tabs['description'] );

		if ( $this->product_description ) {
			$tabs['description'] = array(
				'title'    => __( 'Description', 'woocommerce' ),
				'priority' => 10,
				'callback' => [ $this, 'product_description_tab' ],
			);
		}

		return $tabs;
	}

	public function product_description_tab() {
		echo $this->product_description;
	}

	// endregion WooBuilder product frontend setup

	public function register_blocks() {

		$blocks = WooBuilder_Blocks::blocks();

		foreach ( $blocks as $block ) {
			register_block_type(
				str_replace( '_', '-', "woobuilder/$block" ),
				[ 'render_callback' => [ $this, "render_$block" ] ]
			);
		}
	}

	public function enable_rest_taxonomy( $args ) {
		$args['show_in_rest'] = true;

		return $args;
	}

	private function openWrap( $props, $class, $tag = 'div', $style = '' ) {

		if ( ! empty( $props['font_size'] ) ) {
			$style .= "font-size:{$props['font_size']}px;";
		}
		if ( ! empty( $props['font'] ) ) {
			$props['font'] = stripslashes( $props['font'] );
			$style         .= "font-family:{$props['font']};";
		}
		if ( ! empty( $props['text_color'] ) ) {
			$style .= "color:{$props['text_color']};";
		}
		if ( ! empty( $props['woobuilder_style'] ) ) {
			$class .= " woobuilder-style-$props[woobuilder_style]";
		}

		if ( $style ) {
			$style = 'style="' . $style . '"';
		}

		return "<$tag class='woobuilder-block woobuilder-$class' $style>";
	}

	public function render_title( $props ) {
		ob_start();

		return $this->openWrap( $props, 'title entry-title', 'h1' ) . get_the_title() . '</h1>';
	}

	public function render_rating( $props ) {
		global $product;

		if ( ! $product ) return '';

		ob_start();
		echo $this->openWrap( $props, 'rating' );
		$rating_count = $product->get_rating_count();
		$review_count = $product->get_review_count();
		$average      = $product->get_average_rating();
		?>
        <div class="woobuilder-product-rating">
			<?php echo wc_get_rating_html( $average, $rating_count ); ?>
			<?php if ( $rating_count > 0 && comments_open() ) : ?><a href="#reviews" class="woobuilder-review-link"
                                                                     rel="nofollow">
                (<?php printf( _n( '%s customer review', '%s customer reviews', $review_count, 'woocommerce' ), '<span class="count">' . esc_html( $review_count ) . '</span>' ); ?>
                )</a><?php endif ?>
        </div>
		<?php
		echo '</div>';

		return ob_get_clean();
	}

	public function render_add_to_cart( $props ) {
		global $product;

		if ( ! $product ) return '';

		ob_start();
		echo $this->openWrap( $props, 'add-to-cart' );
		woocommerce_template_single_add_to_cart();
		echo '</div>';

		return ob_get_clean();
	}

	public function render_sale_counter( $props ) {
		global $product;

		if ( ! $product ) return '';

		/** @var WC_Product $product */
		// Declare and define two dates
		$date1 = strtotime( $product->get_date_on_sale_to() );
		$diff  = $date1 - time();

		if ( ! $diff || $diff < 5 ) {
			return '<div></div>';
		}

		$props = wp_parse_args( $props, [
			'active_color' => '#555',
			'track_color' => '#ddd',
			'track_width' => '2',
		] );

		ob_start();

		echo $this->openWrap( $props, 'sale_counter_wrap' );
		echo "<div class='woobuilder-sale_counter' data-date-end='$date1'>";

		$days = floor( $diff / ( 60 * 60 * 24 ) );

		$hours = floor( $diff % (60 * 60 * 24) / ( 60 * 60 ) );

		$minutes = floor( $diff % (60 * 60) / 60 );

		$seconds = floor( $diff % 60 );

		$r = 15.9154; // 100/2PI
		$center = $r + $props['track_width'] / 2;

		$width = 2 * $center;


		$circle_attrs = "cx=$center cy=$center r='{$r}' stroke-width='{$props['track_width']}' " .
										"style='transform-origin:50%% 50%%;transform:rotate(-90deg);' fill='none'";

		$format =
			'<div class="woob-timr woob-timr-%1$s">' .
			"<svg viewBox='0 0 $width $width'>" .
			"<circle $circle_attrs stroke='{$props['track_color']}' />" .
			"<circle $circle_attrs stroke='{$props['active_color']}' class='woob-timr-arc-%1\$s' />" .
			'</svg>' .
			'<div class="woob-timr-number-%1$s woob-timr-number">%3$s</div>' .
			'<div class="woob-timr-label">%4$s</div>' .
			'</div>';

		echo $days ? sprintf( $format, 'days', $days * 100 / 31, $days, _n( 'day', 'days', $days ) ) : '';

		echo sprintf( $format, 'hours', $hours * 100 / 24, $hours, _n( 'hour', 'hours', $hours ) );

		echo sprintf( $format, 'minutes', $minutes * 100 / 60, $minutes, _n( 'minute', 'minutes', $minutes ) );

		echo sprintf( $format, 'seconds', $seconds * 100 / 60, $seconds, _n( 'second', 'seconds', $seconds ) );

		echo '</div></div>';

		return ob_get_clean();
	}

	public function render_related_products( $props ) {
		global $product;

		if ( ! $product ) return '';

		ob_start();
		echo $this->openWrap( $props, 'related_products' );
		woocommerce_related_products();
		echo '</div>';

		return ob_get_clean();
	}

	public function render_product_price( $props ) {
		global $product;

		if ( ! $product ) return '';


		return $this->openWrap( $props, 'price' ) . $product->get_price_html() . '</div>';
	}

	public function render_excerpt( $props ) {
		global $product, $post;

		if ( ! $product ) return '';

		$short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );

		return $this->openWrap( $props, 'excerpt' ) . $short_description . '</div>';
	}

	public function render_meta( $props ) {
		global $product;

		if ( ! $product ) return '';

		ob_start();
		echo $this->openWrap( $props, 'meta' );
		$metadata = '';
		$sku      = $product->get_sku();
		if ( $sku ) {
			$metadata .= "<span class='woobuilder-sku'>SKU: $sku</span> ";
		}
		$metadata .= wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span> ' );
		$metadata .= wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . ' ', '</span> ' );
		echo apply_filters( 'woobuilder_product_meta', $metadata );
		echo '</div>';

		return ob_get_clean();
	}

	public function render_reviews( $props ) {
		global $product;

		if ( ! $product ) return '';

		ob_start();
		echo $this->openWrap( $props, 'reviews' );
		comments_template();
		echo '</div>';

		return ob_get_clean();
	}

	public function render_images( $props ) {
		global $product;

		if ( ! $product ) return '';

		ob_start();
		echo $this->openWrap( $props, 'images' );
		woocommerce_show_product_images();
		echo '</div>';

		return ob_get_clean();
	}

	public function render_images_carousel( $props ) {
		global $product;

		if ( ! $product ) return '';

		if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
			return '';
		}

		ob_start();
		echo $this->openWrap( $props, 'images_carousel flexslider o-0' );
		$slide_attachments = $product->get_gallery_image_ids();
		array_splice( $slide_attachments, 0, 0, + $product->get_image_id() );
		?>
		<ul class="slides">
			<?php
			if ( $slide_attachments ) {
				foreach ( $slide_attachments as $attachment ) {
					echo '<li>';
					echo wp_get_attachment_image( $attachment, 'large' );
					echo '</li>';
				}
			}
			?>
		</ul>
		<div class="woobuilder-images_carousel-navigation">
			<a href="#" class="flex-prev"></a>
			<a href="#" class="flex-next"></a>
		</div>
		<?php
		echo '</div>';
		return ob_get_clean();
	}

	public function render_tabs( $props ) {
		global $product;

		if ( ! $product ) return '';

		$this->product_description = $props['desc'];
		ob_start();
		echo $this->openWrap( $props, 'tabs' );
		woocommerce_output_product_data_tabs();
		echo '</div>';

		return ob_get_clean();
	}

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function enqueue() {
		global $post;

		if ( $post->post_type != 'product' ) {
			return;
		}

		$token = $this->token;
		$url   = $this->url;
		$ver   = $this->version;

		wp_enqueue_style( $token . '-css', $url . '/assets/front.css', null, $ver );
		wp_enqueue_script( $token . '-utils', $url . '/assets/utils.js', [ 'jquery' ], $ver );
	}
}
