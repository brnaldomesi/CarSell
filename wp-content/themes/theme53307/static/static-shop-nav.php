<?php /* Static Name: Shop Nav */ ?>
<div class="shop-nav"><?php
	wp_nav_menu( array(
	    'container'       => 'ul', 
	    'menu_class'      => 'shop-menu', 
	    'menu_id'         => 'shopnav',
	    'depth'           => 0,
	    'theme_location' => 'shop_menu'
	)); 
?></div>