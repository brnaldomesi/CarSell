/*!
 * Variation Swatches for WooCommerce v1.0.63 
 * 
 * Author: Emran Ahmed ( emran.bd.08@gmail.com ) 
 * Date: 10/2/2019, 10:06:05 PM
 * Released under the GPLv3 license.
 */
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 9);
/******/ })
/************************************************************************/
/******/ ({

/***/ 10:
/***/ (function(module, exports, __webpack_require__) {

jQuery(function ($) {
    Promise.resolve().then(function () {
        return __webpack_require__(11);
    }).then(function () {
        // Init on Ajax Popup :)
        $(document).on('wc_variation_form', '.variations_form', function () {
            $(this).WooVariationSwatches();
        });

        // Support for Jetpack's Infinite Scroll,
        $(document.body).on('post-load', function () {
            $('.variations_form').each(function () {
                $(this).wc_variation_form();
            });
        });

        // Support for Yith Infinite Scroll
        $(document).on('yith_infs_added_elem', function () {
            $('.variations_form').each(function () {
                $(this).wc_variation_form();
            });
        });

        // Support for Yith Ajax Filter
        $(document).on('yith-wcan-ajax-filtered', function () {
            $('.variations_form').each(function () {
                $(this).wc_variation_form();
            });
        });

        // Support for Woodmart theme
        $(document).on('wood-images-loaded', function () {
            $('.variations_form').each(function () {
                $(this).wc_variation_form();
            });
        });

        // Support for berocket ajax filters
        $(document).on('berocket_ajax_products_loaded', function () {
            $('.variations_form').each(function () {
                $(this).wc_variation_form();
            });
        });

        // Flatsome Infinite Scroll Support
        $('.shop-container .products').on('append.infiniteScroll', function (event, response, path) {
            $('.variations_form').each(function () {
                $(this).wc_variation_form();
            });
        });

        // FacetWP Load More
        $(document).on('facetwp-loaded', function () {
            $('.variations_form').each(function () {
                $(this).wc_variation_form();
            });
        });

        // WooCommerce Filter Nav
        $('body').on('aln_reloaded', function () {
            _.delay(function () {
                $('.variations_form').each(function () {
                    $(this).wc_variation_form();
                });
            }, 100);
        });
    });
}); // end of jquery main wrapper

/***/ }),

/***/ 11:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

// ================================================================
// WooCommerce Variation Change
// ================================================================

var WooVariationSwatches = function ($) {

    var Default = {};

    var WooVariationSwatches = function () {
        function WooVariationSwatches(element, config) {
            _classCallCheck(this, WooVariationSwatches);

            // Assign
            this._element = $(element);
            this._config = $.extend({}, Default, config);
            this._generated = {};
            this._out_of_stock = {};
            this.product_variations = this._element.data('product_variations');
            this.is_ajax_variation = !this.product_variations;
            this.product_id = this._element.data('product_id');
            this.hidden_behaviour = $('body').hasClass('woo-variation-swatches-attribute-behavior-hide');
            this.is_mobile = $('body').hasClass('woo-variation-swatches-on-mobile');

            // Call
            this.init(this.is_ajax_variation, this.hidden_behaviour);
            this.loaded(this.is_ajax_variation, this.hidden_behaviour);
            this.update(this.is_ajax_variation, this.hidden_behaviour);
            this.reset(this.is_ajax_variation, this.hidden_behaviour);

            // Trigger
            $(document).trigger('woo_variation_swatches', [this._element]);
        }

        _createClass(WooVariationSwatches, [{
            key: 'init',
            value: function init(is_ajax, hidden_behaviour) {
                var _this3 = this;

                var _this = this;
                this._element.find('ul.variable-items-wrapper').each(function (i, el) {

                    var select = $(this).siblings('select.woo-variation-raw-select');
                    var li = $(this).find('li');
                    var reselect_clear = $(this).hasClass('reselect-clear');
                    var is_mobile = $('body').hasClass('woo-variation-swatches-on-mobile');

                    $(this).parent().addClass('woo-variation-items-wrapper');

                    // For Avada FIX
                    if (select.length < 1) {
                        select = $(this).parent().find('select.woo-variation-raw-select');
                    }

                    if (reselect_clear) {
                        $(this).on('touchstart click', 'li:not(.selected):not(.radio-variable-item):not(.woo-variation-swatches-variable-item-more)', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            var value = $(this).data('value');
                            select.val(value).trigger('change');
                            select.trigger('click');

                            select.trigger('focusin');

                            if (is_mobile) {
                                select.trigger('touchstart');
                            }

                            $(this).trigger('focus'); // Mobile tooltip
                            $(this).trigger('wvs-selected-item', [value, select, _this._element]); // Custom Event for li
                        });

                        $(this).on('touchstart click', 'li.selected:not(.radio-variable-item)', function (e) {
                            e.preventDefault();
                            e.stopPropagation();

                            var value = $(this).val();

                            select.val('').trigger('change');
                            select.trigger('click');

                            select.trigger('focusin');

                            if (is_mobile) {
                                select.trigger('touchstart');
                            }

                            $(this).trigger('focus'); // Mobile tooltip

                            $(this).trigger('wvs-unselected-item', [value, select, _this._element]); // Custom Event for li
                        });

                        // RADIO
                        $(this).on('touchstart click', 'input.wvs-radio-variable-item:radio', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            $(this).trigger('change');
                        });

                        $(this).on('change', 'input.wvs-radio-variable-item:radio', function (e) {
                            var _this2 = this;

                            e.preventDefault();
                            e.stopPropagation();

                            var value = $(this).val();

                            if ($(this).parent('li.radio-variable-item').hasClass('selected')) {
                                select.val('').trigger('change');
                                _.delay(function () {
                                    $(_this2).prop('checked', false);
                                    $(_this2).parent('li.radio-variable-item').trigger('wvs-unselected-item', [value, select, _this._element]); // Custom Event for li
                                }, 1);
                            } else {
                                select.val(value).trigger('change');
                                $(this).parent('.radio-variable-item').trigger('wvs-selected-item', [value, select, _this._element]); // Custom Event for li
                            }

                            select.trigger('click');
                            select.trigger('focusin');
                            if (is_mobile) {
                                select.trigger('touchstart');
                            }
                        });
                    } else {
                        $(this).on('touchstart click', 'li:not(.radio-variable-item):not(.woo-variation-swatches-variable-item-more)', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            var value = $(this).data('value');
                            select.val(value).trigger('change');
                            select.trigger('click');
                            select.trigger('focusin');
                            if (is_mobile) {
                                select.trigger('touchstart');
                            }

                            $(this).trigger('focus'); // Mobile tooltip

                            $(this).trigger('wvs-selected-item', [value, select, _this._element]); // Custom Event for li
                        });

                        // Radio
                        $(this).on('change', 'input.wvs-radio-variable-item:radio', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            var value = $(this).val();

                            select.val(value).trigger('change');
                            select.trigger('click');
                            select.trigger('focusin');

                            if (is_mobile) {
                                select.trigger('touchstart');
                            }

                            // Radio
                            $(this).parent('li.radio-variable-item').removeClass('selected disabled').addClass('selected');
                            $(this).parent('li.radio-variable-item').trigger('wvs-selected-item', [value, select, _this._element]); // Custom Event for li
                        });
                    }
                });

                _.delay(function () {
                    _this3._element.trigger('reload_product_variations');
                    _this3._element.trigger('woo_variation_swatches_init', [_this3, _this3.product_variations]);
                    $(document).trigger('woo_variation_swatches_loaded', [_this3._element, _this3.product_variations]);
                }, 1);
            }
        }, {
            key: 'loaded',
            value: function loaded(is_ajax, hidden_behaviour) {
                if (!is_ajax) {
                    this._element.on('woo_variation_swatches_init', function (event, object, product_variations) {

                        object._generated = product_variations.reduce(function (obj, variation) {

                            Object.keys(variation.attributes).map(function (attribute_name) {
                                if (!obj[attribute_name]) {
                                    obj[attribute_name] = [];
                                }

                                if (variation.attributes[attribute_name]) {
                                    obj[attribute_name].push(variation.attributes[attribute_name]);
                                }
                            });

                            return obj;
                        }, {});

                        object._out_of_stock = product_variations.reduce(function (obj, variation) {

                            Object.keys(variation.attributes).map(function (attribute_name) {
                                if (!obj[attribute_name]) {
                                    obj[attribute_name] = [];
                                }

                                if (variation.attributes[attribute_name] && !variation.is_in_stock) {
                                    obj[attribute_name].push(variation.attributes[attribute_name]);
                                }
                            });

                            return obj;
                        }, {});

                        // console.log(object._out_of_stock);

                        $(this).find('ul.variable-items-wrapper').each(function () {
                            var li = $(this).find('li:not(.woo-variation-swatches-variable-item-more)');
                            var attribute = $(this).data('attribute_name');
                            var attribute_values = object._generated[attribute];
                            var out_of_stock_values = object._out_of_stock[attribute];

                            //console.log(out_of_stock_values)

                            li.each(function () {
                                var attribute_value = $(this).attr('data-value');

                                // if (!_.isEmpty(attribute_values) && !_.contains(attribute_values, attribute_value)){}

                                if (!_.isEmpty(attribute_values) && _.indexOf(attribute_values, attribute_value) === -1) {
                                    $(this).removeClass('selected');
                                    $(this).addClass('disabled');

                                    if ($(this).hasClass('radio-variable-item')) {
                                        $(this).find('input.wvs-radio-variable-item:radio').prop('disabled', true).prop('checked', false);
                                    }
                                }
                            });
                        });
                    });
                }
            }
        }, {
            key: 'reset',
            value: function reset(is_ajax, hidden_behaviour) {
                var _this = this;
                this._element.on('reset_data', function (event) {
                    $(this).find('ul.variable-items-wrapper').each(function () {
                        var li = $(this).find('li');
                        li.each(function () {
                            if (!is_ajax) {
                                $(this).removeClass('selected disabled');

                                if ($(this).hasClass('radio-variable-item')) {
                                    $(this).find('input.wvs-radio-variable-item:radio').prop('disabled', false).prop('checked', false);
                                }
                            } else {
                                if ($(this).hasClass('radio-variable-item')) {
                                    //    $(this).find('input.wvs-radio-variable-item:radio').prop('checked', false);
                                }
                            }

                            $(this).trigger('wvs-unselected-item', ['', '', _this._element]); // Custom Event for li
                        });
                    });
                });
            }
        }, {
            key: 'update',
            value: function update(is_ajax, hidden_behaviour) {

                this._element.on('__found_variation', function (event, variation) {

                    //console.log(this.$attributeFields);

                    /*  _.delay(() => {
                          $(this).find('ul.variable-items-wrapper').each(function () {
                              let attribute_name = $(this).data('attribute_name');
                               $(this).find('li').each(function () {
                                  let value = $(this).attr('data-value');
                                   console.log(variation)
                                   if (variation.attributes[attribute_name] === value && !variation.is_in_stock) {
                                      $(this).addClass('disabled');
                                  }
                               });
                          });
                       }, 2)*/
                });

                this._element.on('woocommerce_variation_has_changed', function (event) {
                    if (is_ajax) {
                        $(this).find('ul.variable-items-wrapper').each(function () {
                            var _this4 = this;

                            var selected = '',
                                options = $(this).siblings('select.woo-variation-raw-select').find('option'),
                                current = $(this).siblings('select.woo-variation-raw-select').find('option:selected'),
                                eq = $(this).siblings('select.woo-variation-raw-select').find('option').eq(1),
                                li = $(this).find('li'),
                                selects = [];

                            // For Avada FIX
                            if (options.length < 1) {
                                options = $(this).parent().find('select.woo-variation-raw-select').find('option');
                                current = $(this).parent().find('select.woo-variation-raw-select').find('option:selected');
                                eq = $(this).parent().find('select.woo-variation-raw-select').find('option').eq(1);
                            }

                            options.each(function () {
                                if ($(this).val() !== '') {
                                    selects.push($(this).val());
                                    selected = current ? current.val() : eq.val();
                                }
                            });

                            _.delay(function () {
                                li.each(function () {
                                    var value = $(this).attr('data-value');
                                    $(this).removeClass('selected disabled');

                                    if (value === selected) {
                                        $(this).addClass('selected');
                                        if ($(this).hasClass('radio-variable-item')) {
                                            $(this).find('input.wvs-radio-variable-item:radio').prop('disabled', false).prop('checked', true);
                                        }
                                    }
                                });

                                // Items Updated
                                $(_this4).trigger('wvs-items-updated');
                            }, 1);
                        });
                    }
                });

                // WithOut Ajax Update
                this._element.on('woocommerce_update_variation_values', function (event) {
                    $(this).find('ul.variable-items-wrapper').each(function () {
                        var _this5 = this;

                        var selected = '',
                            options = $(this).siblings('select.woo-variation-raw-select').find('option'),
                            current = $(this).siblings('select.woo-variation-raw-select').find('option:selected'),
                            eq = $(this).siblings('select.woo-variation-raw-select').find('option').eq(1),
                            li = $(this).find('li:not(.woo-variation-swatches-variable-item-more)'),
                            selects = [];

                        // For Avada FIX
                        if (options.length < 1) {
                            options = $(this).parent().find('select.woo-variation-raw-select').find('option');
                            current = $(this).parent().find('select.woo-variation-raw-select').find('option:selected');
                            eq = $(this).parent().find('select.woo-variation-raw-select').find('option').eq(1);
                        }

                        options.each(function () {
                            if ($(this).val() !== '') {
                                selects.push($(this).val());
                                selected = current ? current.val() : eq.val();
                            }
                        });

                        _.delay(function () {
                            li.each(function () {
                                var value = $(this).attr('data-value');
                                $(this).removeClass('selected disabled').addClass('disabled');

                                // if (_.contains(selects, value))

                                if (_.indexOf(selects, value) !== -1) {

                                    $(this).removeClass('disabled');

                                    $(this).find('input.wvs-radio-variable-item:radio').prop('disabled', false);

                                    if (value === selected) {

                                        $(this).addClass('selected');

                                        if ($(this).hasClass('radio-variable-item')) {
                                            $(this).find('input.wvs-radio-variable-item:radio').prop('checked', true);
                                        }
                                    }
                                } else {

                                    if ($(this).hasClass('radio-variable-item')) {
                                        $(this).find('input.wvs-radio-variable-item:radio').prop('disabled', true).prop('checked', false);
                                    }
                                }
                            });

                            // Items Updated
                            $(_this5).trigger('wvs-items-updated');
                        }, 1);
                    });
                });
            }
        }], [{
            key: '_jQueryInterface',
            value: function _jQueryInterface(config) {
                return this.each(function () {
                    new WooVariationSwatches(this, config);
                });
            }
        }]);

        return WooVariationSwatches;
    }();

    /**
     * ------------------------------------------------------------------------
     * jQuery
     * ------------------------------------------------------------------------
     */

    $.fn['WooVariationSwatches'] = WooVariationSwatches._jQueryInterface;
    $.fn['WooVariationSwatches'].Constructor = WooVariationSwatches;
    $.fn['WooVariationSwatches'].noConflict = function () {
        $.fn['WooVariationSwatches'] = $.fn['WooVariationSwatches'];
        return WooVariationSwatches._jQueryInterface;
    };

    return WooVariationSwatches;
}(jQuery);

/* harmony default export */ __webpack_exports__["default"] = (WooVariationSwatches);

/***/ }),

/***/ 9:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(10);


/***/ })

/******/ });