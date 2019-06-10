/**
 * Cherry WooCommerce package scripts
 */

/*
 * debouncedresize: special jQuery event that happens once after a window resize
 *
 * Copyright 2012 @louis_remi
 * Licensed under the MIT license.
 */
(function($) {

	var $event = $.event,
		$special,
		resizeTimeout;

	$special = $event.special.debouncedresize = {
		setup: function() {
			$( this ).on( "resize", $special.handler );
		},
		teardown: function() {
			$( this ).off( "resize", $special.handler );
		},
		handler: function( event, execAsap ) {
			// Save the context
			var context = this,
				args = arguments,
				dispatch = function() {
					// set correct event type
					event.type = "debouncedresize";
					$event.dispatch.apply( context, args );
				};

			if ( resizeTimeout ) {
				clearTimeout( resizeTimeout );
			}

			execAsap ?
				dispatch() :
				resizeTimeout = setTimeout( dispatch, $special.threshold );
		},
		threshold: 150
	};

})(jQuery);

jQuery(document).ready(function($) {

	//Dropdown account in header
	$('.cherry-wc-account_title').click(function(event){
		event.preventDefault();
		event.stopPropagation();
		if( $(this).hasClass('cherry-dropdown-opened') ) {
			$(this).removeClass('cherry-dropdown-opened')
			$(this).parent().find('.cherry-wc-account_content').slideUp(300).removeClass('opened');
		} else {
			$(this).addClass('cherry-dropdown-opened')
			$(this).parent().find('.cherry-wc-account_content').slideDown(300).addClass('opened');
		}
	});

	$(document).on('click', 'body', function(event) {
		$(this).find('.cherry-wc-account_content.opened').slideUp(300).removeClass('opened');
		$(this).find('.cherry-dropdown-opened').removeClass('cherry-dropdown-opened');
	});

	$(document).on('click', '.cherry-wc-account_content', function(event) {
		event.stopPropagation();
	})

	$('.sf-menu > li > .cherry-badge').each(function(){
		$(this).append('<b class="cherry-badge-content">' + $(this).data('badge-text') + '</b>');
	});


	// product carousel init
	$('.cherry_wc_product_carousel').each(function(index, el) {
		var params = $(this).data('params');
		$(this).find('>ul').owlCarousel(params);
	});

	// quick view
	$(document).on('click', '.cherry-quick-view', function(event) {

		event.preventDefault();
		event.stopPropagation();
		event.stopImmediatePropagation();

		var product_id = $(this).data('product'),
			item = $(this).parents('li.product'),
			current_popup = 'cherry-quick-view-popup-' + product_id;

		var send_ajax_request = function() {
			jQuery.ajax({
				type : "post",
				dataType : "json",
				url : cherry_wc_data.ajax_url,
				data : {
					action: 'cherry_wc_quick_view',
					_wpnonce: cherry_wc_data.nonce,
					product: product_id
				},
				success: function(response) {
					$('#'+current_popup).find('.cherry-quick-view-popup-content').html(response.content);
					if ( $.isFunction( jQuery.fn.prettyPhoto ) ) {
						$(".cherry-quick-view-images a.zoom").prettyPhoto({
							hook: 'data-rel',
							social_tools: false,
							theme: 'pp_woocommerce',
							horizontal_padding: 20,
							opacity: 0.8,
							deeplinking: false
						});
					}
				}
			})
		}

		if ( !item.find('.cherry-quick-view-popup').length ) {
			item.append('<div id="' + current_popup + '" class="cherry-quick-view-popup mfp-hide"><span href="#" class="mfp-close">&times;</span><div class="cherry-quick-view-popup-content"><div class="cherry-quick-view-load">' + cherry_wc_data.loading + '</div></div></div>');
			send_ajax_request();
		}

		if ( $.isFunction( jQuery.fn.magnificPopup ) ) {
			$.magnificPopup.open({
				items: {
					src: '#' + current_popup
				},
				type: 'inline'
			}, 0);
		}

		return false;

	});

	function zoomInit() {

		if ( $('.product-large-image img').length <= 0 ) {
			return;
		}

		$('.zoomContainer').remove();
		if ( $.isFunction( jQuery.fn.elevateZoom ) ) {
			$('.product-large-image img').elevateZoom({
				zoomType: "inner",
				cursor: "crosshair",
				zoomWindowFadeIn: 500,
				zoomWindowFadeOut: 750
			});
		}
	}

	// single product page
	$(document).on('click', '.product-thumbnails_item', function(event) {

		event.preventDefault();
		var _this      = $(this);
			_parent    = _this.parents('.product-images'),
			_large_img = _this.attr('data-large-img'),
			_orig_img  = _this.attr('data-original-img');

		if ( $( '.placeholder-thumb', _this ).length > 0 ) {
			return;
		}

		_this.addClass('active').siblings().removeClass('active');
		_parent.find('.product-large-image img').attr('src', _large_img);
		_parent.find('.product-large-image img').attr('data-zoom-image', _orig_img);
		zoomInit();
	});

	zoomInit();

	var initial_width = current_width = $('#motopress-main').width(),
		reinit        = false;

	function reinit_scripts() {
		reinit        = false;
		current_width = $('#motopress-main').width();

		if ( initial_width > 979 && current_width <= 979) {
			reinit = true;
		} else if ( initial_width <= 979 && current_width > 979 ) {
			reinit = true;
		} else if ( initial_width > 450 && current_width <= 450 ) {
			reinit = true;
		} else if ( initial_width <= 450 && current_width > 450 ) {
			reinit = true;
		}

		if ( true == reinit ) {
			initial_width = current_width;
			zoomInit();
			if ( $.isFunction( jQuery.fn.cycle ) ) {
				$('.cycle-slideshow').cycle('reinit');
			}
		}

	}

	$(window).on( "orientationchange debouncedresize", reinit_scripts );

	//Change variation images on variation change
	$( document ).on( 'found_variation', function( event, variation ) {

		var thumb, largeImg, item, image;

		event.preventDefault();

		// jscs:disable
		thumb    = variation.image_src,
		largeImg = variation.image_link,
		// jscs:enable

		item     = $( '.product-large-image' ),
		image    = $( 'img', item );

		if ( '' === thumb || '' === largeImg ) {
			thumb    = image.data( 'initial-thumb' ),
			largeImg = image.data( 'initial-thumb-large' );
		} else if ( $( '.product-thumbnails_item.active-image' ).length > 0 ) {
			$( '.product-thumbnails_item.active-image' ).removeClass( 'active-image' );
		} else if ( $( '.owl-item.active-image' ).length > 0 ) {
			$( '.owl-item.active-image' ).removeClass( 'active-image' );
		}

		image.attr( 'src', thumb ).data( 'zoom-image', largeImg ).attr( 'data-zoom-image', largeImg );
		zoomInit();
	});

	$( document ).on( 'reset_data', function( event ) {

		var item, image, initialThmb, initialLrg;

		event.preventDefault();

		item        = $( '.product-large-image' ),
		image       = $( 'img', item ),
		initialThmb = image.data( 'initial-thumb' ),
		initialLrg  = image.data( 'initial-thumb-large' );

		image.attr( 'src', initialThmb ).data( 'zoom-image', initialLrg ).attr( 'data-zoom-image', initialLrg );
		zoomInit();
	});

	/**
	 * Open sharing popup
	 */
	$(document).on('click', '.share-buttons_link', function(event) {
		event.preventDefault();
		var width  = 816,
			height = 400,
			url    = $(this).data('url');

		var leftPosition, topPosition;
		//Allow for borders.
		leftPosition = (window.screen.width / 2) - ((width / 2) + 10);
		//Allow for title and status bars.
		topPosition = (window.screen.height / 2) - ((height / 2) + 50);
		//Open the window.
		window.open(url, "Share this", "status=no,height=" + height + ",width=" + width + ",resizable=yes,left=" + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY=" + topPosition + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no");
	});


});