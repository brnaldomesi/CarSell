/*!
 * WooCommerce Variation Swatches v1.0.57 
 * 
 * Author: Emran Ahmed ( emran.bd.08@gmail.com ) 
 * Date: 5/16/2019, 1:24:01 AM
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXNzZXRzL2pzL2FkbWluLmpzIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vL3dlYnBhY2svYm9vdHN0cmFwIDU5M2EzZDcxZTlkNWUxNjk0MjFiIiwid2VicGFjazovLy9zcmMvanMvYmFja2VuZC5qcyIsIndlYnBhY2s6Ly8vc3JjL2pzL1BsdWdpbkhlbHBlci5qcyIsIndlYnBhY2s6Ly8vLi9zcmMvc2Nzcy9iYWNrZW5kLnNjc3M/YmU2MSIsIndlYnBhY2s6Ly8vLi9zcmMvc2Nzcy9nd3AtYWRtaW4uc2Nzcz80NzY0Iiwid2VicGFjazovLy8uL3NyYy9zY3NzL2d3cC1hZG1pbi1ub3RpY2Uuc2Nzcz9mMWZiIiwid2VicGFjazovLy8uL3NyYy9zY3NzL2Zyb250ZW5kLnNjc3M/NjI2YyIsIndlYnBhY2s6Ly8vLi9zcmMvc2Nzcy90b29sdGlwLnNjc3M/OWIwMyIsIndlYnBhY2s6Ly8vLi9zcmMvc2Nzcy90aGVtZS1vdmVycmlkZS5zY3NzPzVlMjIiXSwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gd2VicGFjay9ib290c3RyYXAgNTkzYTNkNzFlOWQ1ZTE2OTQyMWIiLCJqUXVlcnkoJCA9PiB7XG4gICAgaW1wb3J0KCcuL1BsdWdpbkhlbHBlcicpLnRoZW4oKHtQbHVnaW5IZWxwZXJ9KSA9PiB7XG5cbiAgICAgICAgUGx1Z2luSGVscGVyLkdXUEFkbWluKCk7XG4gICAgICAgIFBsdWdpbkhlbHBlci5TZWxlY3RXb28oKTtcbiAgICAgICAgUGx1Z2luSGVscGVyLkNvbG9yUGlja2VyKCk7XG4gICAgICAgIFBsdWdpbkhlbHBlci5GaWVsZERlcGVuZGVuY3koKTtcbiAgICAgICAgUGx1Z2luSGVscGVyLkltYWdlVXBsb2FkZXIoKTtcbiAgICAgICAgUGx1Z2luSGVscGVyLkF0dHJpYnV0ZURpYWxvZygpO1xuXG4gICAgICAgICQoZG9jdW1lbnQuYm9keSkub24oJ3dvb2NvbW1lcmNlX2FkZGVkX2F0dHJpYnV0ZScsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIFBsdWdpbkhlbHBlci5TZWxlY3RXb28oKTtcbiAgICAgICAgICAgIFBsdWdpbkhlbHBlci5Db2xvclBpY2tlcigpO1xuICAgICAgICAgICAgUGx1Z2luSGVscGVyLkltYWdlVXBsb2FkZXIoKTtcbiAgICAgICAgICAgIFBsdWdpbkhlbHBlci5BdHRyaWJ1dGVEaWFsb2coKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgJChkb2N1bWVudC5ib2R5KS5vbignd3ZzX3Byb19wcm9kdWN0X3N3YXRjaGVzX3ZhcmlhdGlvbl9sb2FkZWQnLCAoKSA9PiB7XG4gICAgICAgICAgICBQbHVnaW5IZWxwZXIuQ29sb3JQaWNrZXIoKTtcbiAgICAgICAgICAgIFBsdWdpbkhlbHBlci5JbWFnZVVwbG9hZGVyKCk7XG4gICAgICAgIH0pO1xuICAgIH0pO1xufSk7ICAvLyBlbmQgb2YganF1ZXJ5IG1haW4gd3JhcHBlclxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyBzcmMvanMvYmFja2VuZC5qcyIsIi8qZ2xvYmFsIFdWU1BsdWdpbk9iamVjdCwgd3AsIHdvb2NvbW1lcmNlX2FkbWluX21ldGFfYm94ZXMqL1xuXG5jb25zdCBQbHVnaW5IZWxwZXIgPSAoKCQpID0+IHtcbiAgICBjbGFzcyBQbHVnaW5IZWxwZXIge1xuXG4gICAgICAgIHN0YXRpYyBHV1BBZG1pbigpIHtcbiAgICAgICAgICAgIGlmICgkKCkuZ3dwX2xpdmVfZmVlZCkge1xuICAgICAgICAgICAgICAgICQoKS5nd3BfbGl2ZV9mZWVkKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBpZiAoJCgpLmd3cF9kZWFjdGl2YXRlX3BvcHVwKSB7XG4gICAgICAgICAgICAgICAgJCgpLmd3cF9kZWFjdGl2YXRlX3BvcHVwKCd3b28tdmFyaWF0aW9uLXN3YXRjaGVzJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICBzdGF0aWMgSW1hZ2VVcGxvYWRlcigpIHtcbiAgICAgICAgICAgICQoZG9jdW1lbnQpLm9mZignY2xpY2snLCAnYnV0dG9uLnd2c191cGxvYWRfaW1hZ2VfYnV0dG9uJyk7XG4gICAgICAgICAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnYnV0dG9uLnd2c191cGxvYWRfaW1hZ2VfYnV0dG9uJywgdGhpcy5BZGRJbWFnZSk7XG4gICAgICAgICAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnYnV0dG9uLnd2c19yZW1vdmVfaW1hZ2VfYnV0dG9uJywgdGhpcy5SZW1vdmVJbWFnZSk7XG4gICAgICAgIH1cblxuICAgICAgICBzdGF0aWMgQWRkSW1hZ2UoZXZlbnQpIHtcblxuICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXG4gICAgICAgICAgICBsZXQgZmlsZV9mcmFtZTtcblxuICAgICAgICAgICAgaWYgKHR5cGVvZiB3cCAhPT0gJ3VuZGVmaW5lZCcgJiYgd3AubWVkaWEgJiYgd3AubWVkaWEuZWRpdG9yKSB7XG5cbiAgICAgICAgICAgICAgICAvLyBJZiB0aGUgbWVkaWEgZnJhbWUgYWxyZWFkeSBleGlzdHMsIHJlb3BlbiBpdC5cbiAgICAgICAgICAgICAgICBpZiAoZmlsZV9mcmFtZSkge1xuICAgICAgICAgICAgICAgICAgICBmaWxlX2ZyYW1lLm9wZW4oKTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vIENyZWF0ZSB0aGUgbWVkaWEgZnJhbWUuXG4gICAgICAgICAgICAgICAgZmlsZV9mcmFtZSA9IHdwLm1lZGlhLmZyYW1lcy5zZWxlY3RfaW1hZ2UgPSB3cC5tZWRpYSh7XG4gICAgICAgICAgICAgICAgICAgIHRpdGxlICAgIDogV1ZTUGx1Z2luT2JqZWN0Lm1lZGlhX3RpdGxlLFxuICAgICAgICAgICAgICAgICAgICBidXR0b24gICA6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRleHQgOiBXVlNQbHVnaW5PYmplY3QuYnV0dG9uX3RpdGxlXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIG11bHRpcGxlIDogZmFsc2UsXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvLyBXaGVuIGFuIGltYWdlIGlzIHNlbGVjdGVkLCBydW4gYSBjYWxsYmFjay5cbiAgICAgICAgICAgICAgICBmaWxlX2ZyYW1lLm9uKCdzZWxlY3QnLCAoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGxldCBhdHRhY2htZW50ID0gZmlsZV9mcmFtZS5zdGF0ZSgpLmdldCgnc2VsZWN0aW9uJykuZmlyc3QoKS50b0pTT04oKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoJC50cmltKGF0dGFjaG1lbnQuaWQpICE9PSAnJykge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBsZXQgdXJsID0gKHR5cGVvZihhdHRhY2htZW50LnNpemVzLnRodW1ibmFpbCkgPT09ICd1bmRlZmluZWQnKSA/IGF0dGFjaG1lbnQuc2l6ZXMuZnVsbC51cmwgOiBhdHRhY2htZW50LnNpemVzLnRodW1ibmFpbC51cmw7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucHJldigpLnZhbChhdHRhY2htZW50LmlkKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuY2xvc2VzdCgnLm1ldGEtaW1hZ2UtZmllbGQtd3JhcHBlcicpLmZpbmQoJ2ltZycpLmF0dHIoJ3NyYycsIHVybCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLm5leHQoKS5zaG93KCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgLy9maWxlX2ZyYW1lLmNsb3NlKCk7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvLyBXaGVuIG9wZW4gc2VsZWN0IHNlbGVjdGVkXG4gICAgICAgICAgICAgICAgZmlsZV9mcmFtZS5vbignb3BlbicsICgpID0+IHtcblxuICAgICAgICAgICAgICAgICAgICAvLyBHcmFiIG91ciBhdHRhY2htZW50IHNlbGVjdGlvbiBhbmQgY29uc3RydWN0IGEgSlNPTiByZXByZXNlbnRhdGlvbiBvZiB0aGUgbW9kZWwuXG4gICAgICAgICAgICAgICAgICAgIGxldCBzZWxlY3Rpb24gID0gZmlsZV9mcmFtZS5zdGF0ZSgpLmdldCgnc2VsZWN0aW9uJyk7XG4gICAgICAgICAgICAgICAgICAgIGxldCBjdXJyZW50ICAgID0gJCh0aGlzKS5wcmV2KCkudmFsKCk7XG4gICAgICAgICAgICAgICAgICAgIGxldCBhdHRhY2htZW50ID0gd3AubWVkaWEuYXR0YWNobWVudChjdXJyZW50KTtcbiAgICAgICAgICAgICAgICAgICAgYXR0YWNobWVudC5mZXRjaCgpO1xuICAgICAgICAgICAgICAgICAgICBzZWxlY3Rpb24uYWRkKGF0dGFjaG1lbnQgPyBbYXR0YWNobWVudF0gOiBbXSk7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvLyBGaW5hbGx5LCBvcGVuIHRoZSBtb2RhbC5cbiAgICAgICAgICAgICAgICBmaWxlX2ZyYW1lLm9wZW4oKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHN0YXRpYyBSZW1vdmVJbWFnZShldmVudCkge1xuXG4gICAgICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgIGxldCBwbGFjZWhvbGRlciA9ICQodGhpcykuY2xvc2VzdCgnLm1ldGEtaW1hZ2UtZmllbGQtd3JhcHBlcicpLmZpbmQoJ2ltZycpLmRhdGEoJ3BsYWNlaG9sZGVyJyk7XG4gICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJy5tZXRhLWltYWdlLWZpZWxkLXdyYXBwZXInKS5maW5kKCdpbWcnKS5hdHRyKCdzcmMnLCBwbGFjZWhvbGRlcik7XG4gICAgICAgICAgICAkKHRoaXMpLnByZXYoKS5wcmV2KCkudmFsKCcnKTtcbiAgICAgICAgICAgICQodGhpcykuaGlkZSgpO1xuICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICB9XG5cbiAgICAgICAgc3RhdGljIFNlbGVjdFdvbyhzZWxlY3RvciA9ICdzZWxlY3Qud3ZzLXNlbGVjdHdvbycpIHtcbiAgICAgICAgICAgIGlmICgkKCkuc2VsZWN0V29vKSB7XG4gICAgICAgICAgICAgICAgJChzZWxlY3Rvcikuc2VsZWN0V29vKHtcbiAgICAgICAgICAgICAgICAgICAgYWxsb3dDbGVhciA6IHRydWVcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHN0YXRpYyBDb2xvclBpY2tlcihzZWxlY3RvciA9ICdpbnB1dC53dnMtY29sb3ItcGlja2VyJykge1xuICAgICAgICAgICAgaWYgKCQoKS53cENvbG9yUGlja2VyKSB7XG4gICAgICAgICAgICAgICAgJChzZWxlY3Rvcikud3BDb2xvclBpY2tlcigpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgc3RhdGljIEZpZWxkRGVwZW5kZW5jeShzZWxlY3RvciA9ICdbZGF0YS1kZXBlbmRzXScpIHtcbiAgICAgICAgICAgIGlmICgkKCkuRm9ybUZpZWxkRGVwZW5kZW5jeSkge1xuICAgICAgICAgICAgICAgICQoc2VsZWN0b3IpLkZvcm1GaWVsZERlcGVuZGVuY3koKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHN0YXRpYyBzYXZpbmdEaWFsb2coJHdyYXBwZXIsICRkaWFsb2csIHRheG9ub215KSB7XG5cbiAgICAgICAgICAgIGxldCBkYXRhID0ge307XG4gICAgICAgICAgICBsZXQgdGVybSA9ICcnO1xuXG4gICAgICAgICAgICAvLyBAVE9ETzogV2Ugc2hvdWxkIHVzZSBmb3JtIGRhdGEsIGJlY2F1c2Ugd2UgaGF2ZSB0byBwaWNrIGFycmF5IGJhc2VkIGRhdGEgYWxzbyA6KVxuXG4gICAgICAgICAgICAkZGlhbG9nLmZpbmQoYGlucHV0LCBzZWxlY3RgKS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBsZXQga2V5ICAgPSAkKHRoaXMpLmF0dHIoJ25hbWUnKTtcbiAgICAgICAgICAgICAgICBsZXQgdmFsdWUgPSAkKHRoaXMpLnZhbCgpO1xuICAgICAgICAgICAgICAgIGlmIChrZXkpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKGtleSA9PT0gJ3RhZ19uYW1lJykge1xuICAgICAgICAgICAgICAgICAgICAgICAgdGVybSA9IHZhbHVlXG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBkYXRhW2tleV0gPSB2YWx1ZVxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykudmFsKCcnKVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBpZiAodGVybSkge1xuICAgICAgICAgICAgICAgICQoJy5wcm9kdWN0X2F0dHJpYnV0ZXMnKS5ibG9jayh7XG4gICAgICAgICAgICAgICAgICAgIG1lc3NhZ2UgICAgOiBudWxsLFxuICAgICAgICAgICAgICAgICAgICBvdmVybGF5Q1NTIDoge1xuICAgICAgICAgICAgICAgICAgICAgICAgYmFja2dyb3VuZCA6ICcjZmZmJyxcbiAgICAgICAgICAgICAgICAgICAgICAgIG9wYWNpdHkgICAgOiAwLjZcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgbGV0IGFqYXhfZGF0YSA9IHtcbiAgICAgICAgICAgICAgICAgICAgYWN0aW9uICAgOiAnd29vY29tbWVyY2VfYWRkX25ld19hdHRyaWJ1dGUnLFxuICAgICAgICAgICAgICAgICAgICB0YXhvbm9teSA6IHRheG9ub215LFxuICAgICAgICAgICAgICAgICAgICB0ZXJtICAgICA6IHRlcm0sXG4gICAgICAgICAgICAgICAgICAgIHNlY3VyaXR5IDogd29vY29tbWVyY2VfYWRtaW5fbWV0YV9ib3hlcy5hZGRfYXR0cmlidXRlX25vbmNlLFxuICAgICAgICAgICAgICAgICAgICAuLi5kYXRhXG4gICAgICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgICAgICQucG9zdCh3b29jb21tZXJjZV9hZG1pbl9tZXRhX2JveGVzLmFqYXhfdXJsLCBhamF4X2RhdGEsIGZ1bmN0aW9uIChyZXNwb25zZSkge1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChyZXNwb25zZS5lcnJvcikge1xuICAgICAgICAgICAgICAgICAgICAgICAgLy8gRXJyb3IuXG4gICAgICAgICAgICAgICAgICAgICAgICB3aW5kb3cuYWxlcnQocmVzcG9uc2UuZXJyb3IpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIGVsc2UgaWYgKHJlc3BvbnNlLnNsdWcpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIFN1Y2Nlc3MuXG4gICAgICAgICAgICAgICAgICAgICAgICAkd3JhcHBlci5maW5kKCdzZWxlY3QuYXR0cmlidXRlX3ZhbHVlcycpLmFwcGVuZCgnPG9wdGlvbiB2YWx1ZT1cIicgKyByZXNwb25zZS50ZXJtX2lkICsgJ1wiIHNlbGVjdGVkPVwic2VsZWN0ZWRcIj4nICsgcmVzcG9uc2UubmFtZSArICc8L29wdGlvbj4nKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICR3cmFwcGVyLmZpbmQoJ3NlbGVjdC5hdHRyaWJ1dGVfdmFsdWVzJykuY2hhbmdlKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAkKCcucHJvZHVjdF9hdHRyaWJ1dGVzJykudW5ibG9jaygpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgZWxzZSB7XG4gICAgICAgICAgICAgICAgJCgnLnByb2R1Y3RfYXR0cmlidXRlcycpLnVuYmxvY2soKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHN0YXRpYyBBdHRyaWJ1dGVEaWFsb2coKSB7XG5cbiAgICAgICAgICAgIGxldCBzZWxmID0gdGhpcztcbiAgICAgICAgICAgICQoJy5wcm9kdWN0X2F0dHJpYnV0ZXMnKS5vbignY2xpY2snLCAnYnV0dG9uLnd2c19hZGRfbmV3X2F0dHJpYnV0ZScsIGZ1bmN0aW9uIChldmVudCkge1xuXG4gICAgICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICAgICAgICAgIGxldCAkd3JhcHBlciAgPSAkKHRoaXMpLmNsb3Nlc3QoJy53b29jb21tZXJjZV9hdHRyaWJ1dGUnKTtcbiAgICAgICAgICAgICAgICBsZXQgYXR0cmlidXRlID0gJHdyYXBwZXIuZGF0YSgndGF4b25vbXknKTtcbiAgICAgICAgICAgICAgICBsZXQgdGl0bGUgICAgID0gJCh0aGlzKS5kYXRhKCdkaWFsb2dfdGl0bGUnKTtcblxuICAgICAgICAgICAgICAgICQoJy53dnMtYXR0cmlidXRlLWRpYWxvZy1mb3ItJyArIGF0dHJpYnV0ZSkuZGlhbG9nKHtcbiAgICAgICAgICAgICAgICAgICAgdGl0bGUgICAgICAgICA6ICcnLFxuICAgICAgICAgICAgICAgICAgICBkaWFsb2dDbGFzcyAgIDogJ3dwLWRpYWxvZyB3dnMtYXR0cmlidXRlLWRpYWxvZycsXG4gICAgICAgICAgICAgICAgICAgIGNsYXNzZXMgICAgICAgOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInVpLWRpYWxvZ1wiIDogXCJ3cC1kaWFsb2cgd3ZzLWF0dHJpYnV0ZS1kaWFsb2dcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBhdXRvT3BlbiAgICAgIDogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgIGRyYWdnYWJsZSAgICAgOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICB3aWR0aCAgICAgICAgIDogJ2F1dG8nLFxuICAgICAgICAgICAgICAgICAgICBtb2RhbCAgICAgICAgIDogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgcmVzaXphYmxlICAgICA6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICBjbG9zZU9uRXNjYXBlIDogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgcG9zaXRpb24gICAgICA6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIG15IDogXCJjZW50ZXJcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIGF0IDogXCJjZW50ZXJcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIG9mIDogd2luZG93XG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIG9wZW4gICAgICAgICAgOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBjbG9zZSBkaWFsb2cgYnkgY2xpY2tpbmcgdGhlIG92ZXJsYXkgYmVoaW5kIGl0XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcudWktd2lkZ2V0LW92ZXJsYXknKS5iaW5kKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKCcjYXR0cmlidXRlLWRpYWxvZycpLmRpYWxvZygnY2xvc2UnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIGNyZWF0ZSAgICAgICAgOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBzdHlsZSBmaXggZm9yIFdvcmRQcmVzcyBhZG1pblxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gJCgnLnVpLWRpYWxvZy10aXRsZWJhci1jbG9zZScpLmFkZENsYXNzKCd1aS1idXR0b24nKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgICAgIC5kaWFsb2coXCJvcHRpb25cIiwgXCJ0aXRsZVwiLCB0aXRsZSlcbiAgICAgICAgICAgICAgICAgICAgLmRpYWxvZyhcIm9wdGlvblwiLCBcImJ1dHRvbnNcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRleHQgIDogV1ZTUGx1Z2luT2JqZWN0LmRpYWxvZ19zYXZlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjbGljayA6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNlbGYuc2F2aW5nRGlhbG9nKCR3cmFwcGVyLCAkKHRoaXMpLCBhdHRyaWJ1dGUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5kaWFsb2coXCJjbG9zZVwiKS5kaWFsb2coXCJkZXN0cm95XCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRleHQgIDogV1ZTUGx1Z2luT2JqZWN0LmRpYWxvZ19jYW5jZWwsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNsaWNrIDogZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5kaWFsb2coXCJjbG9zZVwiKS5kaWFsb2coXCJkZXN0cm95XCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgXVxuICAgICAgICAgICAgICAgICAgICApXG4gICAgICAgICAgICAgICAgICAgIC5kaWFsb2coJ29wZW4nKVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICByZXR1cm4gUGx1Z2luSGVscGVyO1xufSkoalF1ZXJ5KTtcblxuZXhwb3J0IHsgUGx1Z2luSGVscGVyIH07XG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHNyYy9qcy9QbHVnaW5IZWxwZXIuanMiLCIvLyByZW1vdmVkIGJ5IGV4dHJhY3QtdGV4dC13ZWJwYWNrLXBsdWdpblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vc3JjL3Njc3MvYmFja2VuZC5zY3NzXG4vLyBtb2R1bGUgaWQgPSAzXG4vLyBtb2R1bGUgY2h1bmtzID0gMCIsIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9zcmMvc2Nzcy9nd3AtYWRtaW4uc2Nzc1xuLy8gbW9kdWxlIGlkID0gNFxuLy8gbW9kdWxlIGNodW5rcyA9IDAiLCIvLyByZW1vdmVkIGJ5IGV4dHJhY3QtdGV4dC13ZWJwYWNrLXBsdWdpblxuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vc3JjL3Njc3MvZ3dwLWFkbWluLW5vdGljZS5zY3NzXG4vLyBtb2R1bGUgaWQgPSA1XG4vLyBtb2R1bGUgY2h1bmtzID0gMCIsIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9zcmMvc2Nzcy9mcm9udGVuZC5zY3NzXG4vLyBtb2R1bGUgaWQgPSA2XG4vLyBtb2R1bGUgY2h1bmtzID0gMCIsIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luXG5cblxuLy8vLy8vLy8vLy8vLy8vLy8vXG4vLyBXRUJQQUNLIEZPT1RFUlxuLy8gLi9zcmMvc2Nzcy90b29sdGlwLnNjc3Ncbi8vIG1vZHVsZSBpZCA9IDdcbi8vIG1vZHVsZSBjaHVua3MgPSAwIiwiLy8gcmVtb3ZlZCBieSBleHRyYWN0LXRleHQtd2VicGFjay1wbHVnaW5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL3NyYy9zY3NzL3RoZW1lLW92ZXJyaWRlLnNjc3Ncbi8vIG1vZHVsZSBpZCA9IDhcbi8vIG1vZHVsZSBjaHVua3MgPSAwIl0sIm1hcHBpbmdzIjoiOzs7Ozs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDN0RBO0FBQ0E7QUFBQTtBQUFBO0FBQUE7QUFDQTtBQUNBO0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7Ozs7Ozs7Ozs7O0FDdEJBO0FBQ0E7QUFDQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQ0E7QUFEQTtBQUFBO0FBQUE7QUFJQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQVZBO0FBQUE7QUFBQTtBQWFBO0FBQ0E7QUFDQTtBQUNBO0FBaEJBO0FBQUE7QUFBQTtBQWtCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBREE7QUFHQTtBQUxBO0FBQ0E7QUFPQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUF2RUE7QUFBQTtBQUFBO0FBQ0E7QUEwRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBbkZBO0FBQUE7QUFBQTtBQXFGQTtBQUNBO0FBQUE7QUFDQTtBQUNBO0FBREE7QUFHQTtBQUNBO0FBM0ZBO0FBQUE7QUFBQTtBQTZGQTtBQUNBO0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFqR0E7QUFBQTtBQUFBO0FBbUdBO0FBQ0E7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQXZHQTtBQUFBO0FBQUE7QUFDQTtBQTBHQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFGQTtBQUZBO0FBQ0E7QUFPQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBSkE7QUFDQTtBQU9BO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFqS0E7QUFBQTtBQUFBO0FBQ0E7QUFvS0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQURBO0FBR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFIQTtBQUtBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBMUJBO0FBZ0NBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFMQTtBQVFBO0FBQ0E7QUFDQTtBQUNBO0FBSkE7QUFTQTtBQUNBO0FBOU5BO0FBQ0E7QUFEQTtBQUFBO0FBQ0E7QUFnT0E7QUFDQTtBQUNBOzs7Ozs7O0FDck9BOzs7Ozs7QUNBQTs7Ozs7O0FDQUE7Ozs7OztBQ0FBOzs7Ozs7QUNBQTs7Ozs7O0FDQUE7OztBIiwic291cmNlUm9vdCI6IiJ9