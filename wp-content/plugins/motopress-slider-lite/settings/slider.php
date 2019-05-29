<?php

/** @var MPSLSliderOptions $this */
/** @var array $options */

$isAjax = defined('DOING_AJAX') && DOING_AJAX; // Checks `is it action ?`
//$isAction = isset($_POST['action']);
$isCreatePage = !(isset($_GET['id']) && $_GET['id']);
$optionsExists = isset($options) && is_array($options);

$categoriesArr = array();
$tagsArr = array();
$postTypesArr = array();
//$allPostTypesArr = array();
$postFormatsDependency = array();
$tagsDependency = array();
$catDependency = array();
$defaultPostType = $this->sliderType === 'post' ? 'post' : 'product';

// tmp
$_categories = array();
$_tags = array();
$_format = array();

if (($isCreatePage || $optionsExists) && !$isAjax && is_admin()) {

	if (in_array($this->sliderType, array('post', 'woocommerce'))) {

		if ($this->sliderType === 'post') {
			if ($optionsExists && isset($options['post_type']) && $options['post_type']) {
				$selectedPostType = $options['post_type'];
			} else {
				$selectedPostType = 'post';
			}
		} else {
			$selectedPostType = 'product';
		}

		if ($this->sliderType === 'post') {
			$postTypes = get_post_types(array(), 'objects');
			if (isset($postTypes['attachment'])) unset($postTypes['attachment']);
			if (isset($postTypes['revision'])) unset($postTypes['revision']);
			if (isset($postTypes['nav_menu_item'])) unset($postTypes['nav_menu_item']);

			// Reset default post_type
			if (count($postTypes) && !isset($postTypes['post'])) {
				$defaultPostType = reset(array_keys($postTypes));
			}

		} else {
			$postTypes = array('product' => get_post_type_object('product'));
		}

		if (count($postTypes)) {
			$categories = $tags = array();

			foreach ($postTypes as $postTypeName => $postType) {
				if (is_null($postType)) continue;

				$postTypeHierarchicalTaxs = $this->getTaxonomyName($postTypeName);
				$categories = $this->getTaxTerms($postTypeHierarchicalTaxs, $postTypeName, 'categories');
				$tags = $this->getTaxTerms($postTypeHierarchicalTaxs, $postTypeName, 'tags');
				// Get post-formats only once (because they are shared
				if (!count($_format)) $postFormats = $this->getTaxTerms($postTypeHierarchicalTaxs, $postTypeName, 'format');

				if (post_type_supports($postTypeName, 'post-formats')) {
					$postFormatsDependency[] = $postTypeName;
				}
				if (count(array_intersect(array('post_tag', 'product_tag'), array_keys($postTypeHierarchicalTaxs))) > 0) {
					$tagsDependency[] = $postTypeName;
				}
				if (count(array_intersect(array('category', 'product_cat'), array_keys($postTypeHierarchicalTaxs))) > 0) {
					$catDependency[] = $postTypeName;
				}

				if (count($categories) || count($tags)) {
					$postTypesArr[$postTypeName] = array(
						'label' => $postType->labels->singular_name,
						'attrs' => array(
							'data-categories' => $categories,
							'data-tags' => $tags,
//							'data-formats' => $postFormats
						),
						'value' => $postTypeName
					);
				} else {
					$postTypesArr[$postTypeName] = array(
						'label' => $postType->labels->singular_name,
						'attrs' => array(),
						'value' => $postTypeName
					);
				}

//				if ($postTypeHierarchicalTaxs) $allPostTypesArr[] = $postTypeName;

				if (
					($this->sliderType === 'post' && $postTypeName === $selectedPostType) ||
					($this->sliderType === 'woocommerce' && $postTypeName === 'product')
				) {
					if (!count($_categories)) {
						foreach ($categories as $cat) {
							$_categories[$cat['value']] = $cat['label'];
						}
					}
					if (!count($_tags)) {
						foreach ($tags as $tag) {
							$_tags[$tag['value']] = $tag['label'];
						}
					}
				}
				if (!count($_format)) {
					foreach ($postFormats as $format) {
						$_format[$format['value']] = $format['label'];
					}
				}

			}
		}

	}

}

