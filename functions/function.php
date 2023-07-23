<?php

//More options:  https://gist.github.com/warrendholmes/9481310
if( ! function_exists( 'rc_slider_get_placeholder_image' )){
    function rc_slider_get_placeholder_image(): string
    {
        return "<img src='" . RC_SLIDER_URL . "assets/images/default.jpg' class='img-fluid wp-post-image' />";
    }
}

if( ! function_exists( 'mv_slider_options' )){
    function rc_slider_options(): void
    {
        $show_bullets = isset( RC_Slider_Settings::$options['rc_slider_bullets'] ) && RC_Slider_Settings::$options['rc_slider_bullets'] == 1;

        wp_enqueue_script( 'rc-slider-options-js', RC_SLIDER_URL . 'vendor/flexslider/flexslider.js', array( 'jquery' ), RC_SLIDER_VERSION, true );
        wp_localize_script( 'rc-slider-options-js', 'SLIDER_OPTIONS', array(
            'controlNav' => $show_bullets
        ) );
    }
}
