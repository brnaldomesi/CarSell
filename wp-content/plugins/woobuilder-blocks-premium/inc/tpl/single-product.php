<?php
global $post, $product;
$product = wc_get_product( $post );
?>
<div class="product woobuilder">
	<?php do_action( 'woobuilder_render_product', $product, $post ); ?>
</div>