$sliderSettings = array(
    'main' => array(
        'title' => __('General', 'motopress-slider-lite'),
        'icon' => null,
        'description' => '',
        'options' => array(
            'slider_type' => array(
                'type' => 'select',
                'default' => 'custom',
                'list' => array(
                    'custom' => 'custom',
                    'post' => 'post',
                    'woocommerce' => 'woocommerce'
                ),
                'hidden' => true,
            ),
            'title' => array(
                'type' => 'text',
                'label' => __('Slider title *', 'motopress-slider-lite'),
                'description' => __('Required. The title of the slider. Example: Slider1', 'motopress-slider-lite'),
                'default' => __('New Slider', 'motopress-slider-lite'),
                'disabled' => false,
                'required' => true,
            ),
            'alias' => array(
                'type' => 'alias',
                'label' => __('Slider alias *', 'motopress-slider-lite'),
                'alias' => 'shortcode',
                'description' => __('Required. The alias that will be used in shortcode for embedding the slider. Alias must be unique. Example: slider1', 'motopress-slider-lite'),
                'default' => '',
                'disabled' => false,
                'required' => true,
            ),
            'shortcode' => array(
                'type' => 'shortcode',
                'label' => __('Slider Shortcode', 'motopress-slider-lite'),
                'description' => 'Copy this shortocode and paste to your page.',
                'default' => '',
                'readonly' => true,
//                'disabled' => false,
            ),


			'width' => array(
                'type' => 'number',
                'label' => __('Slider size on Desktop (default)', 'motopress-slider-lite'),
                'label2' => __('Width:', 'motopress-slider-lite'),
                'description' => __('Initial width of the layers', 'motopress-slider-lite'),
//                'pattern' => '/^(0|[1-9][0-9]*)$/',
                'default' => 1170,
                'min' => 0,
//                'disabled' => false
            ),
            'height' => array(
                'type' => 'number',
                'label' => '',
                'label2' => __('Height:', 'motopress-slider-lite'),
                'description' => __('Initial height of the layers', 'motopress-slider-lite'),
                'default' => 600,
                'min' => 0,
//                'disabled' => false
            ),
            /*'min_height' => array(
                'type' => 'number',
                'label2' => __('Min. Height:'),
                'default' => 500
            ),*/

//            'post_slider' => array(
//                'type' => 'checkbox',
//                'label' => '',
//                'label2' => __('Post content', 'motopress-slider-lite'),
//                'description' => __('Enable post slider', 'motopress-slider-lite'),
//                'default' => false
//            ),

//            'slider_layout' => array(
//                'type' => 'select',
//                'label' => __('Slider Layout', 'motopress-slider-lite'),
//                'default' => 'auto',
//                'list' => array(
//                    'auto' => __('Auto', 'motopress-slider-lite')
//                )
//            ),
//            'description' => array(
//                'type' => 'textarea',
//                'label' => __('Description :', 'motopress-slider-lite'),
//                'description' => __('Write some description', 'motopress-slider-lite'),
//                'default' => 'Default description',
////                'disabled' => false,
//            ),
//            'test' => array(
//                'type' => 'select',
//                'label' => __('Test dependency', 'motopress-slider-lite'),
//                'default' => 'off',
//                'list' => array(
//                    'on' => 'On',
//                    'off' => 'Off'
//                ),
//            ),
//            'test_dependency' => array(
//                'type' => 'text',
//                'label' => __('Test dependency input', 'motopress-slider-lite'),
//                'default' => 'visible',
//                'dependency' => array(
//                    'parameter' => 'test',
//                    'value' => 'on'
//                ),
//            ),
//            'radio_group' => array(
//                'type' => 'radio_group',
//                'label' => __('Test radiogroup', 'motopress-slider-lite'),
//                'default' => 'one',
//                'list' => array(
//                    'one' => 'One',
//                    'two' => 'Two',
//                    'three' => 'Three',
//                )
//            ),

        )
    ),

	'size' => array(
		'title' => __('Size', 'motopress-slider-lite'),
		'icon' => null,
		'description' => '',
		'options' => array(
			'full_width' => array(
				'type' => 'checkbox',
				'label' =>  __('Full Width', 'motopress-slider-lite'),
				'label2' => __('Make this slider full-width / wide-screen', 'motopress-slider-lite'),
				'default' => false
			),
			'full_height' => array(
				'type' => 'checkbox',
				'label' => __('Full Height', 'motopress-slider-lite'),
				'label2' => __('Make this slider full-height', 'motopress-slider-lite'),
				'default' => false,
			),

			'full_height_offset' => array(
				'type' => 'number',
				'label' => __('Full height increment:', 'motopress-slider-lite'),
				'description' => __('Slider height will be increased or decreased to this value', 'motopress-slider-lite'),
				'default' => '',
				'dependency' => array(
					'parameter' => 'full_height',
					'value' => true,
				)
			),

			'full_height_units' => array(
				'type' => 'select',
				'label' => __('Increment units:', 'motopress-slider-lite'),
				'default' => 'px',
				'list' => array(
					'px' => __('Pixels (px)', 'motopress-slider-lite'),
					'%' => __('Percents (%)', 'motopress-slider-lite'),
				),
				'dependency' => array(
					'parameter' => 'full_height',
					'value' => true,
				)
			),

			'full_height_container' => array(
				'type' => 'text',
				'label' => __('Offset by container:', 'motopress-slider-lite'),
				'description' => __('The height will be decreased with the height of these elements. Enter CSS Selector.', 'motopress-slider-lite'),
				'default' => '',
				'dependency' => array(
					'parameter' => 'full_height',
					'value' => true,
				)
			),

			'full_size_grid' => array(
				'type' => 'checkbox',
				'label' => __('Full Size Grid', 'motopress-slider-lite'),
				'description' => __('Even if you select this option you still need to set Grid width and height to define slider size. If you check Full Width and/or Full Height options, the slider will be stretched to screen edges.', 'motopress-slider-lite'),
				'label2' => __('Make grid stretch to parent container', 'motopress-slider-lite'),
				'default' => false,
			),

			'enable_notebook' => array(
				'type' => 'checkbox',
				'label' => __('Slider size on Laptop', 'motopress-slider-lite'),
				'label2' =>  __('Configure layers size, position and styles manually for Laptop screen size. Initial Laptop dimensions:'),
				'default' => false,
				'layout' => 'notebook'
			),
			'notebook_width' => array(
				'type' => 'number',
				'label' => __('Width', 'motopress-slider-lite'),
				'default' => 1024,
				'min' => 0,
				'dependency' => array(
					'parameter' => 'enable_notebook',
					'value' => true,
				)
			),
			'notebook_height' => array(
				'type' => 'number',
				'label' => __('Height', 'motopress-slider-lite'),
				'default' => 768,
				'min' => 0,
				'dependency' => array(
					'parameter' => 'enable_notebook',
					'value' => true,
				)
			),

			'enable_tablet' => array(
				'type' => 'checkbox',
				'label' => __('Slider size on Tablet', 'motopress-slider-lite'),
				'label2' =>  __('Configure layers size, position and styles manually for Tablet screen size. Initial Tablet dimensions:'),
				'default' => false,
				'layout' => 'tablet'
			),
			'tablet_width' => array(
				'type' => 'number',
				'label' => __('Width', 'motopress-slider-lite'),
				'default' => 778,
				'min' => 0,
				'dependency' => array(
					'parameter' => 'enable_tablet',
					'value' => true,
				)
			),
			'tablet_height' => array(
				'type' => 'number',
				'label' => __('Height', 'motopress-slider-lite'),
				'default' => 960,
				'min' => 0,
				'dependency' => array(
					'parameter' => 'enable_tablet',
					'value' => true,
				)
			),

			'enable_mobile' => array(
				'type' => 'checkbox',
				'label' => __('Slider size on Mobile', 'motopress-slider-lite'),
				'label2' =>  __('Configure layers size, position and styles manually for Mobile screen size. Initial Mobile dimensions:'),
				'default' => false,
				'layout' => 'mobile'
			),
			'mobile_width' => array(
				'type' => 'number',
				'label' => __('Width', 'motopress-slider-lite'),
				'default' => 480,
				'min' => 0,
				'dependency' => array(
					'parameter' => 'enable_mobile',
					'value' => true,
				)
			),
			'mobile_height' => array(
				'type' => 'number',
				'label' => __('Height', 'motopress-slider-lite'),
				'default' => 720,
				'min' => 0,
				'dependency' => array(
					'parameter' => 'enable_mobile',
					'value' => true,
				)
			),
		),
	),
	'slideshow' => array(
		'title' => __('Slideshow', 'motopress-slider-lite'),
		'icon' => null,
		'description' => '',
		'options' => array(
			'enable_timer' => array(
                'type' => 'checkbox',
                'label' => __('Slideshow', 'motopress-slider-lite'),
                'label2' => __('Enable Slideshow', 'motopress-slider-lite'),
                'default' => true,
//                'disabled' => false
            ),
			'slider_delay' => array(
				'type' => 'text',
				'label' => __('Slideshow Delay:', 'motopress-slider-lite'),
				'description' => __('The time one slide stays on the screen in milliseconds', 'motopress-slider-lite'),
				'default' => 7000
			),
			'slider_animation' => array(
				'type' => 'select',
				'label' => __('Slideshow Animation:', 'motopress-slider-lite'),
				'default' => 'msSlide',
				'list' => array(
					'msSlide' => __('Slide', 'motopress-slider-lite'),
					'msSlideFade' => __('Fade', 'motopress-slider-lite'),
					'msSlideUpDown' => __('Slide Up', 'motopress-slider-lite'),
				),
				//'description' => __('Select slideshow animation', 'motopress-slider-lite'),
			),
			'slider_duration' => array(
				'type' => 'text',
				'label' => __('Animation Duration:', 'motopress-slider-lite'),
				'description' => __('Animation duration in milliseconds', 'motopress-slider-lite'),
				'default' => 2000
			),
			'slider_easing' => array(
				'type' => 'select',
				'label' => __('Animation Easing:', 'motopress-slider-lite'),
				'default' => 'easeOutCirc',
				'list' => array(
					'linear' => __('linear', 'motopress-slider-lite'),
					'ease' => __('ease', 'motopress-slider-lite'),
					'easeIn' => __('easeIn', 'motopress-slider-lite'),
					'easeOut' => __('easeOut', 'motopress-slider-lite'),
					'easeInOut' => __('easeInOut', 'motopress-slider-lite'),
					'easeInQuad' => __('easeInQuad', 'motopress-slider-lite'),
					'easeInCubic' => __('easeInCubic', 'motopress-slider-lite'),
					'easeInQuart' => __('easeInQuart', 'motopress-slider-lite'),
					'easeInQuint' => __('easeInQuint', 'motopress-slider-lite'),
					'easeInSine' => __('easeInSine', 'motopress-slider-lite'),
					'easeInExpo' => __('easeInExpo', 'motopress-slider-lite'),
					'easeInCirc' => __('easeInCirc', 'motopress-slider-lite'),
					'easeInBack' => __('easeInBack', 'motopress-slider-lite'),
					'easeOutQuad' => __('easeOutQuad', 'motopress-slider-lite'),
					'easeOutCubic' => __('easeOutCubic', 'motopress-slider-lite'),
					'easeOutQuart' => __('easeOutQuart', 'motopress-slider-lite'),
					'easeOutQuint' => __('easeOutQuint', 'motopress-slider-lite'),
					'easeOutSine' => __('easeOutSine', 'motopress-slider-lite'),
					'easeOutExpo' => __('easeOutExpo', 'motopress-slider-lite'),
					'easeOutCirc' => __('easeOutCirc', 'motopress-slider-lite'),
					'easeOutBack' => __('easeOutBack', 'motopress-slider-lite'),
					'easeInOutQuad' => __('easeInOutQuad', 'motopress-slider-lite'),
					'easeInOutCubic' => __('easeInOutCubic', 'motopress-slider-lite'),
					'easeInOutQuart' => __('easeInOutQuart', 'motopress-slider-lite'),
					'easeInOutQuint' => __('easeInOutQuint', 'motopress-slider-lite'),
					'easeInOutSine' => __('easeInOutSine', 'motopress-slider-lite'),
					'easeInOutExpo' => __('easeInOutExpo', 'motopress-slider-lite'),
					'easeInOutCirc' => __('easeInOutCirc', 'motopress-slider-lite'),
					'easeInOutBack' => __('easeInOutBack', 'motopress-slider-lite'),
				),
				'description' => __('<a href="https://jqueryui.com/easing/" target="_blank">Easing examples</a>', 'motopress-slider-lite'),
//                'dependency' => array(
//                    'parameter' => 'slider_animation',
//                    'value' => 'msSlide'
//                ),
			),
		)
	),



    'controls' => array(
        'title' => __('Controls', 'motopress-slider-lite'),
        'icon' => null,
        'description' => '',
        'options' => array(
            'arrows_show' => array(
                'type' => 'checkbox',
                'label2' => __('Show arrows', 'motopress-slider-lite'),
                'default' => true
            ),
            'thumbnails_show' => array(
                'type' => 'checkbox',
                'label2' => __('Show bullets', 'motopress-slider-lite'),
                'default' => true
            ),
            'slideshow_timer_show' => array(
                'type' => 'checkbox',
                'label2' => __('Show slideshow timer', 'motopress-slider-lite'),
                'default' => true
            ),
            'slideshow_ppb_show' => array(
                'type' => 'checkbox',
                'label2' => __('Show slideshow play/pause button', 'motopress-slider-lite'),
                'default' => true
            ),
            'controls_hide_on_leave' => array(
                'type' => 'checkbox',
                'label2' => __('Hide controls when mouse leaves slider', 'motopress-slider-lite'),
                'default' => false
            ),
            'hover_timer' => array(
                'type' => 'checkbox',
                'label2' => __('Pause on Hover', 'motopress-slider-lite'),
                'description' => __('Pause slideshow when hover the slider', 'motopress-slider-lite'),
                'default' => false
            ),
            'timer_reverse' => array(
                'type' => 'checkbox',
                'label2' => __('Reverse order of the slides', 'motopress-slider-lite'),
                'description' => __('Animate slides in the reverse order', 'motopress-slider-lite'),
                'default' => false
            ),
            'counter' => array(
                'type' => 'checkbox',
                'label2' => __('Show counter', 'motopress-slider-lite'),
                'description' => __('Displays the number of slides', 'motopress-slider-lite'),
                'default' => false
            ),
            'swipe' => array(
                'type' => 'checkbox',
                'label2' => __('Enable swipe', 'motopress-slider-lite'),
                'description' => __('Turn on swipe on desktop', 'motopress-slider-lite'),
                'default' => true
            ),
			'edit_slider' => array(
				'type' => 'checkbox',
				'label2' => __('Show edit button', 'motopress-slider-lite'),
				'description' => __('Display an icon for quick reference to slider settings', 'motopress-slider-lite'),
				'default' => true,
			),
        )
    ),

    'appearance' => array(
        'title' => __('Appearance', 'motopress-slider-lite'),
        'icon' => null,
        'description' => '',
        'options' => array(
			'start_slide' => array(
				'type' => 'number',
				'label' => __('Start with slide:', 'motopress-slider-lite'),
				'description' => __('Slide index in the list of slides', 'motopress-slider-lite'),
				'default' => 1,
				'min' => 1
			),
            'visible_from' => array(
                'type' => 'number',
                'label' => __('Visible', 'motopress-slider-lite'),
                'label2' => __('from', 'motopress-slider-lite'),
                'unit' => 'px',
                'default' => '',
                'min' => 0,
            ),
            'visible_till' => array(
                'type' => 'number',
                'label' => '',
                'label2' => __('till', 'motopress-slider-lite'),
                'unit' => 'px',
                'default' => '',
                'min' => 0,
            ),
            'presets' => array(
                'type' => 'action_group',
                'label' => '',
                'label2' => __('presets:', 'motopress-slider-lite'),
                'default' => '',
                'list' => array(
                    'phone' => __('Phone', 'motopress-slider-lite'),
                    'tablet' => __('Tablet', 'motopress-slider-lite'),
                    'desktop' => __('Desktop', 'motopress-slider-lite')
                ),
                'actions' => array(
                    'phone' => array(
                        'visible_from' => '',
                        'visible_till' => 767
                    ),
                    'tablet' => array(
                        'visible_from' => 768,
                        'visible_till' => 991
                    ),
                    'desktop' => array(
                        'visible_from' => 992,
                        'visible_till' => ''
                    )
                )
            ),
            'delay_init' => array(
                'type' => 'text',
                'label' => __('Initialization delay:', 'motopress-slider-lite'),
                'description' => __('Time in milliseconds before slider starts loading', 'motopress-slider-lite'),
                'default' => 0
            ),

            'scroll_init' => array(
                'type' => 'checkbox',
                'label' => '',
                'label2' => __('Display slider when it becomes visible on page (initialize slider on scroll)', 'motopress-slider-lite'),
                //'description' => __('Enable this option to init slider on scroll', 'motopress-slider-lite'),
                'default' => false
            ),
            'custom_class' => array(
                'type' => 'text',
                'label' => __('Custom CSS class', 'motopress-slider-lite'),
                'default' => ''
            ),
            'custom_styles' => array(
                'type' => 'codemirror',
                'mode' => 'css',
                'label2' => __('Slider custom styles', 'motopress-slider-lite'),
                'default' => ''
            ),
        )
    ),

);

