(function($)
{
var LiveChat =
{
    init: function()
	{
		this.signInWithLiveChat();
		this.bindDisconnect();
		this.hideInstalledNotification();
		this.settingsForm();
	},

    bindEvent: function(element, eventName, eventHandler) {
        if (element.addEventListener){
            element.addEventListener(eventName, eventHandler, false);
        } else if (element.attachEvent) {
            element.attachEvent('on' + eventName, eventHandler);
        }
    },

    signInWithLiveChat: function () {
        var logoutButton = document.getElementById('resetAccount'),
            iframeEl = document.getElementById('login-with-livechat');

        LiveChat.bindEvent(window, 'message', function (e) {
            if(e.data.startsWith('{')) {
                var lcDetails = JSON.parse(e.data);
                switch (lcDetails.type) {
                    case 'logged-in':
                        var licenseForm = $('div#wordpress-livechat-container div#useExistingAccount form#licenseForm');
                        if(licenseForm.length) {
                            licenseForm.find('input#licenseEmail').val(lcDetails.email);
                            licenseForm.find('input#licenseNumber').val(lcDetails.license);
                            LiveChat.sendEvent(
                                'Integrations: User authorized the app',
                                lcDetails.license,
                                lcDetails.email,
                                function () {
                                    licenseForm.submit();
                                }
                            );
                        }
                        break;
                    case 'signed-out':
                        $('#login-with-livechat').css('display', 'block');
                        $('#logout').css('display', 'none');
                        break;
                }
            }
        });

        if(logoutButton) {
            LiveChat.bindEvent(logoutButton, 'click', function (e) {
                sendMessage('logout');
            });
        }

        var sendMessage = function(msg) {
            iframeEl.contentWindow.postMessage(msg, '*');
        };
    },

    bindDisconnect: function() {
	    $('#resetAccount').click(function (e) {
            e.preventDefault();
            LiveChat.sendEvent(
                'Integrations: User unauthorized the app',
                lcDetails.license,
                lcDetails.email,
                function () {
                    location.href = $('#resetAccount').attr('href');
                }
            );
        });
    },

    sendEvent: function(eventName, license, email, callback) {
	    var amplitudeURL = 'https://queue.livechatinc.com/app_event/';
	    var data = {
	        "e" : JSON.stringify(
	            [{
                    "event_type": eventName,
                    "user_id": email,
                    "user_properties": {
                        "license": license
                    },
                    "product_name": "livechat",
                    "event_properties": {
                        "integration name": "wp-live-chat-software-for-wordpress"
                    }
                }]
            )
        };
	    $.ajax({
            url: amplitudeURL,
            type: 'GET',
            crossOrigin: true,
            data: data
        }).always(function () {
            if(callback) callback();
        });
    },

	hideInstalledNotification: function () {
        var notificationElement = $('.updated.installed');
        $('#installed-close').click(function () {
            notificationElement.slideUp();
        });
        setTimeout(function () {
            notificationElement.slideUp();
        }, 3000);
    },

    setSettings: function(settings) {
        $.ajax({
            url: '?page=livechat_settings',
            type: "POST",
            data: settings,
            dataType: 'json',
            cache: false,
            async: false,
            error: function () {
                alert('Something went wrong. Please try again or contact our support team.');
            }
        });
    },
    settingsForm: function() {
        $('.settings .title').click(function() {
            $(this).next('.onoffswitch').children('label').click();
        });
        $('.onoffswitch-checkbox').change(function() {
            var settings = {};
            $('.onoffswitch-checkbox').each(function(){
                var paramName = $(this).attr('id');
                if ($(this).is(':checked')) {
                    settings[paramName] = 1;
                } else {
                    settings[paramName]= 0;
                }
            });

            LiveChat.setSettings(settings);
        });
    }
};

$(document).ready(function()
{
	LiveChat.init();
});
})(jQuery);