<?php

class MPSliderDB {
    private $mpsl_settings;
    private $lastRowID;
	private static $instance = null;

    private function __construct() {
        global $mpsl_settings;
        $this->mpsl_settings = &$mpsl_settings;
    }

	public static function getInstance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    public function getSliderList($fields = null, $keyField = null) {
        global $wpdb;

        if ($keyField and !in_array($keyField, array('id', 'alias'))) $keyField = null;
        $entity = 'slider';
        $singleValue = false;

        if (is_null($fields)) {
            $_fields = array('*');

        } elseif (is_array($fields)) {
//            if (count($fields) === 1) $singleValue = reset($fields);
            $_fields = $fields;
            if ($keyField and !in_array($keyField, $_fields)) $_fields[] = $keyField;

        } else {
            $fields = trim($fields);
            $singleValue = $fields;
            $_fields = array($fields);
            if ($keyField and $fields !== $keyField) $_fields[] = $keyField;
        }

        $query = sprintf(
            'SELECT %s FROM %s',
            implode(',', $_fields),
            $this->mpsl_settings["{$entity}s_table"]
        );

        $sliders = $wpdb->get_results($query, 'ARRAY_A');

        if ($keyField) {
            $_sliders = array();
            foreach ($sliders as $slider) {
                $_sliders[$slider[$keyField]] = $slider;
            }
            $sliders = $_sliders;
        }

        $decodeOptions = (in_array('options', $_fields) or in_array('*', $_fields));

        foreach ($sliders as $key => $slider) {
            if ($decodeOptions) {
                $sliders[$key]['options'] = json_decode($sliders[$key]['options'], true);
            }
            if ($singleValue) {
                $sliders[$key] = $sliders[$key][$singleValue];
            }
        }

        return $sliders;
    }

	public function getSlideList($fields = null, $keyField = null) {
        global $wpdb;

        if ($keyField and !in_array($keyField, array('id'))) $keyField = null;
        $entity = 'slide';
        $singleValue = false;

        if (is_null($fields)) {
            $_fields = array('*');

        } elseif (is_array($fields)) {
//            if (count($fields) === 1) $singleValue = reset($fields);
            $_fields = $fields;
            if ($keyField and !in_array($keyField, $_fields)) $_fields[] = $keyField;

        } else {
            $fields = trim($fields);
            $singleValue = $fields;
            $_fields = array($fields);
            if ($keyField and $fields !== $keyField) $_fields[] = $keyField;
        }

        $query = sprintf(
            'SELECT %s FROM %s',
            implode(',', $_fields),
            $this->mpsl_settings["{$entity}s_table"]
        );

        $slides = $wpdb->get_results($query, 'ARRAY_A');

        if ($keyField) {
            $_slides = array();
            foreach ($slides as $slider) {
                $_slides[$slider[$keyField]] = $slider;
            }
            $slides = $_slides;
        }

        $decodeOptions = (in_array('options', $_fields) or in_array('*', $_fields));
        $decodeLayers = (in_array('layers', $_fields) or in_array('*', $_fields));

        foreach ($slides as $key => $slider) {
            if ($decodeOptions) $slides[$key]['options'] = json_decode($slides[$key]['options'], true);
            if ($decodeLayers) {
	            $slides[$key]['layers'] = json_decode($slides[$key]['layers'], true);
	            if (!is_array($slides[$key]['layers'])) $slides[$key]['layers'] = array();
            }
            if ($singleValue) $slides[$key] = $slides[$key][$singleValue];
        }

        return $slides;
    }

    /**
     * @param $id - Slide(r) id
     * @param null | string | array $fields - Slide(r) fields
     * If null - get all fields
     * If string - get one field
     * If array - get array of fields
     * @param $entity - slider or slide
     * @return mixed
     * @throws ErrorException
     */
    private function getSliderOrSlide($id, $fields = null, $entity) {
        global $wpdb;

        if (!in_array($entity, array('slider', 'slide', 'preview_slide'))) {
            throw new ErrorException('Bad entity type');
        }

        if (is_null($fields)) {
            $_fields = '*';
        } elseif (is_array($fields)) {
            $_fields = implode(',', $fields);
        } else {
            $_fields = (string) $fields;
        }

        $query = sprintf(
            'SELECT %s FROM %s WHERE id=%d',
            $_fields,
            $this->mpsl_settings["{$entity}s_table"],
            $id
        );

        if (is_string($fields)) {
            $slider = $wpdb->get_var($query);
        } else {
            $slider = $wpdb->get_row($query, 'ARRAY_A');
//            foreach ($slider as &$attr) {
//                if (is_numeric($attr)) $attr = (int) $attr;
//            }
        }

        return $slider;
    }

