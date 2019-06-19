// SCRIPT Woo Layout Injector version: 4.5, script.js

jQuery(document).ready(function () {

    if (jQuery('.single-product .sb_woo_product_image').length) {
        if (!jQuery('.single-product .sb_woo_product_image').hasClass('sb_woo_image_disable_zoom')) {
            jQuery('.single-product .sb_woo_product_image').zoom({
                callback: function callback() {
                    if (!jQuery('.single-product .sb_woo_product_image').hasClass('sb_woo_image_disable_lightbox')) {
                        jQuery(this).colorbox({
                            href: jQuery('.single-product .sb_woo_product_image img').attr('src')
                        });
                    }
                }
            });
        } else {
            if (!jQuery('.single-product .sb_woo_product_image').hasClass('sb_woo_image_disable_lightbox')) {
                jQuery('.sb_woo_product_image').colorbox({
                    href: jQuery('.single-product .sb_woo_product_image img').attr('src')
                });
            }
        }
    }

    if (jQuery('.single-product .cart.variations_form')) {
        ////////////////////////////////////////////////
        jQuery('.single-product .cart.variations_form .variations .value select').each(function (index, attr) {
            jQuery(this).change(function () {
                sb_woo_variation_image();
            });
        });
        ////////////////////////////////////////////////
    }

    if (jQuery('.woocommerce-remove-coupon').length) {
        jQuery('.et_pb_woo_checkout_coupon').slideUp();
    }

    //to handle removing items from the cart with a blank response. Note to edit this if no empty cart layout specified
    jQuery(document.body).on('wc_fragments_refreshed', function () {
        //if (jQuery('body.woocommerce-cart').length && (!jQuery('.woocommerce-cart-form').length && !jQuery('.sb_et_woo_li_cart_empty').length)) {
        //console.log('Woo Injector Refreshing Cart');
        //location.reload(); //refresh the page
        //}
    });

    if (jQuery('body').hasClass('wli_injected')) {

        if (jQuery('.wpcf7').length > 0) {
            var wli_post_id,
                matches = document.body.className.match(/(^|\s)postid-(\d+)(\s|$)/);
            if (matches) {
                jQuery("input[name='_wpcf7_container_post']").val(matches[2]);
                jQuery(".wpcf7-submit").addClass('button');
                jQuery(".wpcf7-form > p").addClass('form-row');
                jQuery(".wpcf7-form > p .wpcf7-form-control-wrap input").addClass('input-text');
            }
        }

        //to add class of button to thr add to cart ajax function for consistency
        jQuery(document.body).on('added_to_cart', function () {
            setTimeout(function () {
                jQuery('.added_to_cart').addClass('button');
            }, 50);
        });

        //to recalculate the product count in cart
        jQuery(document.body).on('wc_fragments_loaded', function () {
            wli_refresh_cart_count();
        });

        //to recalculate the product count in cart
        jQuery(document.body).on('wc_fragments_refreshed', function () {
            wli_refresh_cart_count();
        });

        //to handle showing the coupon system in a lightbox
        jQuery(document.body).on("checkout_error", function () {

            if (jQuery('.woocommerce-NoticeGroup').length) {
                sb_woo_popup_notice(jQuery('.woocommerce-NoticeGroup').html());
                setTimeout(function () {
                    jQuery('.woocommerce-NoticeGroup').remove();
                }, 250);
            }
        });

        //to handle showing the coupon system in a lightbox
        jQuery(document.body).on("updated_wc_div", function () {

            if (jQuery('.woocommerce .woocommerce-error').length) {
                sb_woo_popup_notice(jQuery('.woocommerce .woocommerce-error'));
                jQuery('.entry-content .woocommerce .woocommerce-error').remove();
            }
            if (jQuery('.woocommerce .woocommerce-message').length) {
                sb_woo_popup_notice(jQuery('.woocommerce .woocommerce-message').clone().wrap("<div />"));
                jQuery('.entry-content .woocommerce .woocommerce-message').remove();
            }
            if (jQuery('.cart-empty').length > 0) {
                jQuery('.et_pb_woo_cart_totals').remove();
            }
        });

        //to handle showing the coupon system in a lightbox
        jQuery(document.body).on("applied_coupon", function () {

            if (jQuery('.woocommerce .woocommerce-error').length) {
                sb_woo_popup_notice(jQuery('.woocommerce .woocommerce-error'));
                jQuery('.entry-content .woocommerce .woocommerce-error').remove();
            }
        });

        //to handle showing the coupon removed in a lightbox
        jQuery(document.body).on("removed_coupon", function () {

            if (jQuery('.woocommerce .woocommerce-message').length) {
                sb_woo_popup_notice(jQuery('.woocommerce .woocommerce-message').clone().wrap("<div />"));
                jQuery('.entry-content .woocommerce .woocommerce-message').remove();
            }
        });

        //to handle showing the coupon system in a lightbox
        jQuery(document.body).on("update_checkout", function () {

            if (jQuery('.wli_wrapper_checkout-form-coupon .woocommerce-error').length) {
                sb_woo_popup_notice(jQuery('.wli_wrapper_checkout-form-coupon .woocommerce-error').clone().wrap("<div />"));
                jQuery('.et_pb_woo_checkout_coupon').slideDown();
            } else if (jQuery('.wli_wrapper_checkout-form-coupon .woocommerce-message').length) {
                sb_woo_popup_notice(jQuery('.wli_wrapper_checkout-form-coupon .woocommerce-message').clone().wrap("<div />"));
                jQuery('.coupon-module').val('');

                if (jQuery('.woocommerce-remove-coupon').length) {
                    jQuery('.et_pb_woo_checkout_coupon').slideDown();
                } else {
                    jQuery('.et_pb_woo_checkout_coupon').slideUp();
                }
            } else if (jQuery('.woocommerce .woocommerce-message').length) {
                sb_woo_popup_notice(jQuery('.woocommerce .woocommerce-message').clone().wrap("<div />"));
                setTimeout(function () {
                    jQuery('.entry-content > .woocommerce > .woocommerce-message').remove();
                }, 250);
            }
        });
    }
});

