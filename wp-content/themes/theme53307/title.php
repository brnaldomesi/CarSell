<section class="title-section">
	<h1 class="title-header">
		<?php if(is_home()){ ?>
			<?php $blog_text = of_get_option('blog_text'); ?>
				<?php if($blog_text){?>
					<?php echo of_get_option('blog_text'); ?>
				<?php } else { ?>
					<?php echo theme_locals("blog") ?>
			<?php } ?>
		<?php } elseif ( is_category() ) {
				echo theme_locals("category_archives").": ".'<small>'.single_cat_title( '', false ).'</small>';
				echo category_description(); /* displays the category's description from the Wordpress admin */
			} elseif ( is_tax('portfolio_category') ) { ?>
			<?php echo theme_locals("portfolio_category").": "; ?>
			<small><?php echo single_cat_title( '', false ); ?> </small>
		
		<?php } elseif ( is_search() ) { ?>
			<?php echo theme_locals("fearch_for").": ";?>"<?php the_search_query(); ?>"
		
		<?php } elseif ( is_day() ) { ?>
			<?php printf( theme_locals("daily_archives").": <small>%s</small>", get_the_date() ); ?>
			
		<?php } elseif ( is_month() ) { ?>	
			<?php printf( theme_locals("monthly_archives").": <small>%s</small>", get_the_date('F Y') ); ?>
			
		<?php } elseif ( is_year() ) { ?>	
			<?php printf( theme_locals("yearly_archives").": <small>%s</small>", get_the_date('Y') ); ?>
		
		<?php } elseif ( is_author() ) { ?>
			<?php 
				global $author;
				$userdata = get_userdata($author);
				echo theme_locals("by");?><?php echo $userdata->display_name;
			} elseif ( is_tag() ) { 
				echo theme_locals("tag_archives").': '.'<small>' . wp_title('', false) . '</small>'; 
			} elseif ( is_tax('portfolio_tag') ) { 
				echo theme_locals("portfolio_tag").": ".'<small>'.single_tag_title( '', false ).'</small>';
		?>
<!--Begin shop-->
		<?php } elseif (function_exists( 'is_shop' ) && is_shop()) {
				if (class_exists( 'Woocommerce' ) && (is_woocommerce())) {
					$page_id = woocommerce_get_page_id('shop');
					echo get_page($page_id)->post_title;
				} elseif (function_exists( 'jigoshop_init' )){
					$page_id = jigoshop_get_page_id('shop');
					echo get_page($page_id)->post_title;
				}
		?>
<!--End shop-->
		<?php } else { ?>

			<?php
			if (class_exists( 'Woocommerce' ) && !is_singular()){
				if ( is_tax('product_cat') ||  	is_tax('product_tag') ) {
					$term =	$wp_query->queried_object;
					echo $term->name;
				} else {
					$page_id = woocommerce_get_page_id('shop');
					echo get_page($page_id)->post_title;
				} 
			} elseif (function_exists( 'jigoshop_init' ) && !is_singular()){
				if ( is_tax('product_cat') ) {
					$term =	$wp_query->queried_object;
					echo $term->name;
				} else {
					$page_id = jigoshop_get_page_id('shop');
					echo get_page($page_id)->post_title;
				}
					
			} else {
			if (have_posts()) : while (have_posts()) : the_post();
				$pagetitle = get_post_custom_values("page-title");
				$pagedesc = get_post_custom_values("title-desc");
				if($pagetitle == ""){
					the_title();
				} else {
					echo $pagetitle[0];
				}
				if($pagedesc != ""){ ?>
					<span class="title-desc"><?php echo $pagedesc[0];?></span>
				<?php }
			endwhile; endif;
			wp_reset_query();
			}
		} ?>
	</h1>
	<?php
		if (of_get_option('g_breadcrumbs_id') == 'yes') { ?>
			<!-- BEGIN BREADCRUMBS-->
			<?php
/* Begin shop */	
				if ( ( function_exists( 'is_shop' ) && is_shop() ) || ( function_exists( 'is_product' ) && is_product() ) ||  ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) || ( function_exists( 'is_product_list' ) && is_product_list() ) ) {
					if(class_exists( 'Woocommerce' )){
						woocommerce_breadcrumb(array('delimiter' => ' / ', 'wrap_before' => '<ul class="breadcrumb breadcrumb__t">', 'wrap_after' => '</ul>'));
					} elseif(function_exists( 'jigoshop_init' )){
						jigoshop_breadcrumb('/ ', '<ul class="breadcrumb breadcrumb__t">', '</ul>');
					}
/* End shop */
				} elseif (function_exists('breadcrumbs')) {
					breadcrumbs();
				};
			?>
			<!-- END BREADCRUMBS -->
	<?php }
	?>
</section><!-- .title-section -->