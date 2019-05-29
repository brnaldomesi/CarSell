<?php

class MPSLWidget extends WP_Widget {

    function __construct() {
        global $mpsl_settings;
        parent::__construct(false, $mpsl_settings['product_name'], array('description' => sprintf(__('Add %s', 'motopress-slider-lite'), $mpsl_settings['product_name'])));
    }

    public function widget($args, $instance) {
	    if (!empty($instance['alias'])) {
	        echo $args['before_widget'];
	        echo get_mpsl_slider($instance['alias']);
		    echo $args['after_widget'];
	    }
    }

    public function form($instance) {
        $sliders = new MPSLSlidersList();
        $list = $sliders->getSliderAliases();
        $alias = isset($instance['alias']) ? $instance['alias'] : '';
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('alias'); ?>"><?php _e('Select slider:', 'motopress-slider-lite'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('alias'); ?>" name="<?php echo $this->get_field_name('alias'); ?>">
                <option value="">-- <?php _e('SELECT', 'motopress-slider-lite'); ?> --</option>
                <?php foreach ($list as $value) : ?>
                    <option value="<?php echo esc_attr($value['alias']); ?>" <?php selected($alias, $value['alias']); ?>>
                        <?php echo esc_attr($value['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
    <?php
    }

    public function update($newInstance, $oldInstance) {
	    $instance = array();
        $instance['alias'] = !empty($newInstance['alias']) ? strip_tags($newInstance['alias']) : '';

        return $instance;
    }

}