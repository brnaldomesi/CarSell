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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXNzZXRzL2pzL2d3cC1hZG1pbi5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy93ZWJwYWNrL2Jvb3RzdHJhcCA1OTNhM2Q3MWU5ZDVlMTY5NDIxYiIsIndlYnBhY2s6Ly8vc3JjL2pzL2d3cC1hZG1pbi5qcyIsIndlYnBhY2s6Ly8vc3JjL2pzL0dXUEFkbWluSGVscGVyLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIiBcdC8vIFRoZSBtb2R1bGUgY2FjaGVcbiBcdHZhciBpbnN0YWxsZWRNb2R1bGVzID0ge307XG5cbiBcdC8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG4gXHRmdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cbiBcdFx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG4gXHRcdGlmKGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdKSB7XG4gXHRcdFx0cmV0dXJuIGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdLmV4cG9ydHM7XG4gXHRcdH1cbiBcdFx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcbiBcdFx0dmFyIG1vZHVsZSA9IGluc3RhbGxlZE1vZHVsZXNbbW9kdWxlSWRdID0ge1xuIFx0XHRcdGk6IG1vZHVsZUlkLFxuIFx0XHRcdGw6IGZhbHNlLFxuIFx0XHRcdGV4cG9ydHM6IHt9XG4gXHRcdH07XG5cbiBcdFx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG4gXHRcdG1vZHVsZXNbbW9kdWxlSWRdLmNhbGwobW9kdWxlLmV4cG9ydHMsIG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG4gXHRcdC8vIEZsYWcgdGhlIG1vZHVsZSBhcyBsb2FkZWRcbiBcdFx0bW9kdWxlLmwgPSB0cnVlO1xuXG4gXHRcdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG4gXHRcdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbiBcdH1cblxuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZXMgb2JqZWN0IChfX3dlYnBhY2tfbW9kdWxlc19fKVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5tID0gbW9kdWxlcztcblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGUgY2FjaGVcbiBcdF9fd2VicGFja19yZXF1aXJlX18uYyA9IGluc3RhbGxlZE1vZHVsZXM7XG5cbiBcdC8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb24gZm9yIGhhcm1vbnkgZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kID0gZnVuY3Rpb24oZXhwb3J0cywgbmFtZSwgZ2V0dGVyKSB7XG4gXHRcdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywgbmFtZSkpIHtcbiBcdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgbmFtZSwge1xuIFx0XHRcdFx0Y29uZmlndXJhYmxlOiBmYWxzZSxcbiBcdFx0XHRcdGVudW1lcmFibGU6IHRydWUsXG4gXHRcdFx0XHRnZXQ6IGdldHRlclxuIFx0XHRcdH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIlwiO1xuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDEyKTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyB3ZWJwYWNrL2Jvb3RzdHJhcCA1OTNhM2Q3MWU5ZDVlMTY5NDIxYiIsIihmdW5jdGlvbiAoJCkge1xuXG4gICAgaW1wb3J0KCcuL0dXUEFkbWluSGVscGVyJykudGhlbigoe0dXUEFkbWluSGVscGVyfSkgPT4ge1xuXG4gICAgICAgICQuZm4uZ3dwX2xpdmVfZmVlZCA9IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIEdXUEFkbWluSGVscGVyLkxpdmVGZWVkKCk7XG4gICAgICAgIH1cblxuICAgICAgICAkLmZuLmd3cF9kZWFjdGl2YXRlX3BvcHVwID0gZnVuY3Rpb24gKCRzbHVnKSB7XG4gICAgICAgICAgICBHV1BBZG1pbkhlbHBlci5EZWFjdGl2YXRlUG9wdXAoJHNsdWcpO1xuICAgICAgICB9XG4gICAgfSk7XG5cbn0oalF1ZXJ5KSk7XG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHNyYy9qcy9nd3AtYWRtaW4uanMiLCIvKmdsb2JhbCBHV1BBZG1pbiovXG5cbmNvbnN0IEdXUEFkbWluSGVscGVyID0gKCgkKSA9PiB7XG4gICAgY2xhc3MgR1dQQWRtaW5IZWxwZXIge1xuXG4gICAgICAgIHN0YXRpYyBMaXZlRmVlZCgpIHtcbiAgICAgICAgICAgICQoJy5nd3AtbGl2ZS1mZWVkLWNsb3NlJykub24oJ2NsaWNrJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgbGV0IGlkID0gJCh0aGlzKS5kYXRhKCdmZWVkX2lkJyk7XG4gICAgICAgICAgICAgICAgd3AuYWpheC5zZW5kKCdnd3BfbGl2ZV9mZWVkX2Nsb3NlJywge1xuICAgICAgICAgICAgICAgICAgICBkYXRhIDoge2lkfVxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5wYXJlbnQoKS5mYWRlT3V0KCdmYXN0JywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpXG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgc3RhdGljIFJlc2V0UG9wdXBEYXRhKHBsdWdpbnNsdWcpIHtcbiAgICAgICAgICAgIGxldCBpZCAgICAgID0gYCNnd3AtcGx1Z2luLWRlYWN0aXZhdGUtZmVlZGJhY2stZGlhbG9nLXdyYXBwZXItJHtwbHVnaW5zbHVnfWA7XG4gICAgICAgICAgICBsZXQgJGJ1dHRvbiA9ICQoJy5mZWVkYmFjay1kaWFsb2ctZm9ybS1idXR0b24tc2VuZCcsIGlkKTtcbiAgICAgICAgICAgICRidXR0b24ucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSkudGV4dCgkYnV0dG9uLmRhdGEoJ2RlZmF1bHR2YWx1ZScpKS5uZXh0KCkucmVtb3ZlQ2xhc3MoJ3Zpc2libGUnKTtcbiAgICAgICAgfVxuXG4gICAgICAgIHN0YXRpYyBEZWFjdGl2YXRlUG9wdXAocGx1Z2luc2x1Zykge1xuXG4gICAgICAgICAgICBsZXQgaWQgPSBgI2d3cC1wbHVnaW4tZGVhY3RpdmF0ZS1mZWVkYmFjay1kaWFsb2ctd3JhcHBlci0ke3BsdWdpbnNsdWd9YDtcblxuICAgICAgICAgICAgJChpZCkuZGlhbG9nKHtcbiAgICAgICAgICAgICAgICB0aXRsZSAgICAgICAgIDogR1dQQWRtaW4uZmVlZGJhY2tfdGl0bGUsXG4gICAgICAgICAgICAgICAgZGlhbG9nQ2xhc3MgICA6ICd3cC1kaWFsb2cgZ3dwLWRlYWN0aXZhdGUtZmVlZGJhY2stZGlhbG9nJyxcbiAgICAgICAgICAgICAgICBhdXRvT3BlbiAgICAgIDogZmFsc2UsXG4gICAgICAgICAgICAgICAgZHJhZ2dhYmxlICAgICA6IGZhbHNlLFxuICAgICAgICAgICAgICAgIHdpZHRoICAgICAgICAgOiAnYXV0bycsXG4gICAgICAgICAgICAgICAgbW9kYWwgICAgICAgICA6IHRydWUsXG4gICAgICAgICAgICAgICAgcmVzaXphYmxlICAgICA6IGZhbHNlLFxuICAgICAgICAgICAgICAgIGNsb3NlT25Fc2NhcGUgOiB0cnVlLFxuICAgICAgICAgICAgICAgIHBvc2l0aW9uICAgICAgOiB7XG4gICAgICAgICAgICAgICAgICAgIG15IDogXCJjZW50ZXJcIixcbiAgICAgICAgICAgICAgICAgICAgYXQgOiBcImNlbnRlclwiLFxuICAgICAgICAgICAgICAgICAgICBvZiA6IHdpbmRvd1xuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgY3JlYXRlICAgICAgICA6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgJCgnLnVpLWRpYWxvZy10aXRsZWJhci1jbG9zZScpLmFkZENsYXNzKCd1aS1idXR0b24nKTtcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIG9wZW4gICAgICAgICAgOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICQoJy51aS13aWRnZXQtb3ZlcmxheScpLmJpbmQoJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJChpZCkuZGlhbG9nKCdjbG9zZScpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICBsZXQgb3BlbmVyID0gJCh0aGlzKS5kYXRhKCdnd3AtZGVhY3RpdmF0ZS1kaWFsb2ctb3BlbmVyJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgR1dQQWRtaW5IZWxwZXIuUmVzZXRQb3B1cERhdGEocGx1Z2luc2x1Zyk7XG5cbiAgICAgICAgICAgICAgICAgICAgbGV0IHNsdWcgICAgICAgICAgICA9ICQob3BlbmVyKS5kYXRhKCdzbHVnJyk7XG4gICAgICAgICAgICAgICAgICAgIGxldCBwbHVnaW4gICAgICAgICAgPSAkKG9wZW5lcikuZGF0YSgncGx1Z2luJyk7XG4gICAgICAgICAgICAgICAgICAgIGxldCBkZWFjdGl2YXRlX2xpbmsgPSAkKG9wZW5lcikuZGF0YSgnZGVhY3RpdmF0ZV9saW5rJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgJCgnLmZlZWRiYWNrLWRpYWxvZy1mb3JtLWJ1dHRvbi1za2lwJywgaWQpLnByb3AoJ2hyZWYnLCBkZWFjdGl2YXRlX2xpbmspXG4gICAgICAgICAgICAgICAgICAgICQoJy5mZWVkYmFjay1kaWFsb2ctZm9ybS1idXR0b24tc2VuZCcsIGlkKS5kYXRhKCdkZWFjdGl2YXRlX2xpbmsnLCBkZWFjdGl2YXRlX2xpbmspXG5cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgJCgnLmZlZWRiYWNrLWRpYWxvZy1mb3JtLWJ1dHRvbi1zZW5kJywgaWQpLm9uKCdjbGljaycsIGZ1bmN0aW9uIChldmVudCkge1xuICAgICAgICAgICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgbGV0IGRhdGEgPSAkKCcuZmVlZGJhY2stZGlhbG9nLWZvcm0nLCBpZCkuc2VyaWFsaXplSlNPTigpO1xuXG4gICAgICAgICAgICAgICAgbGV0IGxpbmsgPSAkKHRoaXMpLmRhdGEoJ2RlYWN0aXZhdGVfbGluaycpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHR5cGVvZiBkYXRhWydyZWFzb25fdHlwZSddID09PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5wcm9wKCdkaXNhYmxlZCcsIHRydWUpLnRleHQoJCh0aGlzKS5kYXRhKCdkZWFjdGl2YXRpbmcnKSkubmV4dCgpLmFkZENsYXNzKCd2aXNpYmxlJyk7XG5cbiAgICAgICAgICAgICAgICB3cC5hamF4LnNlbmQoZGF0YS5hY3Rpb24sIHtcbiAgICAgICAgICAgICAgICAgICAgZGF0YSxcbiAgICAgICAgICAgICAgICAgICAgc3VjY2VzcyA6IChyZXNwb25zZSkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLnJlcGxhY2UobGluaylcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgZXJyb3IgICA6ICgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5yZXBsYWNlKGxpbmspXG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vY29uc29sZS5sb2coZGF0YSlcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkKCc6cmFkaW8nLCBpZCkub24oJ2NoYW5nZScsIGZ1bmN0aW9uICgpIHtcblxuICAgICAgICAgICAgICAgICQodGhpcykuY2xvc2VzdCgnLmZlZWRiYWNrLWRpYWxvZy1mb3JtLWJvZHknKS5maW5kKCcuZmVlZGJhY2stdGV4dCcpLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSkuaGlkZSgpO1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5uZXh0QWxsKCcuZmVlZGJhY2stdGV4dCcpLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpLnNob3coKS5mb2N1cygpO1xuICAgICAgICAgICAgICAgIC8vIGNvbnNvbGUubG9nKCQodGhpcykudmFsKCkpXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgJCgnLndwLWxpc3QtdGFibGUucGx1Z2lucycpLmZpbmQoJ1tkYXRhLXNsdWc9XCInICsgcGx1Z2luc2x1ZyArICdcIl0uYWN0aXZlJykuZWFjaChmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgICAgICBsZXQgZGVhY3RpdmF0ZV9saW5rID0gJCh0aGlzKS5maW5kKCcuZGVhY3RpdmF0ZSBhJykucHJvcCgnaHJlZicpO1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5kYXRhKCdkZWFjdGl2YXRlX2xpbmsnLCBkZWFjdGl2YXRlX2xpbmspO1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCcuZGVhY3RpdmF0ZSBhJykub24oJ2NsaWNrJywgKGV2ZW50KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgICAgICAgICAgICAgJChpZCkuZGF0YSgnZ3dwLWRlYWN0aXZhdGUtZGlhbG9nLW9wZW5lcicsIHRoaXMpLmRpYWxvZygnb3BlbicpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICByZXR1cm4gR1dQQWRtaW5IZWxwZXI7XG59KShqUXVlcnkpO1xuXG5leHBvcnQgeyBHV1BBZG1pbkhlbHBlciB9O1xuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyBzcmMvanMvR1dQQWRtaW5IZWxwZXIuanMiXSwibWFwcGluZ3MiOiI7Ozs7Ozs7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7Ozs7Ozs7Ozs7OztBQzdEQTtBQUNBO0FBQ0E7QUFBQTtBQUFBO0FBQUE7QUFDQTtBQUNBO0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBOzs7Ozs7Ozs7Ozs7OztBQ2JBO0FBQ0E7QUFDQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQ0E7QUFEQTtBQUFBO0FBQUE7QUFJQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBREE7QUFDQTtBQUdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFmQTtBQUFBO0FBQUE7QUFrQkE7QUFDQTtBQUNBO0FBQ0E7QUFyQkE7QUFBQTtBQUFBO0FBQ0E7QUF3QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBSEE7QUFLQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQWpDQTtBQUNBO0FBbUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFQQTtBQUNBO0FBU0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUE1R0E7QUFDQTtBQURBO0FBQUE7QUFDQTtBQThHQTtBQUNBO0FBQ0E7Ozs7O0EiLCJzb3VyY2VSb290IjoiIn0=