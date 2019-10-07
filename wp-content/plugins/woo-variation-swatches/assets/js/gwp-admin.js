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
/******/ 	return __webpack_require__(__webpack_require__.s = 12);
/******/ })
/************************************************************************/
/******/ ({

/***/ 12:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(13);


/***/ }),

/***/ 13:
/***/ (function(module, exports, __webpack_require__) {

(function ($) {

    Promise.resolve().then(function () {
        return __webpack_require__(14);
    }).then(function (_ref) {
        var GWPAdminHelper = _ref.GWPAdminHelper;


        $.fn.gwp_live_feed = function () {
            GWPAdminHelper.LiveFeed();
        };

        $.fn.gwp_deactivate_popup = function ($slug) {
            GWPAdminHelper.DeactivatePopup($slug);
        };
    });
})(jQuery);

/***/ }),

/***/ 14:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "GWPAdminHelper", function() { return GWPAdminHelper; });
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/*global GWPAdmin*/

var GWPAdminHelper = function ($) {
    var GWPAdminHelper = function () {
        function GWPAdminHelper() {
            _classCallCheck(this, GWPAdminHelper);
        }

        _createClass(GWPAdminHelper, null, [{
            key: 'LiveFeed',
            value: function LiveFeed() {
                $('.gwp-live-feed-close').on('click', function (e) {
                    e.preventDefault();
                    var id = $(this).data('feed_id');
                    wp.ajax.send('gwp_live_feed_close', {
                        data: { id: id }
                    });

                    $(this).parent().fadeOut('fast', function () {
                        $(this).remove();
                    });
                });
            }
        }, {
            key: 'ResetPopupData',
            value: function ResetPopupData(pluginslug) {
                var id = '#gwp-plugin-deactivate-feedback-dialog-wrapper-' + pluginslug;
                var $button = $('.feedback-dialog-form-button-send', id);
                $button.prop('disabled', false).text($button.data('defaultvalue')).next().removeClass('visible');
            }
        }, {
            key: 'DeactivatePopup',
            value: function DeactivatePopup(pluginslug) {

                var id = '#gwp-plugin-deactivate-feedback-dialog-wrapper-' + pluginslug;

                $(id).dialog({
                    title: GWPAdmin.feedback_title,
                    dialogClass: 'wp-dialog gwp-deactivate-feedback-dialog',
                    autoOpen: false,
                    draggable: false,
                    width: 'auto',
                    modal: true,
                    resizable: false,
                    closeOnEscape: true,
                    position: {
                        my: "center",
                        at: "center",
                        of: window
                    },
                    create: function create() {
                        $('.ui-dialog-titlebar-close').addClass('ui-button');
                    },
                    open: function open() {
                        $('.ui-widget-overlay').bind('click', function () {
                            $(id).dialog('close');
                        });

                        var opener = $(this).data('gwp-deactivate-dialog-opener');

                        GWPAdminHelper.ResetPopupData(pluginslug);

                        var slug = $(opener).data('slug');
                        var plugin = $(opener).data('plugin');
                        var deactivate_link = $(opener).data('deactivate_link');

                        $('.feedback-dialog-form-button-skip', id).prop('href', deactivate_link);
                        $('.feedback-dialog-form-button-send', id).data('deactivate_link', deactivate_link);
                    }
                });

                $('.feedback-dialog-form-button-send', id).on('click', function (event) {
                    event.preventDefault();
                    var data = $('.feedback-dialog-form', id).serializeJSON();

                    var link = $(this).data('deactivate_link');

                    if (typeof data['reason_type'] === 'undefined') {
                        return;
                    }

                    $(this).prop('disabled', true).text($(this).data('deactivating')).next().addClass('visible');

                    wp.ajax.send(data.action, {
                        data: data,
                        success: function success(response) {
                            window.location.replace(link);
                        },
                        error: function error() {
                            window.location.replace(link);
                        }
                    });

                    //console.log(data)
                });

                $(':radio', id).on('change', function () {

                    $(this).closest('.feedback-dialog-form-body').find('.feedback-text').prop('disabled', true).hide();

                    $(this).nextAll('.feedback-text').prop('disabled', false).show().focus();
                    // console.log($(this).val())
                });

                $('.wp-list-table.plugins').find('[data-slug="' + pluginslug + '"].active').each(function () {
                    var _this = this;

                    var deactivate_link = $(this).find('.deactivate a').prop('href');

                    $(this).data('deactivate_link', deactivate_link);

                    $(this).find('.deactivate a').on('click', function (event) {
                        event.preventDefault();

                        $(id).data('gwp-deactivate-dialog-opener', _this).dialog('open');
                    });
                });
            }
        }]);

        return GWPAdminHelper;
    }();

    return GWPAdminHelper;
}(jQuery);



/***/ })

/******/ });