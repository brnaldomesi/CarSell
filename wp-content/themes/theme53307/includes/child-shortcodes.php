<?php
//Shortcodes

add_shortcode( 'advanced_categories', 'tm_advanced_categories_shortcode' );

function tm_advanced_categories_shortcode( $atts ) {

	extract( shortcode_atts( array(
		'from_cat' => '',
		'select_only_with_images' => true,
		'show_image' => true,
		'show_name' => false,
		'show_description' => false,
		'columns' => '4'
	), $atts ) );

	//global $tm_theme_texdomain;

	if ( '' != $from_cat) {
		$parent_cat = get_term_by( 'slug', $from_cat, 'product_cat' );
		$args = array(
		    'hide_empty'    => false, 
		    'parent'         => $parent_cat->term_id
		); 
	} else {
		$args = array(
		    'hide_empty'    => false
		);
	}
	$prod_cats = get_terms( 'product_cat', $args );
	if ( $prod_cats ) {
		$container_class = '';
		switch ($columns) {
			case '1':
				$container_class = 'cols_1';
				$col_num = 1;
				break;
			case '2':
				$container_class = 'cols_2';
				$col_num = 2;
				break;
			case '3':
				$container_class = 'cols_3';
				$col_num = 3;
				break;
			case '4':
				$container_class = 'cols_4';
				$col_num = 4;
				break;
			case '5':
				$container_class = 'cols_5';
				$col_num = 5;
				break;
			case '6':
				$container_class = 'cols_6';
				$col_num = 6;
				break;
			default:
				$container_class = 'cols_6';
				$col_num = 6;
				break;
		}
		
		$output = "<ul class='advanced_categories " . $container_class . "'>\n";
		$cat_iterator = 0;
		foreach ( $prod_cats as $cat ) {

			$cat_link = get_term_link( $cat, 'product_cat' );

			$visible_trigger = true;
			if ( true == $select_only_with_images ) {
				$thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
				$image = wp_get_attachment_url( $thumbnail_id );
				if (!$image) {
					$visible_trigger = false;
				}
			}
			if ( true == $visible_trigger ) {
				$cat_iterator++;
				$item_class = '';
				if ( 1 == $cat_iterator % $col_num ) {
					$item_class = ' first';
				} elseif ( 0 == $cat_iterator % $col_num ) {
					$item_class = ' last';
				}
				$output .= "<li class='advanced_categories_item" . $item_class . "'>\n";
					$output .= "<div class='advanced_categories_item_inner'>\n";
						if ( true == $show_image ) {
							$thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
							$image = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' );
							$output .= "<figure>\n";
								$output .= "<a href='" . $cat_link . "'><img src='" . $image[0] . "' alt='" . $cat->name . "'></a>\n";
							$output .= "</figure>\n";
						}
						if ( true == $show_name ) {
							$output .= "<h4><a href='" . $cat_link . "'>" . $cat->name . "</a></h4>\n";
						}
						if ( true == $show_description && $cat->description != '' ) {
							$output .= "<div class='cat_desc'>" . $cat->description . "</div>\n";
						}
					$output .= "</div>\n";
				$output .= "</li>\n";
			}

		}
		$output .= "</ul>\n";
	} else {
		$output = __( 'There is no categories has been found', CURRENT_THEME );
	}

	return $output;
	
}

//Custom element
function shortcode_custom_element($atts, $content = null) {
	extract(shortcode_atts(array(
			'element' => 'div',
			'css_class' => 'my_class',
			'inner_wrapper' => false 
	), $atts));

	$output = '<'.$element.' class="'.esc_attr( $css_class ).'">';
	if (true == $inner_wrapper) {
		$output .= '<div class="'.esc_attr( $css_class ).'_wrap_inner">';
	}
		$output .= do_shortcode($content);
	if (true == $inner_wrapper) {
		$output .= '</div>';
	}
	$output .= '</'.$element.'>';

	return $output;
}
add_shortcode('custom_element', 'shortcode_custom_element');
/**
 * Banner
 *
 */