function wli_refresh_cart_count() {
    var wli_new_count = 0;

    if (jQuery('.sb_woo_mini_cart ul li').length) {
        jQuery('.sb_woo_mini_cart ul li').each(function () {
            var wli_quantity = jQuery(this).children('.quantity').text();
            var wli_quantity_nums = wli_quantity.split(' ');
            var wli_quantity_num = parseInt(wli_quantity_nums[0]);

            wli_new_count += wli_quantity_num;
        });
    }

    if (wli_new_count <= 0) {
        wli_new_count = '';
    }

    jQuery('.sb_woo_prod_cart_container .et-cart-info span').text(wli_new_count);
}

function sb_woo_popup_notice(popup_object) {
    jQuery('html, body').scrollTop(0);

    jQuery.colorbox({
        html: popup_object,
        width: "50%",
        className: "woocommerce"
    });
}

function sb_woo_maybe_submit_checkout_coupon() {
    jQuery(this).keypress(function (e) {
        if (e.which == 13) {
            sb_woo_submit_checkout_coupon();
        }
    });
}

function sb_woo_submit_checkout_coupon() {
    if (jQuery('.coupon-module').length) {
        jQuery('.coupon-module').parent().removeClass('woocommerce-invalid').removeClass('woocommerce-validated');

        var coupon = jQuery('.coupon-module').val();

        if (coupon != '') {
            jQuery('#coupon_code').val(coupon);
            jQuery('.checkout_coupon').submit();
        } else {
            jQuery('.coupon-module').parent().addClass('woocommerce-invalid').removeClass('woocommerce-validated');
        }
    }

    return false;
}

