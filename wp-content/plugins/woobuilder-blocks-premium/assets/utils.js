/**
 * Plugin front end scripts
 *
 * @package WooBuilder_Blocks
 * @version 1.0.0
 */

window.WoobuilderBlocksSetup = function () {

	var $ = jQuery;

	// region Select control
	$( '.woobuilder-block select' ).each( function () {
		var $t = $( this );
		if ( ! $t.closest( '.woobuilder-select-wrap' ).length ) {
			$t.wrap( '<span class="woobuilder-select-wrap"></span>' );
		}
	} );
	// endregion Select control

	// region Images carousel
	$( '.woobuilder-images_carousel' ).flexslider( {
		move              : 1,
		animation         : "slide",
		animationLoop     : false,
		itemWidth         : 400,
		itemMargin        : 7,
		minItems          : 1.25,
		maxItems          : 1.8,
		customDirectionNav: $( '.woobuilder-images_carousel-navigation a' ),
		start: function() {
			$( '.woobuilder-images_carousel.o-0' ).removeClass( 'o-0' );
		}
	} );
	// endregion Images carousel

	// region Sales Countdown
	var salesCounter = $( '.woobuilder-sale_counter' );

	if ( salesCounter.length ) {
		var
			date      = salesCounter.data( 'date-end' ),
			timeParts = ['days', 'hours', 'minutes', 'seconds'],
			timeEls   = {};

		for ( var i = 0; i < timeParts.length; i ++ ) {
			timeEls[timeParts[i]] = {
				circ: salesCounter.find( '.woob-timr-arc-' + timeParts[i] ),
				num : salesCounter.find( '.woob-timr-number-' + timeParts[i] ),
			};
		}

		timeEls['days'].max = 31;
		timeEls['hours'].max = 24;
		timeEls['minutes'].max = 60;
		timeEls['seconds'].max = 60;

		setInterval( function () {
			var
				dt      = new Date(),
				timeNow = Math.floor( dt.getTime() / 1000 ),
				diff    = date - timeNow;
			timeEls['days'].val = Math.floor( diff / (
				60 * 60 * 24
			) );
			timeEls['hours'].val = Math.floor( diff % (
				60 * 60 * 24
			) / (
																					 60 * 60
																				 ) );
			timeEls['minutes'].val = Math.floor( diff % (
				60 * 60
			) / 60 );
			timeEls['seconds'].val = Math.floor( diff % 60 );

			for ( var j = 0; j < timeParts.length; j ++ ) {
				var els = timeEls[timeParts[j]];
				els.circ.attr( 'stroke-dasharray', els.val * 100 / els.max + ',100' );
				els.num.html( els.val );
			}

		}, 1000 );
	}
	// endregion Sales Countdown
};

jQuery( window.WoobuilderBlocksSetup );