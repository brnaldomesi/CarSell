<?php
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
?>

<div id="gwp-plugin-deactivate-feedback-dialog-wrapper-<?php echo esc_attr( $slug ) ?>" style="display: none">
    <form class="feedback-dialog-form" method="post" onsubmit="return false">
        <input type="hidden" name="action" value="gwp_deactivate_feedback"/>
        <input type="hidden" name="plugin" value="<?php echo esc_attr( $slug ) ?>"/>
        <input type="hidden" name="version" value="<?php echo esc_attr( $version ) ?>"/>
        <div class="feedback-dialog-form-caption"><?php esc_html_e( 'May we have a little info about why you are deactivating?', 'woo-variation-swatches' ); ?></div>
        <div class="feedback-dialog-form-body">
			<?php foreach ( $deactivate_reasons as $reason_key => $reason ) : ?>
                <div class="feedback-dialog-input-wrapper">
                    <input id="feedback-<?php echo esc_attr( $reason_key ); ?><?php echo esc_attr( $slug ) ?>" class="feedback-dialog-input" type="radio" name="reason_type" value="<?php echo esc_attr( $reason_key ); ?>"/>
                    <label for="feedback-<?php echo esc_attr( $reason_key ); ?><?php echo esc_attr( $slug ) ?>" class="feedback-dialog-label"><?php echo $reason[ 'title' ]; ?></label>
					<?php if ( ! empty( $reason[ 'input_placeholder' ] ) ) : ?>
                        <input value="<?php echo( isset( $reason[ 'input_value' ] ) ? $reason[ 'input_value' ] : '' ) ?>" class="feedback-text" style="display: none" disabled type="text" name="reason_text" placeholder="<?php echo esc_attr( $reason[ 'input_placeholder' ] ); ?>"/>
					<?php endif; ?>
					<?php if ( ! empty( $reason[ 'alert' ] ) ) : ?>
                        <div class="feedback-text feedback-alert"><?php echo $reason[ 'alert' ]; ?></div>
					<?php endif; ?>
                </div>
			<?php endforeach; ?>
        </div>
        <div class="feedback-dialog-form-buttons">
            <button class="button button-primary feedback-dialog-form-button-send" data-defaultvalue="<?php esc_html_e( 'Send &amp; Deactivate', 'woo-variation-swatches' ) ?>" data-deactivating="<?php esc_html_e( 'Deactivating...', 'woo-variation-swatches' ) ?>"><?php esc_html_e( 'Send &amp; Deactivate', 'woo-variation-swatches' ) ?></button>
            <span class="spinner"></span>
            <a href="#" class="feedback-dialog-form-button-skip"><?php esc_html_e( 'Skip &amp; Deactivate', 'woo-variation-swatches' ) ?></a>
            <div class="clear"></div>
        </div>
    </form>
</div>
