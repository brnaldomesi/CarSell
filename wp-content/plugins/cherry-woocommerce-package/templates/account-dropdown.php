<?php	
/**
 * account dropdown template
 *
 * @author 		Cherry Team
 * @category 	Core
 * @package 	cherry-woocommerce-package/templates
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $cherry_wc_account_dropdown;
?>
<div class="cherry-wc-account dropdown">
	<?php 
		if ( is_user_logged_in() ) {
			$title = $cherry_wc_account_dropdown->account_options['logged_in_label'];
		} else {
			$title = $cherry_wc_account_dropdown->account_options['not_logged_in_label'];
		}
		echo apply_filters( 'cherry_woocommerce_account_title', '<a class="cherry-wc-account_title" data-toggle="dropdown" href="#">' . $title . '</a>', $title ); 
	?>
	<div class="cherry-wc-account_content"><?php 
		$cherry_wc_account_dropdown->show_account_list();
		$cherry_wc_account_dropdown->show_account_auth(); 
	?></div>
</div>