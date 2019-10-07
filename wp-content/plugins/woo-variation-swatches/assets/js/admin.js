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
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
__webpack_require__(3);
__webpack_require__(4);
__webpack_require__(5);
__webpack_require__(6);
__webpack_require__(7);
module.exports = __webpack_require__(8);


/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

jQuery(function ($) {
    Promise.resolve().then(function () {
        return __webpack_require__(2);
    }).then(function (_ref) {
        var PluginHelper = _ref.PluginHelper;


        PluginHelper.GWPAdmin();
        PluginHelper.SelectWoo();
        PluginHelper.ColorPicker();
        PluginHelper.FieldDependency();
        PluginHelper.ImageUploader();
        PluginHelper.AttributeDialog();

        $(document.body).on('woocommerce_added_attribute', function () {
            PluginHelper.SelectWoo();
            PluginHelper.ColorPicker();
            PluginHelper.ImageUploader();
            PluginHelper.AttributeDialog();
        });

        $(document.body).on('wvs_pro_product_swatches_variation_loaded', function () {
            PluginHelper.ColorPicker();
            PluginHelper.ImageUploader();
        });
    });
}); // end of jquery main wrapper

/***/ }),
/* 2 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PluginHelper", function() { return PluginHelper; });
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/*global WVSPluginObject, wp, woocommerce_admin_meta_boxes*/