if (!function_exists('banner_shortcode')) {

 function banner_shortcode($atts, $content = null) {
  extract(shortcode_atts(
   array(
    'img'          => '',
    'banner_link'  => '',
    'title'        => '',
    'text'         => '',
    'btn_text'     => '',
    'target'       => '',
    'custom_class' => ''
  ), $atts));

  // get attribute
  $content_url = content_url();
  $content_str = 'wp-content';

  $pos = strpos($img, $content_str);
  if ($pos !== false) {
   $img_new = substr( $img, $pos+strlen($content_str), strlen($img)-$pos );
   $img     = $content_url.$img_new;
  }

  $output =  '<a href="'. $banner_link .'" title="'. $title .'" class="banner-wrap '.$custom_class.'">'; 

  if ($img !="") {
   $output .= '<figure class="featured-thumbnail">';
   if ($banner_link != "") {
    $output .= '<img src="' . $img .'" title="'. $title .'" alt="" />';
   } else {
    $output .= '<img src="' . $img .'" title="'. $title .'" alt="" />';
   }
   $output .= '</figure>';
  }  
  $output .= '<div class="extra-wrap">';
  
   if ($title!="") {
   $output .= '<h5>';
   $output .= $title;
   $output .= '</h5>';
  }

  if ($text!="") {
   $output .= '<p>';
   $output .= $text;
   $output .= '</p>';
  }
  
  if ($btn_text!="") {
   $output .=  '<div class="link-align banner-btn">';
   $output .= $btn_text;
   $output .= '</div>';
  }
  $output .= '</div>';
  $output .= '</a><!-- .banner-wrap (end) -->';
  return $output;

 }
 add_shortcode('banner', 'banner_shortcode');
}
/**
 * Post Grid
 *
 */
