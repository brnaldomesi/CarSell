/**
 * Plugin front end scripts
 *
 * @package WooBuilder_Blocks
 * @version 1.0.0
 */
jQuery( function ( $ ) {

	function getAPIURL( endpoint, queryString, attr ) {
		if ( ! queryString ) {
			queryString = '';
		} else {
			queryString += '&';
		}
		if ( attr ) {
			queryString = queryString + decodeURIComponent( $.param( attr ) ).replace( /\+/g, ' ' );
		}
		return '/woobuilder_blocks/v1/' + endpoint + '?post=' + woobuilderData.post + '&' + queryString
	}

	function woobFields( fields ) {
		if ( ! fields ) {
			fields = {};
		}

		fields['font'] = {
			label  : 'Font',
			type   : 'font',
			section: 'Typography',
		};
		fields['font_size'] = {
			label  : 'Font Size',
			default: 16,
			type   : 'range',
			section: 'Typography',
		};
		fields['text_color'] = {
			label  : 'Text color',
			type   : 'color',
			section: 'Typography',
			default: '',
		};

		return fields;
	}

	function woobApiCallbackGenerator( title, setupJSBlocks ) {
		return function ( props, that ) {
			if ( props.apiData && props.apiData.data ) {
				setupJSBlocks && setTimeout( window.WoobuilderBlocksSetup, 500 );
				return Caxton.html2el( props.apiData.data, {
					className: 'woocommerce',
					key      : 'block-html',
					style    : {},
					onClick  : function ( e ) {
						e.preventDefault();
					}
				} );
			} else {
				return wp.element.createElement( 'div', {
					className: 'caxton-notification',
					key      : 'notice'
				}, 'Loading ' + title + '...' );
			}
		};
	}

	// region WooBuilder: Template

	CaxtonLayoutOptionsBlock(
		{
			debug: 1,
			id      : 'woobuilder/tpl',
			title   : 'Woobuilder Template',
			category: 'woobuilder',
		},
		[
			{
				title: 'Classic',
				img  : woobuilderData.img_url + 'classic.png',
				props: {
					tpl: [
						[
							'caxton/grid',
							{
								"tpl": '[' +
											 '["caxton/section", {"Grid area": "span 1/span 6", "tpl": "[[\\"woobuilder/images\\", {}]]" }],' +
											 '["caxton/section", {"Grid area": "span 1/span 6", "tpl": "[[\\"woobuilder/title\\", {}], [\\"woobuilder/rating\\", {}], [\\"woobuilder/product-price\\", {}], [\\"woobuilder/excerpt\\", {}], [\\"woobuilder/add-to-cart\\", {}], [\\"woobuilder/meta\\", {}]]" }]' +
											 ']',
							}
						]
					],
				}
			},
			{
				title: 'Classic image right',
				img  : woobuilderData.img_url + 'classic-right.png',
				props: {
					tpl: [
						[
							'caxton/grid',
							{
								"tpl": '[' +
											 '["caxton/section", {"Grid area": "span 1/span 6", "tpl": "[[\\"woobuilder/title\\", {}], [\\"woobuilder/rating\\", {}], [\\"woobuilder/product-price\\", {}], [\\"woobuilder/excerpt\\", {}], [\\"woobuilder/add-to-cart\\", {}], [\\"woobuilder/meta\\", {}]]" }],' +
											 '["caxton/section", {"Grid area": "span 1/span 6", "tpl": "[[\\"woobuilder/images\\", {}]]" }]' +
											 ']',
							}
						]
					],
				}
			}
		]
	);

	// endregion WooBuilder: Template

	// region WooBuilder: Product rating

	CaxtonBlock( {
		id         : 'woobuilder/rating',
		title      : 'WooBuilder: Product rating',
		icon       : 'star-filled',
		category   : 'woobuilder',
		apiUrl     : function ( props ) {
			var
				attr = props.attributes,
				qry  = '';
			return {
				apiData: getAPIURL( 'rating', qry, attr ),
			};
		},
		apiCallback: woobApiCallbackGenerator( 'Product rating' ),
		fields     : woobFields( {
//			'Text color': {
//				type: 'color',
//				default: '#fff',
//				section: 'Typography',
//			},
		} ),
	} );

	// endregion WooBuilder: Product rating

	// region WooBuilder: Product title

	CaxtonBlock( {
		id         : 'woobuilder/title',
		title      : 'WooBuilder: Product title',
		icon       : 'editor-textcolor',
		category   : 'woobuilder',
		apiUrl     : function ( props ) {
			var
				attr = props.attributes,
				qry  = '';
			return {
				apiData: getAPIURL( 'title', qry, attr ),
			};
		},
		apiCallback: woobApiCallbackGenerator( 'Product title' ),
		fields     : woobFields( {
//			'Text color': {
//				type: 'color',
//				default: '#fff',
//				section: 'Typography',
//			},
		} ),
	} );

	// endregion WooBuilder: Product title

	// region WooBuilder: Add to cart

	CaxtonBlock( {
		id         : 'woobuilder/add-to-cart',
		title      : 'WooBuilder: Add to cart',
		icon       : 'cart',
		category   : 'woobuilder',
		apiUrl     : function ( props ) {
			var
				attr = props.attributes,
				qry  = '';
			return {
				apiData: getAPIURL( 'add_to_cart', qry, attr ),
			};
		},
		apiCallback: woobApiCallbackGenerator( 'Add to cart', 'runSetup' ),
		fields     : woobFields( {
			'woobuilder_style': {
				label  : 'Outlined button and input',
				type   : 'checkbox',
				value  : '1',
				section: 'Layout',
			},
		} ),
	} );

	// endregion WooBuilder: Add to cart

	// region WooBuilder: Sale Countdown

	CaxtonBlock( {
		id         : 'woobuilder/sale-counter',
		title      : 'WooBuilder: Sale Countdown',
		icon       : 'clock',
		category   : 'woobuilder',
		apiUrl     : function ( props ) {
			var
				attr = props.attributes,
				qry  = '';
			return {
				apiData: getAPIURL( 'sale_counter', qry, attr ),
			};
		},
		apiCallback: woobApiCallbackGenerator( 'Sale Countdown', 'runSetup' ),
		fields     : woobFields( {
			'active_color'    : {
				label  : 'Arc color',
				type   : 'color',
				default: '#555',
				section: 'Layout',
			},
			'track_width'     : {
				label  : 'Circle width',
				type   : 'number',
				default: '2',
				section: 'Layout',
			},
			'track_color'     : {
				label  : 'Circle color',
				type   : 'color',
				default: '#ddd',
				section: 'Layout',
			},
			'woobuilder_style': {
				label  : 'Timer dial position',
				type   : 'select',
				options: [
					{value: '', label: 'Behind text',},
					{value: 'above', label: 'Above text',},
					{value: 'below', label: 'Below text',},
				],
				section: 'Layout',
			},
		} ),
	} );

	// endregion WooBuilder: Sale Countdown

	// region WooBuilder: Product price

	CaxtonBlock( {
		id         : 'woobuilder/product-price',
		title      : 'WooBuilder: Product price',
		icon       : 'tag',
		category   : 'woobuilder',
		apiUrl     : function ( props ) {
			var
				attr = props.attributes,
				qry  = '';
			return {
				apiData: getAPIURL( 'product_price', qry, attr ),
			};
		},
		apiCallback: woobApiCallbackGenerator( 'Product price' ),
		fields     : woobFields( {
//			'Text color': {
//				type: 'color',
//				default: '#fff',
//				section: 'Typography',
//			},
		} ),
	} );

	// endregion WooBuilder: Product price

	// region WooBuilder: Product tabs

	CaxtonBlock( {
		id         : 'woobuilder/tabs',
		title      : 'WooBuilder: Product tabs',
		icon       : 'tag',
		category   : 'woobuilder',
		apiUrl     : function ( props ) {
			var
				attr = props.attributes,
				qry  = '';
			return {
				apiData: getAPIURL( 'tabs', qry, attr ),
			};
		},
		apiCallback: woobApiCallbackGenerator( 'Product tabs' ),
		fields     : woobFields( {
			'desc': {
				label: 'Product Description',
				type : 'textarea',
			},
		} ),
	} );

	// endregion WooBuilder: Product tabs

	// region WooBuilder: Related products

	CaxtonBlock( {
		id         : 'woobuilder/related-products',
		title      : 'WooBuilder: Related products',
		icon       : 'products',
		category   : 'woobuilder',
		apiUrl     : function ( props ) {
			var
				attr = props.attributes,
				qry  = '';
			return {
				apiData: getAPIURL( 'related_products', qry, attr ),
			};
		},
		apiCallback: woobApiCallbackGenerator( 'Related products' ),
		fields     : woobFields( {
//			'Text color': {
//				type: 'color',
//				default: '#fff',
//				section: 'Typography',
//			},
		} ),
	} );

	// endregion WooBuilder: Related products

	// region WooBuilder: Product Short Description

	CaxtonBlock( {
		id         : 'woobuilder/excerpt',
		title      : 'WooBuilder: Product Short Description',
		icon       : 'editor-justify',
		category   : 'woobuilder',
		apiUrl     : function ( props ) {
			var
				attr = props.attributes,
				qry  = '';
			return {
				apiData: getAPIURL( 'excerpt', qry, attr ),
			};
		},
		apiCallback: woobApiCallbackGenerator( 'Product Short Description' ),
		fields     : woobFields( {
//			'Text color': {
//				type: 'color',
//				default: '#fff',
//				section: 'Typography',
//			},
		} ),
	} );

	// endregion WooBuilder: Product Short Description

	// region WooBuilder: Product Meta

	CaxtonBlock( {
		id         : 'woobuilder/meta',
		title      : 'WooBuilder: Product Meta',
		icon       : 'format-aside',
		category   : 'woobuilder',
		apiUrl     : function ( props ) {
			var
				attr = props.attributes,
				qry  = '';
			return {
				apiData: getAPIURL( 'meta', qry, attr ),
			};
		},
		apiCallback: woobApiCallbackGenerator( 'Product Meta' ),
		fields     : woobFields( {
//			'Text color': {
//				type: 'color',
//				default: '#fff',
//				section: 'Typography',
//			},
		} ),
	} );

	// endregion WooBuilder: Product Meta

	// region WooBuilder: Product Reviews

	CaxtonBlock( {
		id         : 'woobuilder/reviews',
		title      : 'WooBuilder: Product Reviews',
		icon       : 'archive',
		category   : 'woobuilder',
		apiUrl     : function ( props ) {
			var
				attr = props.attributes,
				qry  = '';
			return {
				apiData: getAPIURL( 'reviews', qry, attr ),
			};
		},
		apiCallback: woobApiCallbackGenerator( 'Product Reviews' ),
		fields     : woobFields( {
//			'Text color': {
//				type: 'color',
//				default: '#fff',
//				section: 'Typography',
//			},
		} ),
	} );

	// endregion WooBuilder: Product Reviews

	// region WooBuilder: Product Images

	CaxtonBlock( {
		id         : 'woobuilder/images',
		title      : 'WooBuilder: Product Images',
		icon       : 'images-alt2',
		category   : 'woobuilder',
		apiUrl     : function ( props ) {
			var
				attr = props.attributes,
				qry  = '';
			return {
				apiData: getAPIURL( 'images', qry, attr ),
			};
		},
		apiCallback: woobApiCallbackGenerator( 'Product Images' ),
		fields     : woobFields( {
			'woobuilder_style': {
				label  : 'Gallery images',
				type: 'select',
				options: [
					{value: '', label: 'Default',},
					{value: 'hide-gallery', label: 'Hide',},
					{value: 'left-gallery', label: 'Left',},
					{value: 'right-gallery', label: 'Right',},
				],
				section: 'Layout',
			}
		} ),
	} );

	// endregion WooBuilder: Product Images

	// region WooBuilder: Product Images Carousel

	CaxtonBlock( {
		id         : 'woobuilder/images-carousel',
		title      : 'WooBuilder: Product Images Carousel',
		icon       : 'slides',
		category   : 'woobuilder',
		apiUrl     : function ( props ) {
			var
				attr = props.attributes,
				qry  = '';
			return {
				apiData: getAPIURL( 'images_carousel', qry, attr ),
			};
		},
		apiCallback: woobApiCallbackGenerator( 'Product Images Carousel', 1 ),
		fields     : woobFields( {
//			'woobuilder_style': {
//				label  : 'Hide gallery image',
//				type   : 'checkbox',
//				value  : 'hide-gallery',
//				section: 'Layout',
//			}
		} ),
	} );

	// endregion WooBuilder: Product Images Carousel

	function WooBuilderButtons() {
		var el = wp.element.createElement;
		return [
			el(
				wp.editPost.PluginPostStatusInfo,
				{
					className: 'woobuilder-switch-to-default',
					key: 'switch2default',
				},
				el(
					'a',
					{
						id: 'woobuilder-switch-to-default',
						className: 'button-link-delete components-button editor-post-trash is-button is-default is-large',
						onClick  : function () {
							var sure = confirm( 'Are you sure You want to revert to default editor?' );
							if ( sure ) {
								window.location = woobuilderData.switchToDefaultEditorUrl
							}
						},
					},
					'Switch to default editor'
				)
			),
		];
	}

	wp.plugins.registerPlugin( 'woobuilder', {
		render: WooBuilderButtons,
	} );
} );