    public function getSlider($id, $fields = null) {
        return $this->getSliderOrSlide($id, $fields, 'slider');
    }

    public function getSlide($id, $fields = null) {
        return $this->getSliderOrSlide($id, $fields, 'slide');
    }

	public function getPreviewSlide($id, $fields = null) {
		return $this->getSliderOrSlide($id, $fields, 'preview_slide');
	}

    public function isSliderExists($id) {
        $sliderId = $this->getSlider($id, 'id');
        return is_null($sliderId) ? false : true;
    }

    public function isSlideExists($id) {
        $slideId = $this->getSlide($id, 'id');
        return is_null($slideId) ? false : true;
    }

    public function getSiblings($id) {
        global $wpdb;
        return $wpdb->get_results(sprintf(
            'SELECT id FROM %s WHERE slider_id=%d ORDER BY slide_order ASC',
            $this->mpsl_settings['slides_table'],
            $id
        ), ARRAY_A);
    }
    public function getSlidesBySlider($id, $decodeFields = array()) {
        global $wpdb;
        $slides = $wpdb->get_results(sprintf(
            'SELECT * FROM %s WHERE slider_id=%d ORDER BY slide_order ASC',
            $this->mpsl_settings['slides_table'],
            $id
        ), ARRAY_A);

        foreach($slides as &$slide) {
	        foreach ($decodeFields as $field) {
		        if (isset($slide[$field])) {
			        $slide[$field] = json_decode($slide[$field], true);
		        }
	        }
        }

        return $slides;
    }

    public function updateSlidesOrder($slidesOrder){
        global $wpdb;
        $query= 'UPDATE ' . $this->mpsl_settings['slides_table'] .
                ' SET slide_order =  CASE ';
        foreach ($slidesOrder as $order => $id){
            $query .= sprintf(' WHEN id = %d THEN %d', $id, $order);
        }
        $query .= ' ELSE slide_order';
        $query .= ' END';
//        $query .= sprintf(' WHERE slider_id=%d', $sliderId);
        $wpdb->hide_errors();
        return $wpdb->query($query);
    }


    public function getPostsByOptions($options, $sliderType) {
	    $postType = $sliderType === 'post' ? $options['post_type'] : 'product';

        $args = array(
            'post_type' => $postType,
	        'orderby' => 'menu_order date',
			'order' => 'DESC',
            'post_status' => 'publish',
	        'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'offset' => 0,
        );

        if ($options['post_offset']) {
            $args['offset'] = $options['post_offset'];
        }

        if ($options['post_order_by']) {
            $args['orderby'] = $options['post_order_by'];
        }

        if ($options['post_count']) {
            $args['posts_per_page'] = $options['post_count'];
        }

        if ($options['post_order_direction']) {
            $args['order'] = $options['post_order_direction'];
        }

        if ($options['post_include_ids']) {
	        $args['post__in'] = preg_split('/\D+/', $options['post_include_ids']);
        }

        if ($options['post_exclude_ids']) {
            $args['post__not_in'] = preg_split('/\D+/', $options['post_exclude_ids']);
        }

        $args['tax_query'] = array();
        if(isset($options['post_categories'])) {
            $taxQuery = $this->getTaxQuery($options['post_categories'], 'category', $postType, $sliderType);
            if ($taxQuery) {
                $args['tax_query'] = array_merge($args['tax_query'], $taxQuery);
            }
        }

        if(isset($options['post_tags'])) {
            $taxQuery = $this->getTaxQuery($options['post_tags'], 'tag', $postType, $sliderType);
            if($taxQuery) {
                $args['tax_query'] = array_merge($args['tax_query'], $taxQuery);
            }
        }

        if (isset($options['post_format'])) {
            $taxQuery = $this->getTaxQuery($options['post_format'], 'post_format', $postType, $sliderType);
            if ($taxQuery) {
                $args['tax_query'] = array_merge($args['tax_query'], $taxQuery);
            }
        }

        if ($sliderType === 'woocommerce') {
            $meta_query = array();
            if ($options['wc_only_instock']) {
                $meta_query[] = array(
                    'key'       => '_stock_status',
                    'value'     => 'outofstock',
                    'compare'   => 'NOT IN'
                );
            }

            if ($options['wc_only_featured']) {
                $meta_query[] = array(
                    'key' => '_featured',
                    'value' => 'yes'
                );
            }

            if ($options['wc_only_onsale']) {
                $meta_query[] = array(
                    'key' => '_sale_price',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'NUMERIC'
                );
            }
            $args['meta_query'] = $meta_query;

        }

        return new WP_Query($args);
    }