if (!function_exists('custom_posts_grid_shortcode')) {

	function custom_posts_grid_shortcode( $atts, $content = null, $shortcodename = '' ) {
		extract(shortcode_atts(array(
			'type'            => 'post',
			'category'        => '',
			'custom_category' => '',
			'tag'             => '',
			'columns'         => '3',
			'rows'            => '3',
			'order_by'        => 'date',
			'order'           => 'DESC',
			'thumb_width'     => '370',
			'thumb_height'    => '250',
			'meta'            => '',
			'excerpt_count'   => '15',
			'link'            => 'yes',
			'link_text'       => __('Read more', CHERRY_PLUGIN_DOMAIN),
			'custom_class'    => ''
		), $atts));

		$spans = $columns;
		$rand  = rand();

		// columns
		switch ($spans) {
			case '1':
				$spans = 'span12';
				break;
			case '2':
				$spans = 'span6';
				break;
			case '3':
				$spans = 'span4';
				break;
			case '4':
				$spans = 'span3';
				break;
			case '6':
				$spans = 'span2';
				break;
		}

		// check what order by method user selected
		switch ($order_by) {
			case 'date':
				$order_by = 'post_date';
				break;
			case 'title':
				$order_by = 'title';
				break;
			case 'popular':
				$order_by = 'comment_count';
				break;
			case 'random':
				$order_by = 'rand';
				break;
		}

		// check what order method user selected (DESC or ASC)
		switch ($order) {
			case 'DESC':
				$order = 'DESC';
				break;
			case 'ASC':
				$order = 'ASC';
				break;
		}

		// show link after posts?
		switch ($link) {
			case 'yes':
				$link = true;
				break;
			case 'no':
				$link = false;
				break;
		}

			global $post;
			global $my_string_limit_words;

			$numb = $columns * $rows;

			// WPML filter
			$suppress_filters = get_option('suppress_filters');

			$args = array(
				'post_type'         => $type,
				'category_name'     => $category,
				$type . '_category' => $custom_category,
				'tag'               => $tag,
				'numberposts'       => $numb,
				'orderby'           => $order_by,
				'order'             => $order,
				'suppress_filters'  => $suppress_filters
			);

			$posts      = get_posts($args);
			$i          = 0;
			$count      = 1;
			$output_end = '';
			$countul = 0;

			if ($numb > count($posts)) {
				$output_end = '</ul>';
			}

			$output = '<ul class="posts-grid row-fluid unstyled '. $custom_class .' ul-item-'.$countul.'">';


			foreach ( $posts as $j => $post ) {
				$post_id = $posts[$j]->ID;
				//Check if WPML is activated
				if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
					global $sitepress;

					$post_lang = $sitepress->get_language_for_element( $post_id, 'post_' . $type );
					$curr_lang = $sitepress->get_current_language();
					// Unset not translated posts
					if ( $post_lang != $curr_lang ) {
						unset( $posts[$j] );
					}
					// Post ID is different in a second language Solution
					if ( function_exists( 'icl_object_id' ) ) {
						$posts[$j] = get_post( icl_object_id( $posts[$j]->ID, $type, true ) );
					}
				}

				setup_postdata($posts[$j]);
				$excerpt        = get_the_excerpt();
				$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'full' );
				$url            = $attachment_url['0'];
				$image          = aq_resize($url, $thumb_width, $thumb_height, true);
				$mediaType      = get_post_meta($post_id, 'tz_portfolio_type', true);
				$prettyType     = 0;

				if ($count > $columns) {
					$count = 1;
					$countul ++;
					$output .= '<ul class="posts-grid row-fluid unstyled '. $custom_class .' ul-item-'.$countul.'">';
				}

				$output .= '<li class="'. $spans .' list-item-'.$count.'">';
                    
					if(has_post_thumbnail($post_id) && $mediaType == 'Image') {
                            
						$prettyType = 'prettyPhoto-'.$rand;
                       
						$output .= '<figure class="featured-thumbnail thumbnail">';
						$output .= '<a href="'.$url.'" title="'.get_the_title($post_id).'" rel="' .$prettyType.'">';
						$output .= '<img  src="'.$image.'" alt="'.get_the_title($post_id).'" />';
						$output .= '<span class="zoom-icon"></span></a></figure>';
					} elseif ($mediaType != 'Video' && $mediaType != 'Audio') {

						$thumbid = 0;
						$thumbid = get_post_thumbnail_id($post_id);

						$images = get_children( array(
							'orderby'        => 'menu_order',
							'order'          => 'ASC',
							'post_type'      => 'attachment',
							'post_parent'    => $post_id,
							'post_mime_type' => 'image',
							'post_status'    => null,
							'numberposts'    => -1
						) );

						if ( $images ) {

							$k = 0;
							//looping through the images
							foreach ( $images as $attachment_id => $attachment ) {
								$prettyType = "prettyPhoto-".$rand ."[gallery".$i."]";
								//if( $attachment->ID == $thumbid ) continue;

								$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' ); // returns an array
								$img = aq_resize( $image_attributes[0], $thumb_width, $thumb_height, true ); //resize & crop img
								$alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
								$image_title = $attachment->post_title;

								if ( $k == 0 ) {
									if (has_post_thumbnail($post_id)) {
										$output .= '<figure class="featured-thumbnail thumbnail">';
										$output .= '<a href="'.$image_attributes[0].'" title="'.get_the_title($post_id).'" rel="' .$prettyType.'">';
										$output .= '<img src="'.$image.'" alt="'.get_the_title($post_id).'" />';
									} else {
										$output .= '<figure class="featured-thumbnail thumbnail">';
										$output .= '<a href="'.$image_attributes[0].'" title="'.get_the_title($post_id).'" rel="' .$prettyType.'">';
										$output .= '<img  src="'.$img.'" alt="'.get_the_title($post_id).'" />';
									}
								} else {
									$output .= '<figure class="featured-thumbnail thumbnail" style="display:none;">';
									$output .= '<a href="'.$image_attributes[0].'" title="'.get_the_title($post_id).'" rel="' .$prettyType.'">';
								}
								$output .= '<span class="zoom-icon"></span></a></figure>';
								$k++;
							}
						} elseif (has_post_thumbnail($post_id)) {
							$prettyType = 'prettyPhoto-'.$rand;
							$output .= '<figure class="featured-thumbnail thumbnail">';
							$output .= '<a href="'.$url.'" title="'.get_the_title($post_id).'" rel="' .$prettyType.'">';
							$output .= '<img  src="'.$image.'" alt="'.get_the_title($post_id).'" />';
							$output .= '<span class="zoom-icon"></span></a></figure>';
						}
					} else {

						// for Video and Audio post format - no lightbox
						$output .= '<figure class="featured-thumbnail thumbnail"><a href="'.get_permalink($post_id).'" title="'.get_the_title($post_id).'">';
						$output .= '<img  src="'.$image.'" alt="'.get_the_title($post_id).'" />';
						$output .= '</a></figure>';
					}
                    
                    
					$output .= '<div class="clear"></div>';
                    
                    
                    // post date
					$output .= '<span class="post_date">';
					$output .= '<time datetime="'.get_the_time('Y-m-d\TH:i:s', $post_id).'">' .get_the_date('l, F j, Y'). '</time>';
					$output .= '</span>';
                        
					$output .= '<h5><a href="'.get_permalink($post_id).'" title="'.get_the_title($post_id).'">';
						$output .= get_the_title($post_id);
					$output .= '</a></h5>';
                    
					if ($meta == 'yes') {
						// begin post meta
                        
                            
						$output .= '<div class="post_meta">';


							

							
						
						// end post meta
					}
					$output .= cherry_get_post_networks(array('post_id' => $post_id, 'display_title' => false, 'output_type' => 'return'));
					if($excerpt_count >= 1){
						$output .= '<p class="excerpt">';
							$output .= wp_trim_words($excerpt,$excerpt_count);
						$output .= '</p>';
					}
					if($link){
						$output .= '<a href="'.get_permalink($post_id).'" class="btn_custom_btn" title="'.get_the_title($post_id).'">';
						$output .= $link_text;
						$output .= '</a>';
					}
					$output .= '</li>';
					if ($j == count($posts)-1) {
						$output .= $output_end;
					}
				if ($count % $columns == 0) {
					$output .= '</ul><!-- .posts-grid (end) -->';
				}
			$count++;
			$i++;

		} // end for
		wp_reset_postdata(); // restore the global $post variable

		$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

		return $output;
	}
	add_shortcode('custom_posts_grid', 'custom_posts_grid_shortcode');
    }
?>