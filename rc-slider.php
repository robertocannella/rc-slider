<?php

/*
Plugin Name: Rc Slider
Plugin URI: http://robertocannella.com/wordpress
Description: A slider plugin
Version: 1.0
Author: Roberto Cannella
Author URI: http://robertocannella.com_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
Requires: 6.2
Text Domain: rc-slider
Domain Path: /languages
*/

/*
{Rc Slider} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

{Rc Slider} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with {Rc Slider}. If not, see {ttp://robertocannella.com/wordpress}.
*/

if (!defined('ABSPATH')) {
    die("-not allowed");
    exit;
}

/**
 *  Main RC_Slider class
 */
if (!class_exists('RC_Slider')) {
    class RC_Slider
    {
        public function __construct()
        {
            $this->defineConstants();
            require_once ( RC_SLIDER_PATH . '/post-types/class.RC_Slider_Post_Type.php' );
            $rc_slider_post_type = new RC_Slider_Post_Type();
        }

        /**
         * Define global constants here
         */
        public function defineConstants(): void
        {
            define ( 'RC_SLIDER_PATH' , plugin_dir_path( __FILE__ ));
            define ( 'RC_SLIDER_URL' , plugin_dir_url( __FILE__ ));
            define ( 'RC_SLIDER_VERSION' , '1.0.0' );

        }

        /**
         * Activation
         */
        public static function activate(){

            // Code to register custom post types, taxonomies, and rewrite rules
            // ...

            // flush_rewrite_rules() does not work great when activating plugin, use
            // update_option to clear table
             flush_rewrite_rules(); // Flush the rewrite rules after modifications
           //  update_option( ' rewrite_rules' );// Flush the rewrite rules after modifications

        }
        /**
         * Deactivations
         */
        public static function deactivate(){

            // Code to unregister custom post types, taxonomies, and rewrite rules
            // ...
            flush_rewrite_rules(); // Flush the rewrite rules after modifications

        }
        /**
         * Uninstall
         */
        public static function uninstall(){

        }

    }
}

/**
 * Instantiate the plugin
 */

if (class_exists('RC_Slider')) {

    register_activation_hook(__FILE__, ['RC_Slider', 'activate']);
    register_deactivation_hook(__FILE__, ['RC_Slider', 'deactivate']);
    register_uninstall_hook(__FILE__, ['RC_Slider', 'uninstall']);

    $rc_slider = new RC_Slider();

}