    private function getTaxQuery($termsArr, $type, $postType, $sliderType) {
        $taxQuery = array();
        if ($termsArr) {
            if ($type === 'category') {
                if ($sliderType === 'post') {
                    if ($postType === 'product' ) {
                        $taxonomy = 'product_cat';
                    } else {
                        $taxonomy = 'category';
                    }
                } else {
                    $taxonomy = 'product_cat';
                }

            } else if ($type === 'tag') {
                if ($sliderType === 'post') {
                    if ($postType === 'product' ) {
                        $taxonomy = 'product_tag';
                    } else {
                        $taxonomy = 'post_tag';
                    }
                } else {
                    $taxonomy = 'product_tag';
                }

            } else {
                $taxonomy = 'post_format';
            }

            if (count($termsArr) > 1 && ($key = array_search(0, $termsArr)) !== false) {
                unset($termsArr[$key]);
            }

            if (!in_array(0, $termsArr)) {
                $taxQuery[] = array(
                    'taxonomy' => $taxonomy,
                    'field' => 'id',
                    'terms' => array_values($termsArr),
                    'operator' => 'IN'
                );
            }
        }

        return $taxQuery;
    }

//    private function getTxQuery($categoriesArr, $tagsArr, $postFormatArr) {
//        $taxQuery = array();
////        if (($categoriesArr && $tagsArr) && (!in_array(0, $categoriesArr) || !in_array(0, $tagsArr))) {
////            $taxQuery['relation'] = 'AND';
////        }
//        if (count($categoriesArr)) {
//            if (count($categoriesArr) > 1 && ($key = array_search(0, $categoriesArr)) !== false) {
//                unset($categoriesArr[$key]);
//            }
//
//            if (!in_array(0, $categoriesArr)) {
//                //TODO:: need postformat inject
//
//                $taxQuery[] = array(
//                    'taxonomy' => $type === 'post' ? 'category' : 'product_cat',
//                    'field' => 'id',
//                    'terms' => $categoriesArr,
//                    'operator' => 'IN'
//                );
//            }
//        }
//
//        if (count($tagsArr)) {
//            if (count($tagsArr) > 1 && ($key = array_search(0, $tagsArr)) !== false) {
//                unset($tagsArr[$key]);
//            }
//            if (!in_array(0, $tagsArr)) {
//                $taxQuery[] = array(
//                    'taxonomy' => $type === 'post' ? 'post_tag' : 'product_tag',
//                    'field' => 'id',
//                    'terms' => $tagsArr,
//                    'operator' => 'IN'
//                );
//            }
//        }
//
//        if(count($postFormatArr)){
//
//            if (count($postFormatArr) > 1 && ($key = array_search(0, $postFormatArr)) !== false) {
//                unset($postFormatArr[$key]);
//            }
//
//            if (!in_array(0, $postFormatArr)) {
//
//                $taxQuery[] = array(
//                    'taxonomy' => 'post_format',
//                    'field' => 'id',
//                    'terms' => $postFormatArr,
//                    'operator' => 'IN'
//                );
//            }
//
//        }
//
//        if (count($taxQuery)) {
//            $taxQuery['relation'] = 'AND';
//        }
//
//        return $taxQuery;
//    }


