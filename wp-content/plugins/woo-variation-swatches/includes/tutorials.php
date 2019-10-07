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
                    <a href="http://j.mp/color-swatches-insideplugin" target="_blank" class="button button-live-demo">Live Video Preview</a>
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
                    <a href="http://j.mp/color-swatches-preview-insideplugin" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    <a href="http://bit.ly/wvs-tuts-02" target="_blank" class="button button-docs">Documentation</a>
                </div>
            </div>
            <div class="tutorial-image-wrapper"><img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-2.png' ) ?>"></div>
        </li>

        <li>
            <div class="tutorial-image-wrapper"><img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-3.png' ) ?>"></div>
            <div class="tutorial-description-wrapper">
                <h3>Auto Convert All Variation Dropdown Into Button Swatches By Default</h3>
                <div class="tutorial-contents">
                    Color and Images swatches need to configure globally or from each product pages, but the free version can turn all variation select dropdown into button swatches
                </div>
                <div class="tutorial-buttons">
                    <a href="http://j.mp/button-video-preview-insideplugin" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    
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
                    <a href="http://j.mp/quickview-preview-inside-plugin" target="_blank" class="button button-live-demo">Live Video Preview</a>
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
                    <a href="http://j.mp/tooltip-plugininside" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    <a href="http://bit.ly/wvs-tuts-05" target="_blank" class="button button-docs">Documentation</a>
                </div>
            </div>

        </li>

         <li>

            <div class="tutorial-description-wrapper">
                <h3>Cross/Blur/Hide Out of Stock Variation</h3>
                <div class="tutorial-contents">
                    Showing out of stock variation seems unnecessary. So, the plugin offers option to blur or hide the out of stock variation to simplify in stock variation.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://bit.ly/cross-outofstock-insideplugin" target="_blank" class="button button-live-demo">Live Video Preview</a>
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
                <img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-14.gif' ) ?>">
            </div>
            <div class="tutorial-description-wrapper">
                <h3>Auto Convert All Dropdowns Into Image Swatches If Variation Has Featured Image. (Most Popular & Time Saving Feature)</h3>
                <div class="tutorial-contents">
                    Generally, the variation comes with feature images. If your product variations have it’s featured image set, premium version can convert your variation select drodown into image swatches just after installation. No Configuration would be needed. Best for <strong>Printful</strong>, <strong>Alidrop</strong>, And other <strong>Dropshipping</strong> as well as <strong>Multi Vendor</strong> Plugins
                </div>
                <div class="tutorial-buttons">
                    <a href="http://j.mp/autoimage-swatches-generate-plugininside" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    <a href="http://j.mp/auto-image-swatches-insideplugin" target="_blank" class="button button-docs">Documentation</a>
                    <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                        <a href="<?php echo woo_variation_swatches()->get_pro_link( 'settings-tutorial' ) ?>" target="_blank" class="button button-pro">Upgrade to pro</a>
                    <?php endif; ?>
                </div>
            </div>

        </li>

        <li>
            <div class="tutorial-description-wrapper">
                <h3>Radio Swatches For Attribute Variation</h3>
                <div class="tutorial-contents">
                    Besides showing swatches on product details page and archive pages, it can enable swatches on quickview lighboxes to maximize store wide sales.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://j.mp/radio-swatches-insideplugin" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    <a href="http://bit.ly/radio-tuts-doc-inside" target="_blank" class="button button-docs">Documentation</a>
                    <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                        <a href="<?php echo woo_variation_swatches()->get_pro_link( 'settings-tutorial' ) ?>" target="_blank" class="button button-pro">Upgrade to pro</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tutorial-image-wrapper">
                <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                    <div class="ribbon"><span><?php esc_html_e( 'PRO', 'woo-varriation-swatches' ) ?></span></div>
                <?php endif; ?>
                <img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-7.jpg' ) ?>">
            </div>
        </li>

        <li>
            <div class="tutorial-image-wrapper">
                <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                    <div class="ribbon"><span><?php esc_html_e( 'PRO', 'woo-varriation-swatches' ) ?></span></div>
                <?php endif; ?>
                <img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-13.jpg' ) ?>">
            </div>
            <div class="tutorial-description-wrapper">
                <h3>Enable Dual Color Variation Swatches</h3>
                <div class="tutorial-contents">
                    Besides the color, image, label, and radio swatches, you can enable dual color swatches as well. If you have dual color for your product, you can represent it from dual color variation swatches. 
                </div>
                <div class="tutorial-buttons">
                    <a href="http://j.mp/dual-color-inside-plugin" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    <a href="http://j.mp/dual-color-doc-inside-plugin" target="_blank" class="button button-docs">Documentation</a>
                    <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                        <a href="<?php echo woo_variation_swatches()->get_pro_link( 'settings-tutorial' ) ?>" target="_blank" class="button button-pro">Upgrade to pro</a>
                    <?php endif; ?>
                </div>
            </div>

        </li>

         <li>

            <div class="tutorial-description-wrapper">
                <h3>Change Product Gallery Image Selecting Only Color Variation Like Amazon and Aliexpress (Or Selected Variation)</h3>
                <div class="tutorial-contents">
                   Variable product changes variation image when all available attribute variations are selected. First time in the WooCommerce Variation Swatches plugin history, we enabled option to change gallery image selecting single attribute variation. You don’t need to match entire attribute variation to change variation image. 
                </div>
                <div class="tutorial-buttons">
                    <a href="http://j.mp/amazon-like-swatches-insideplugin" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    <a href="http://bit.ly/amazon-swatches-doc-insideplugin" target="_blank" class="button button-docs">Documentation</a>
                    <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                        <a href="<?php echo woo_variation_swatches()->get_pro_link( 'settings-tutorial' ) ?>" target="_blank" class="button button-pro">Upgrade to pro</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tutorial-image-wrapper">
                <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                    <div class="ribbon"><span><?php esc_html_e( 'PRO', 'woo-varriation-swatches' ) ?></span></div>
                <?php endif; ?>
                <img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-15.gif' ) ?>">
            </div>
        </li>

        <li>
            <div class="tutorial-image-wrapper">
                <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                    <div class="ribbon"><span><?php esc_html_e( 'PRO', 'woo-varriation-swatches' ) ?></span></div>
                <?php endif; ?>
                <img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-16.jpg' ) ?>">
            </div>
            <div class="tutorial-description-wrapper">
                <h3>Individual Product Based Swatches Customization</h3>
                <div class="tutorial-contents">
                WooCommerce Variation Swatch plugin offers global swatches. If you need personalized variation per variable product basis, you quickly achieve them from the desired product admin page.                </div>
                <div class="tutorial-buttons">
                    <a href="http://j.mp/productbased-customization-insideplugin" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    <a href="http://bit.ly/product-basis-swatches-plugininsie" target="_blank" class="button button-docs">Documentation</a>
                    <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                        <a href="<?php echo woo_variation_swatches()->get_pro_link( 'settings-tutorial' ) ?>" target="_blank" class="button button-pro">Upgrade to pro</a>
                    <?php endif; ?>
                </div>
            </div>

        </li>

        <li>

            <div class="tutorial-description-wrapper">
                <h3>Show Image Tooltip in Product and Archive Pages</h3>
                <div class="tutorial-contents">
                    Sometimes tooltip text is not enough to describe your product attribute variation. In this case, Image tooltip can do the rest.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://j.mp/image-tooltip-insideplugin-demo" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    <a href="http://j.mp/image-tooltip-doc-plugininside" target="_blank" class="button button-docs">Documentation</a>
                    <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                        <a href="<?php echo woo_variation_swatches()->get_pro_link( 'settings-tutorial' ) ?>" target="_blank" class="button button-pro">Upgrade to pro</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tutorial-image-wrapper">
                <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                    <div class="ribbon"><span><?php esc_html_e( 'PRO', 'woo-varriation-swatches' ) ?></span></div>
                <?php endif; ?>
                <img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-17.jpg' ) ?>">
            </div>
        </li>

        <li>
            <div class="tutorial-image-wrapper">
                <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                    <div class="ribbon"><span><?php esc_html_e( 'PRO', 'woo-varriation-swatches' ) ?></span></div>
                <?php endif; ?>
                <img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-18.jpg' ) ?>">
            </div>
            <div class="tutorial-description-wrapper">
                <h3>Generate Sarable Variation Link</h3>
                <div class="tutorial-contents">
                    WooCommerce doesn’t come with this option. We brought this feature the first time. With this feature, you can generate and share your specific attribute link in your customer for a quick purchase or share it the social media.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://j.mp/generatelink-demo-plugininside" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    <a href="http://bit.ly/generlink-doc-plugininside" target="_blank" class="button button-docs">Documentation</a>
                    <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                        <a href="<?php echo woo_variation_swatches()->get_pro_link( 'settings-tutorial' ) ?>" target="_blank" class="button button-pro">Upgrade to pro</a>
                    <?php endif; ?>
                </div>
            </div>

        </li>

        <li>

            <div class="tutorial-description-wrapper">
                <h3>Show Only One Attribute in Archive Page</h3>
                <div class="tutorial-contents">
                    If your product has 5 attributes, generally, It shows all the 5 swatches attributes on the archive pages. The first time, we launched this feature to show only a selected attribute on the shop page.
                </div>
                <div class="tutorial-buttons">
                    <a href="http://j.mp/one-shop-swatches-demo-plugininside" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    <a href="http://bit.ly/shop-one-swatches-doc-insideplugin" target="_blank" class="button button-docs">Documentation</a>
                    <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                        <a href="<?php echo woo_variation_swatches()->get_pro_link( 'settings-tutorial' ) ?>" target="_blank" class="button button-pro">Upgrade to pro</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tutorial-image-wrapper">
                <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                    <div class="ribbon"><span><?php esc_html_e( 'PRO', 'woo-varriation-swatches' ) ?></span></div>
                <?php endif; ?>
                <img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-11.jpg' ) ?>">
            </div>
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
                    <a href="http://j.mp/archive-swatches-demo-insideplugin" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    <a href="http://bit.ly/archive-shop-doc-plugininside" target="_blank" class="button button-docs">Documentation</a>
					<?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                        <a href="<?php echo woo_variation_swatches()->get_pro_link( 'settings-tutorial' ) ?>" target="_blank" class="button button-pro">Upgrade to pro</a>
					<?php endif; ?>
                </div>
            </div>

        </li>

         <li>

            <div class="tutorial-description-wrapper">
                <h3>Set MORE Link To avoid misalignment of the uneven number of swatches</h3>
                <div class="tutorial-contents">
                    Products may have a different number of swatches which creates misalignment issues in the product archive pages. To avoid that, you can enable MORE link. Keep archive product look equal and parallel 
                </div>
                <div class="tutorial-buttons">
                    <a href="http://j.mp/addmorelink-demo-plugininside" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    <a href="http://j.mp/addmorelink-doc-plugininside" target="_blank" class="button button-docs">Documentation</a>
                    <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                        <a href="<?php echo woo_variation_swatches()->get_pro_link( 'settings-tutorial' ) ?>" target="_blank" class="button button-pro">Upgrade to pro</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tutorial-image-wrapper">
                <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                    <div class="ribbon"><span><?php esc_html_e( 'PRO', 'woo-varriation-swatches' ) ?></span></div>
                <?php endif; ?>
                <img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-19.jpg' ) ?>">
            </div>
        </li>

        <li>
            <div class="tutorial-image-wrapper">
                <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                    <div class="ribbon"><span><?php esc_html_e( 'PRO', 'woo-varriation-swatches' ) ?></span></div>
                <?php endif; ?>
                <img alt="" src="<?php echo woo_variation_swatches()->images_uri( 'tutorial-20.jpg' ) ?>">
            </div>
            <div class="tutorial-description-wrapper">
                <h3>Highlight Desired Product Attribute</h3>
                <div class="tutorial-contents">
                    Your product may have plenty of attributes. If you want to keep a single attribute standout. you can make the attribute enlarged using this popular WooCommerce Variation Swatches plugin
                </div>
                <div class="tutorial-buttons">
                    <a href="http://j.mp/highlight-demo-plugininside" target="_blank" class="button button-live-demo">Live Video Preview</a>
                    <a href="http://bit.ly/enlarage-selected-attr-doc-plugininside" target="_blank" class="button button-docs">Documentation</a>
                    <?php if ( ! woo_variation_swatches()->is_pro_active() ): ?>
                        <a href="<?php echo woo_variation_swatches()->get_pro_link( 'settings-tutorial' ) ?>" target="_blank" class="button button-pro">Upgrade to pro</a>
                    <?php endif; ?>
                </div>
            </div>

        </li>
    </ul>

</div>
