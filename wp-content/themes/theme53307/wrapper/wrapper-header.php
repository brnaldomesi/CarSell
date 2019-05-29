<?php /* Wrapper Name: Header */ ?>
<div class="row">
    <div class="box1">
    	<div class="span6" data-motopress-type="static">
    		<p><span class="ic">Free Shipping on orders over $99.</span> This offer is valid on all our store items. </p>
    	</div>
    	<div class="span6" data-motopress-type="static" data-motopress-static-file="static/static-woocommerce_account.php">
    		<?php get_template_part("static/static-woocommerce_account"); ?>
    	</div>
    </div>
    <div class="box2">
        <div class="span6" data-motopress-type="static" data-motopress-static-file="static/static-shop-nav.php">
			<?php get_template_part("static/static-logo"); ?>
		</div>
        <div class="span5 info_box" data-motopress-type="static">
            <p class="title">Free phone: <strong>800-2345-6789</strong></p>
            <span>Hours: 6am-4pm PST M-Th; 6am-3pm PST Fri</span>
    	</div>
        <div class="span1" data-motopress-type="static">
            <?php dynamic_sidebar( 'cart-holder' ); ?>
    	</div>
    </div>
</div>
<div class="row">
    <div class="box3">
    	<div class="span12 logo_box" data-motopress-type="static" data-motopress-static-file="static/static-nav.php">
    		<?php get_template_part("static/static-nav"); ?>
            <?php get_template_part("static/static-search"); ?>
    	</div>
    </div>
</div>