if (in_array($this->sliderType, array('post', 'woocommerce'))) {

	// Taxonomy dependencies
	$taxDepsParam = $postFormatsTaxDepsParam = 'post_type';
	if ($this->sliderType === 'woocommerce') {
		$taxDepsParam = 'slider_type';
		$catDependency = $tagsDependency = 'woocommerce';
		
		if (in_array('product', $postFormatsDependency)) {
			$postFormatsTaxDepsParam = 'slider_type';
			$postFormatsDependency = 'woocommerce';
		}
	};

	$postSliderLabels = array();
	switch ($this->sliderType) {
		case 'post':
			$postSliderLabels = array(
				'tab_label' => __('Content', 'motopress-slider-lite'),
                'exclude_label' => __('Exclude posts:', 'motopress-slider-lite'),
                'exclude_description' => __('post id\'s separated by comma', 'motopress-slider-lite'),
                'include_label' => __('Include posts:', 'motopress-slider-lite'),
                'include_description' => __('post id\'s separated by comma', 'motopress-slider-lite'),
                'count_label' => __('Number of posts to display: ', 'motopress-slider-lite'),
                'link_label' => __('Link slides to post\'s page: ', 'motopress-slider-lite'),
			);
			break;
		case 'woocommerce':
			$postSliderLabels = array(
				'tab_label' => __('Content', 'motopress-slider-lite'),
                'exclude_label' => __('Exclude products:', 'motopress-slider-lite'),
                'exclude_description' => __('product id\'s separated by comma', 'motopress-slider-lite'),
                'include_label' => __('Include products:', 'motopress-slider-lite'),
                'include_description' => __('product id\'s separated by comma', 'motopress-slider-lite'),
                'count_label' => __('Number of products to display: ', 'motopress-slider-lite'),
                'link_label' => __('Link slides to product\'s page: ', 'motopress-slider-lite'),
			);
			break;
	}


	$sliderSettings['post_settings'] = array(
		'title' => $postSliderLabels['tab_label'],
		'icon' => null,
		'description' => '',
		'options' => array(
			'post_type' => array(
				'type' => 'select',
				'label' => __('Select Post type:', 'motopress-slider-lite'),
				'default' => $defaultPostType,
				'list' => $postTypesArr,
				'listAttrSettings' => array(
		            'data-categories' => array(
			            'type' => 'json',
		            ),
                    'data-tags' => array(
                        'type' => 'json',
                    )
	            ),
				'dependency' => array(
					'parameter' => 'slider_type',
					'value' => 'post',
				)
			),

			'post_categories' => array(
				'type' => 'select',
				'label' => __('Categories:', 'motopress-slider-lite'),
				'default' => 0,
				'multiple' => true,
				'list' => $_categories,
				'helpers' => array('post_type'),
				'dynamicList' => array(
					'parameter' => 'post_type',
					'attr' => 'data-categories',
				),
                'dependency' => array(
					'parameter' => $taxDepsParam,
					'value' => $catDependency,
				)
			),

			'post_tags' => array(
				'type' => 'select',
				'label' => __('Tags:', 'motopress-slider-lite'),
				'default' => 0,
				'multiple' => true,
				'list' => $_tags,
                'helpers' => array('post_type'),
                'dynamicList' => array(
                    'parameter' => 'post_type',
                    'attr' => 'data-tags',
                ),
                'dependency' => array(
                    'parameter' => $taxDepsParam,
                    'value' => $tagsDependency,
                )
			),

			'post_format' => array(
				'type' => 'select',
				'label' => __('Post Format:', 'motopress-slider-lite'),
				'default' => 0,
				'multiple' => true,
				'list' => $_format,
				'dependency' => array(
					'parameter' => $postFormatsTaxDepsParam,
					'value' => $postFormatsDependency,
				)
			),

			'post_exclude_ids' => array(
				'type' => 'text',
				'label' => $postSliderLabels['exclude_label'],
				'description' => $postSliderLabels['exclude_description'],
				'default' => '',
				'dependency' => array(
					'parameter' => 'slider_type',
					'value' => array('post', 'woocommerce'),
				)
			),
			'post_include_ids' => array(
				'type' => 'text',
				'label' => $postSliderLabels['include_label'],
				'description' => $postSliderLabels['include_description'],
				'default' => '',
				'dependency' => array(
					'parameter' => 'slider_type',
					'value' => array('post', 'woocommerce'),
				)
			),
			'post_count' => array(
				'type' => 'number',
				'label' => $postSliderLabels['count_label'],
				'default' => 10,
				'min' => -1,
				'dependency' => array(
					'parameter' => 'slider_type',
					'value' => array('post', 'woocommerce'),
				)
			),
			'post_excerpt_length' => array(
				'type' => 'number',
				'label' => __('Excerpt length:', 'motopress-slider-lite'),
				'description' => __('character(s)', 'motopress-slider-lite'),
				'default' => 200,
				'min' => 0,
				'dependency' => array(
					'parameter' => 'slider_type',
					'value' => array('post', 'woocommerce'),
				)
			),
			'post_offset' => array(
				'type' => 'number',
				'label' => __('Number of first results to skip (offset):', 'motopress-slider-lite'),
				'default' => '',
				'min' => 0,
				'dependency' => array(
					'parameter' => 'slider_type',
					'value' => array('post', 'woocommerce'),
				)
			),
			'post_link_slide' => array(
				'type' => 'checkbox',
				'label' => $postSliderLabels['link_label'],
				'default' => false,
				'dependency' => array(
					'parameter' => 'slider_type',
					'value' => array('post', 'woocommerce'),
				)
			),
			'post_link_target' => array(
				'type' => 'checkbox',
				'label' => __('Open in new window:', 'motopress-slider-lite'),
				'default' => false,
				'dependency' => array(
					'parameter' => 'post_link_slide',
					'value' => true,
				)
			),
			'post_order_by' => array(
				'type' => 'select',
				'label' => __('Order By:', 'motopress-slider-lite'),
				'default' => 'date',
				'list' => array(
					'date' => 'Date',
					'menu_order' => 'Menu Order',
					'title' => 'Title',
					'id' => 'Id',
					'rand' => 'Random',
					'comments' => 'Comments',
					'date_modified' => 'Date Modified',
					'none' => 'None'
				),
				'dependency' => array(
					'parameter' => 'slider_type',
					'value' => array('post', 'woocommerce'),
				)
			),
			'post_order_direction' => array(
				'type' => 'select',
				'label' => __('Order direction:', 'motopress-slider-lite'),
				'default' => 'DESC',
				'list' => array(
					'DESC' => 'Descending (largest to smallest)',
					'ASC' => 'Ascending (smallest to largest)',
				),
				'dependency' => array(
					'parameter' => 'slider_type',
					'value' => array('post', 'woocommerce'),
				)
			),
		),
	);

	if ($this->sliderType === 'woocommerce') {
		$sliderSettings['post_settings']['options'] = array_merge($sliderSettings['post_settings']['options'], array(
			'wc_only_instock' => array(
				'type' => 'checkbox',
				'label' => __('Only display in-stock products. ', 'motopress-slider-lite'),
				'default' => false,
				'dependency' => array(
					'parameter' => 'slider_type',
					'value' => 'woocommerce',
				)
			),
			'wc_only_featured' => array(
				'type' => 'checkbox',
				'label' => __('Only display featured products. ', 'motopress-slider-lite'),
				'default' => false,
				'dependency' => array(
					'parameter' => 'slider_type',
					'value' => 'woocommerce',
				)
			),
			'wc_only_onsale' => array(
				'type' => 'checkbox',
				'label' => __('Only display on sale products. ', 'motopress-slider-lite'),
				'default' => false,
				'dependency' => array(
					'parameter' => 'slider_type',
					'value' => 'woocommerce',
				)
			)
		));
	}
}


if ($this->sliderType === 'post') {
	$sliderSettings['main']['options']['title']['default'] = __('New Posts Slider', 'motopress-slider-lite');
} else if ($this->sliderType === 'woocommerce') {
	$sliderSettings['main']['options']['title']['default'] = __('New WooCommerce Slider', 'motopress-slider-lite');
}

return $sliderSettings;