var PluginHelper = function ($) {
    var PluginHelper = function () {
        function PluginHelper() {
            _classCallCheck(this, PluginHelper);
        }

        _createClass(PluginHelper, null, [{
            key: 'GWPAdmin',
            value: function GWPAdmin() {
                if ($().gwp_live_feed) {
                    $().gwp_live_feed();
                }
                if ($().gwp_deactivate_popup) {
                    $().gwp_deactivate_popup('woo-variation-swatches');
                }
            }
        }, {
            key: 'ImageUploader',
            value: function ImageUploader() {
                $(document).off('click', 'button.wvs_upload_image_button');
                $(document).on('click', 'button.wvs_upload_image_button', this.AddImage);
                $(document).on('click', 'button.wvs_remove_image_button', this.RemoveImage);
            }
        }, {
            key: 'AddImage',
            value: function AddImage(event) {
                var _this = this;

                event.preventDefault();
                event.stopPropagation();

                var file_frame = void 0;

                if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {

                    // If the media frame already exists, reopen it.
                    if (file_frame) {
                        file_frame.open();
                        return;
                    }

                    // Create the media frame.
                    file_frame = wp.media.frames.select_image = wp.media({
                        title: WVSPluginObject.media_title,
                        button: {
                            text: WVSPluginObject.button_title
                        },
                        multiple: false
                    });

                    // When an image is selected, run a callback.
                    file_frame.on('select', function () {
                        var attachment = file_frame.state().get('selection').first().toJSON();

                        if ($.trim(attachment.id) !== '') {

                            var url = typeof attachment.sizes.thumbnail === 'undefined' ? attachment.sizes.full.url : attachment.sizes.thumbnail.url;

                            $(_this).prev().val(attachment.id);
                            $(_this).closest('.meta-image-field-wrapper').find('img').attr('src', url);
                            $(_this).next().show();
                        }
                        //file_frame.close();
                    });

                    // When open select selected
                    file_frame.on('open', function () {

                        // Grab our attachment selection and construct a JSON representation of the model.
                        var selection = file_frame.state().get('selection');
                        var current = $(_this).prev().val();
                        var attachment = wp.media.attachment(current);
                        attachment.fetch();
                        selection.add(attachment ? [attachment] : []);
                    });

                    // Finally, open the modal.
                    file_frame.open();
                }
            }
        }, {
            key: 'RemoveImage',
            value: function RemoveImage(event) {

                event.preventDefault();
                event.stopPropagation();

                var placeholder = $(this).closest('.meta-image-field-wrapper').find('img').data('placeholder');
                $(this).closest('.meta-image-field-wrapper').find('img').attr('src', placeholder);
                $(this).prev().prev().val('');
                $(this).hide();
                return false;
            }
        }, {
            key: 'SelectWoo',
            value: function SelectWoo() {
                var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'select.wvs-selectwoo';

                if ($().selectWoo) {
                    $(selector).selectWoo({
                        allowClear: true
                    });
                }
            }
        }, {
            key: 'ColorPicker',
            value: function ColorPicker() {
                var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'input.wvs-color-picker';

                if ($().wpColorPicker) {
                    $(selector).wpColorPicker();
                }
            }
        }, {
            key: 'FieldDependency',
            value: function FieldDependency() {
                var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '[data-depends]';

                if ($().FormFieldDependency) {
                    $(selector).FormFieldDependency();
                }
            }
        }, {
            key: 'savingDialog',
            value: function savingDialog($wrapper, $dialog, taxonomy) {

                var data = {};
                var term = '';

                // @TODO: We should use form data, because we have to pick array based data also :)

                $dialog.find('input, select').each(function () {
                    var key = $(this).attr('name');
                    var value = $(this).val();
                    if (key) {
                        if (key === 'tag_name') {
                            term = value;
                        } else {
                            data[key] = value;
                        }
                        $(this).val('');
                    }
                });

                if (term) {
                    $('.product_attributes').block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });

                    var ajax_data = _extends({
                        action: 'woocommerce_add_new_attribute',
                        taxonomy: taxonomy,
                        term: term,
                        security: woocommerce_admin_meta_boxes.add_attribute_nonce
                    }, data);

                    $.post(woocommerce_admin_meta_boxes.ajax_url, ajax_data, function (response) {

                        if (response.error) {
                            // Error.
                            window.alert(response.error);
                        } else if (response.slug) {
                            // Success.
                            $wrapper.find('select.attribute_values').append('<option value="' + response.term_id + '" selected="selected">' + response.name + '</option>');
                            $wrapper.find('select.attribute_values').change();
                        }

                        $('.product_attributes').unblock();
                    });
                } else {
                    $('.product_attributes').unblock();
                }
            }
        }, {
            key: 'AttributeDialog',
            value: function AttributeDialog() {

                var self = this;
                $('.product_attributes').on('click', 'button.wvs_add_new_attribute', function (event) {

                    event.preventDefault();

                    var $wrapper = $(this).closest('.woocommerce_attribute');
                    var attribute = $wrapper.data('taxonomy');
                    var title = $(this).data('dialog_title');

                    $('.wvs-attribute-dialog-for-' + attribute).dialog({
                        title: '',
                        dialogClass: 'wp-dialog wvs-attribute-dialog',
                        classes: {
                            "ui-dialog": "wp-dialog wvs-attribute-dialog"
                        },
                        autoOpen: false,
                        draggable: true,
                        width: 'auto',
                        modal: true,
                        resizable: false,
                        closeOnEscape: true,
                        position: {
                            my: "center",
                            at: "center",
                            of: window
                        },
                        open: function open() {
                            // close dialog by clicking the overlay behind it
                            $('.ui-widget-overlay').bind('click', function () {
                                $('#attribute-dialog').dialog('close');
                            });
                        },
                        create: function create() {
                            // style fix for WordPress admin
                            // $('.ui-dialog-titlebar-close').addClass('ui-button');
                        }
                    }).dialog("option", "title", title).dialog("option", "buttons", [{
                        text: WVSPluginObject.dialog_save,
                        click: function click() {
                            self.savingDialog($wrapper, $(this), attribute);
                            $(this).dialog("close").dialog("destroy");
                        }
                    }, {
                        text: WVSPluginObject.dialog_cancel,
                        click: function click() {
                            $(this).dialog("close").dialog("destroy");
                        }
                    }]).dialog('open');
                });
            }
        }]);

        return PluginHelper;
    }();

    return PluginHelper;
}(jQuery);



/***/ }),
/* 3 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 4 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 5 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 6 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 7 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 8 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
/******/ ]);