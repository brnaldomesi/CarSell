(function($) {
    $(function(){
		//Dropdown cart in header
		$('.cart-holder > h3').click(function(){
			if($(this).hasClass('cart-opened')) {
				$(this).removeClass('cart-opened').next().slideUp(300);
			} else {
				$(this).addClass('cart-opened').next().slideDown(300);
			}
		});
		//Popup rating content
		$('.star-rating').each(function(){
			rate_cont = $(this).attr('title');
			$(this).append('<b class="rate_content">' + rate_cont + '</b>');
		});

		//Disable cart selection
		(function ($) {
            $.fn.disableSelection = function () {
                return this
                    .attr('unselectable', 'on')
                    .css('user-select', 'none')
                    .on('selectstart', false);
            };
            $('.cart-holder h3').disableSelection();
        })(jQuery);

		//Fix contact form not valid messages errors
		jQuery(window).load(function() {
			jQuery('.wpcf7-not-valid-tip').live('mouseover', function(){
				jQuery(this).fadeOut();
			});

			jQuery('.wpcf7-form input[type="reset"]').live('click', function(){
				jQuery('.wpcf7-not-valid-tip, .wpcf7-response-output').fadeOut();
			});
		});

		// compare trigger
		$(document).on('click', '.cherry-compare', function(event) {
			event.preventDefault();
			button = $(this);
			$('body').trigger( 'yith_woocompare_open_popup', { response: compare_data.table_url, button: button } )
		});

    });
    
    $.fn.splitWords = function(index) {
        /*
            If index is specified the sentence will split at that point
            (minus index counts from end). Otherwise sentence is split in two.
        */

        return this.each(function() {

            var el = $(this),
                i, first, words = el.text().split(/\s/);


            if (typeof index === 'number') {
                i = (index > 0) ? index : words.length + index;
            }
            else {
                i = Math.floor(words.length / 2);
            }

            first = words.splice(0, i);

            el.empty().
                append(makeWrapElem(1, first)).
                append(makeWrapElem(2, words));
        });
    };

    function makeWrapElem(i, wordList) {
  if (i != 1) {
         return $('<span class="wrap-' + i + '">' + wordList.join('') + ' </span>');
  } else {
   return $('<b>' + wordList.join(' ') + '</b> ');
  }
    }
    
    
    $('ul.products li.product').each(function(){
       _this = $(this);
       _this.find('.short_desc').after(_this.find('.star-rating'));
       _this.find('ins').after(_this.find('del'));
       _this.find('.product-list-buttons .yith-wcwl-add-to-wishlist').after(_this.find('.product-list-buttons .compare'));
      });
      
      
      
      
})(jQuery);

jQuery(".logo .logo_h__txt .logo_link").splitWords(1);

  