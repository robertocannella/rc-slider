<?php

if( ! class_exists('RC_Slider_Shortcode')){
    class RC_Slider_Shortcode{
        public function __construct(){
            add_shortcode( 'rc_slider', array( $this, 'addShortcode' ) );
        }

        public function addShortcode($attrs = array(), $content = null, $tag = '' ) : string|bool
        {

            $attrs = array_change_key_case( (array) $attrs, CASE_LOWER );

            extract( shortcode_atts(
                array(
                    'id' => '',
                    'orderby' => 'date'
                ),
                $attrs,
                $tag
            ));

            if( !empty( $id ) ){
                $id = array_map( 'absint', explode( ',', $id ) );
            }


            // Be sure to use require here not require_once.  This way multiple instances of
            // the shortcode can be added.
            ob_start();
            require( RC_SLIDER_PATH . 'views/rc-slider_shortcode.php' );
            wp_enqueue_script( 'rc-slider-main-jq' );
            wp_enqueue_style( 'rc-slider-main-css' );
            wp_enqueue_style( 'rc-slider-style-css' );
            rc_slider_options();
            return ob_get_clean();
        }
    }
}
