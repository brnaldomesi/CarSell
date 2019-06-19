<?php
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
?>

<div class="gwp-tutorials-wrapper">

    <ul>
        <li>
            <div class="tutorial-image-wrapper"><img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-1.png' ) ?>"></div>
            <div class="tutorial-description-wrapper">
                <h3>Color Swatches For Attribute Variation</h3>
                <div class="tutorial-contents">
                    The option turns product attribute variation select options drop down into color swatches. It’s the best fit options for the variable products comes with multiple attribute variations.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://bit.ly/plugin-tutorial-tab-color-demo" target="_blank" class="button button-live-demo">Live Demo</a>
                    <a href="http://bit.ly/wvs-tuts-01" target="_blank" class="button button-docs">Documentation</a>
                </div>
            </div>
        </li>


        <li>
            <div class="tutorial-description-wrapper">
                <h3>Image Swatches For Attribute Variation</h3>
                <div class="tutorial-contents">
                    Images variation does more than color swatches. When it comes to display images as product variation, this option comes handy and highly engaging for conversion.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://bit.ly/plugin-demo-image-tab" target="_blank" class="button button-live-demo">Live Demo</a>
                    <a href="http://bit.ly/wvs-tuts-02" target="_blank" class="button button-docs">Documentation</a>
                </div>
            </div>
            <div class="tutorial-image-wrapper"><img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-2.png' ) ?>"></div>
        </li>

        <li>
            <div class="tutorial-image-wrapper"><img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-3.png' ) ?>"></div>
            <div class="tutorial-description-wrapper">
                <h3>Button/Label Swatches For Attribute Variation</h3>
                <div class="tutorial-contents">
                    When comes to show available product size, quantity and other variation related details, button/label swatch boost conversion extensively. It allows selecting customers to select their desired product variation quickly.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://bit.ly/button-plugin-demo-tab" target="_blank" class="button button-live-demo">Live Demo</a>
                    <a href="http://bit.ly/wvs-tuts-03" target="_blank" class="button button-docs">Documentation</a>
                </div>
            </div>
        </li>

        <li>
            <div class="tutorial-description-wrapper">
                <h3>Integrated With Quick View</h3>
                <div class="tutorial-contents">
                    Besides showing swatches on product details page and archive pages, it can enable swatches on quickview lighboxes to maximize store wide sales.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://bit.ly/tutorial-tab-04" target="_blank" class="button button-live-demo">Live Demo</a>
                    <a href="http://bit.ly/wvs-tuts-04" target="_blank" class="button button-docs">Documentation</a>
                </div>
            </div>
            <div class="tutorial-image-wrapper"><img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-4.png' ) ?>"></div>
        </li>


        <li>
            <div class="tutorial-image-wrapper"><img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-5.png' ) ?>"></div>
            <div class="tutorial-description-wrapper">
                <h3>Text Tooltip</h3>
                <div class="tutorial-contents">
                    Tooltip denotes the variation details to explain more. It can be disabled and customized the title text and tooltip background from the admin backend.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://bit.ly/tutorial-tab-06" target="_blank" class="button button-live-demo">Live Demo</a>
                    <a href="http://bit.ly/wvs-tuts-05" target="_blank" class="button button-docs">Documentation</a>
                </div>
            </div>

        </li>

        <li>

            <div class="tutorial-description-wrapper">
                <h3>Blur/Hide Out of Stock Variation</h3>
                <div class="tutorial-contents">
                    Showing out of stock variation seems unnecessary. So, the plugin offers option to blur or hide the out of stock variation to simplify in stock variation.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://bit.ly/tutorial-tab-05" target="_blank" class="button button-live-demo">Live Demo</a>
                    <a href="http://bit.ly/wvs-tuts-06" target="_blank" class="button button-docs">Documentation</a>
                </div>
            </div>
            <div class="tutorial-image-wrapper"><img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-6.png' ) ?>"></div>
        </li>

        <li>
            <div class="tutorial-image-wrapper">
				<?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                    <div class="ribbon"><span><?php esc_html_e( 'PRO', 'woo-varriation-swatches' ) ?></span></div>
				<?php endif; ?>
                <img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-10.jpg' ) ?>">
            </div>
            <div class="tutorial-description-wrapper">
                <h3>Display Swatch in Catalog / Archive Page</h3>
                <div class="tutorial-contents">
                    To boost store conversion and engagement, Attribute variation swatches plugin enables swatch on the catalog page. It allows customers to check product variation from the archive page and add them to the cart.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://bit.ly/tutorial-tab-07" target="_blank" class="button button-live-demo">Live Demo</a>
                    <a href="http://bit.ly/wvs-tuts-07" target="_blank" class="button button-docs">Documentation</a>
					<?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                        <a href="<?php echo woo_variation_swatches()->get_pro_link( 'settings-tutorial' ) ?>" target="_blank" class="button button-pro">Upgrade to pro</a>
					<?php endif; ?>
                </div>
            </div>

        </li>
    </ul>

</div>