function sb_woo_variation_image() {
    //get variation data and store in sb_woo_attr_data
    var sb_woo_attr_data = jQuery('.single-product .cart.variations_form').data('product_variations');
    var sb_woo_attr_val = '';
    var sb_woo_attr_id = '';
    var sb_woo_attr_name = '';
    var sb_woo_attr_set = [];
    var sb_woo_attr_set_l = 0;
    var sb_woo_attr_set_matched = 0;
    var sb_woo_found_set = [];
    var sb_woo_large_image = '';

    ////////////////////////////////////////////////////

    //cache current variation choices in "sb_woo_attr_set"
    jQuery('.single-product .cart.variations_form .variations .value select').each(function (index2, attr2) {
        sb_woo_attr_val = jQuery(this).val();
        sb_woo_attr_id = jQuery(this).attr('id');
        sb_woo_attr_name = 'attribute_' + sb_woo_attr_id;

        if (sb_woo_attr_val) {
            sb_woo_attr_set.push([sb_woo_attr_name, sb_woo_attr_val]);
            sb_woo_attr_set_l++;
        }
    });

    ////////////////////////////////////////////////////

    if (sb_woo_attr_set_l > 0) {
        //foreach of the stored attribute variables
        jQuery(sb_woo_attr_data).each(function (index3, attr3) {
            //loop variation prices
            var sb_woo_attrs = attr3.attributes;
            sb_woo_attr_set_matched = 0; //reset to 0

            //loop attributes linked to this attribute set
            jQuery(sb_woo_attrs).each(function (index4, attr4) {
                jQuery(attr4).each(function (index4, attr4) {
                    jQuery(sb_woo_attr_set).each(function (index5, attr5) {
                        if (attr4[attr5[0]] == attr5[1] || attr4[attr5[0]] == "") {
                            sb_woo_attr_set_matched++;
                        }
                    });
                });
            });

            if (sb_woo_attr_set_matched >= sb_woo_attr_set_l) {
                sb_woo_found_set = attr3; //we found a matching set... store it!
            }
        });

        if (typeof sb_woo_found_set.image !== 'undefined') {
            sb_woo_large_image = sb_woo_found_set.image.full_src;
        } else {
            sb_woo_large_image = jQuery('.sb_woo_product_thumb_col_num_1 a').data('large_image');
        }

        sb_woo_product_thumb_replace_by_url(sb_woo_large_image, jQuery('.sb_woo_product_image_container')); //we aren't selecting the same element here so just grab the image directly
    }
}

function sb_woo_product_thumb_replace_by_url(large_image, image_object) {
    if (jQuery('.single-product .sb_woo_product_image img').attr('src') == large_image) {
        return;
    }

    var parent_object = image_object.closest('.sb_woo_product_image_container');

    if (parent_object.length == 0) {
        var parent_object = jQuery('.sb_woo_product_image_container');
    }

    if (parent_object.length) {

        parent_object.find('.sb_woo_product_image img').trigger('zoom.destroy'); // remove zoom
        parent_object.find('.sb_woo_product_image img.zoomImg').remove(); //remove old zoom image

        var image_height = parent_object.find('.sb_woo_product_image img').height();

        parent_object.find('.sb_woo_product_image').css('height', image_height + 'px');

        parent_object.find('.sb_woo_product_image img').fadeOut(400, function () {
            parent_object.find('.sb_woo_product_image img').attr('src', large_image);

            parent_object.find('.sb_woo_product_image').imagesLoaded(function () {
                var image_height = parent_object.find('.sb_woo_product_image img').height();

                parent_object.find('.sb_woo_product_image').css('height', image_height + 'px');

                parent_object.find('.sb_woo_product_image img').fadeIn(400, function () {
                    if (!parent_object.find('.sb_woo_product_image').hasClass('sb_woo_image_disable_zoom')) {
                        parent_object.find('.sb_woo_product_image').zoom({
                            callback: function callback() {
                                if (!parent_object.find('.sb_woo_product_image').hasClass('sb_woo_image_disable_lightbox')) {
                                    jQuery(this).colorbox({
                                        href: parent_object.find('.sb_woo_product_image img').attr('src')
                                    });
                                }
                            }
                        });
                    } else {
                        if (!parent_object.find('.sb_woo_product_image').hasClass('sb_woo_image_disable_lightbox')) {
                            jQuery('.sb_woo_product_image').colorbox({
                                href: parent_object.find('.sb_woo_product_image img').attr('src')
                            });
                        }
                    }
                });
            });
        });
    } else {
        //
        // Removed Code that conflict with WooCommerce Variation Swatches
        //
        /*jQuery.colorbox({
            href: large_image
        });*/
    }
}

function sb_woo_product_thumb_replace(image_object) {
    var large_image = image_object.data('large_image');

    sb_woo_product_thumb_replace_by_url(large_image, image_object);
}