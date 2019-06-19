<?php

require_once('LiveChatHelper.class.php');

class TrackingCodeHelper extends LiveChatHelper
{
	public function render()
	{
		$tracking = '';

		if (LiveChat::get_instance()->is_installed())
		{
			$license_number = LiveChat::get_instance()->get_license_number();
			$settings = LiveChat::get_instance()->get_settings();
			$check_mobile = LiveChat::get_instance()->check_mobile();
			$check_logged = LiveChat::get_instance()->check_logged();
			$visitor = LiveChat::get_instance()->get_user_data();

			if (!$settings['disableMobile'] || ($settings['disableMobile'] && !$check_mobile)) {
				if (!$settings['disableGuests'] || ($settings['disableGuests'] && $check_logged)) {
					$tracking = <<<TRACKING_CODE_START
<script type="text/javascript">
	window.__lc = window.__lc || {};
	window.__lc.license = {$license_number};

TRACKING_CODE_START;

					$tracking .= <<<VISITOR_DATA
	window.__lc.visitor = {
		name: '{$visitor['name']}',
		email: '{$visitor['email']}'
	};

VISITOR_DATA;

					$tracking .= <<<TRACKING_CODE_END
	(function() {
		var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
		lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
	})();
</script>

TRACKING_CODE_END;

					$tracking .= <<<NOSCRIPT
<noscript>
<a href="https://www.livechatinc.com/chat-with/{$license_number}/">Chat with us</a>,
powered by <a href="https://www.livechatinc.com/?welcome" rel="noopener" target="_blank">LiveChat</a>
</noscript>
NOSCRIPT;

				}
			}
		}

		return $tracking;
	}
}