    public function getImagebyPost($post, $isSrc = false, $imageFrom = 'auto') {
        $imgSrc = '';
        if ($imageFrom === 'auto') {
            $imgSrc = has_post_thumbnail($post->ID) ? $this->getPostThumbnailFullSrc($post->ID) : '';
            if (empty($imgSrc)) {
//                $content = $post->post_content;
	            $content = apply_filters('the_content', get_post_field('post_content', $post));
                $imgSrc = $this->getFirstImageSrcFromString($content);
            }

        } elseif ($imageFrom === 'featured') {
            $imgSrc = has_post_thumbnail($post->ID) ? $this->getPostThumbnailFullSrc($post->ID) : '';

        } elseif ($imageFrom === 'first') {
//            $content = $post->post_content;
	        $content = apply_filters('the_content', get_post_field('post_content', $post));
            $imgSrc = $this->getFirstImageSrcFromString($content);
        }

	    if ($isSrc) {
		    return $imgSrc;
	    } else {
		    if (!empty($imgSrc)) {
                return sprintf('<img src="%s" alt="%s" />', $imgSrc, strip_tags(get_the_title($post)));
//               $post_thumbnail_id = get_post_thumbnail_id($post->ID);
//               return  wp_get_attachment_image($post_thumbnail_id , 'medium', false, array(
//                   'title' =>  esc_attr( $post_thumbnail_id ),
//               ));

		    } else {
			    return '';
		    }
	    }
    }

    private function getPostThumbnailFullSrc($post_id) {
        $post_id = is_null($post_id) ? get_the_ID() : $post_id;
        $post_thumbnail_id = get_post_thumbnail_id($post_id);

//        return wp_get_attachment_url($post_thumbnail_id, 'full');
        return wp_get_attachment_url($post_thumbnail_id);
    }

    private function getFirstImageSrcFromString($content) {
        $images = $this->extractStringTemplates($content);
        return ($images && count($images[1])) ? $images[1][0] : '';
    }

    public function getPostImageThumbnail($post) {
        $post_id = is_null($post->ID) ? get_the_ID() : $post->ID;
        $result = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'thumbnail' );
        return $result[0];
    }

    private function extractStringTemplates($content) {
        preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', $content, $matches);
        return isset($matches) && count($matches[0]) ? $matches : false;
    }

    public function getFormatDate($key, $post) {
        return mysql2date($key, $post->post_date);
    }

    public function getExcerpt($post, $charLen) {
	    if (post_password_required($post)) {
		    return get_the_password_form($post);
	    }

	    $excerpt = has_excerpt() ? $post->post_excerpt : $post->post_content;
	    $excerpt = apply_filters('the_content', $excerpt);
	    /*$excerpt = get_the_excerpt();
	    $excerpt = do_shortcode($excerpt);*/

        $excerpt = $this->stripShortcodes($excerpt);
	    $excerpt = preg_replace('/<script.*?>.*?<\/script>/is', '', $excerpt);
	    $excerpt = preg_replace('/<style.*?>.*?<\/style>/is', '', $excerpt);
	    $excerpt = trim(strip_tags($excerpt));
	    $oldStrLen = strlen($excerpt);
        $excerpt = $this->getTrimmedString($excerpt, $charLen);
	    $newStrLen = strlen($excerpt);

	    if ($excerpt && $newStrLen !== $oldStrLen) {
		    $excerpt .= ' ...';
	    }

        return $excerpt;
    }

    private function getTrimmedString($string, $maxLen = 10000) {
        $maxLen = $maxLen > 0 ? $maxLen : 10000;
        return function_exists('mb_strimwidth') ? mb_strimwidth($string, 0, $maxLen) : substr($string, 0, $maxLen);
    }

    private function stripShortcodes($content, $excludeStripShortcodeTags = null) {
        if (!$content) return $content;

        if (empty($excludeStripShortcodeTags) || !is_array($excludeStripShortcodeTags)) {
            return preg_replace('/\[[^\]]*\]/', '', $content);
        }

        $excludeCodes = join('|', $excludeStripShortcodeTags);
        return preg_replace("~(?:\[/?)(?!(?:$excludeCodes))[^/\]]+/?\]~s", '', $content);
    }

}