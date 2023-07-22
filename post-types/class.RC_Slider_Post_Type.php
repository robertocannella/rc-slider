<?php

if (!class_exists( 'RC_Slider_Post_Type ') ) {
    class RC_Slider_Post_Type
    {
        public function __construct()
        {
            add_action('init', [$this, 'createPostType'], 10);
            add_action('add_meta_boxes', [$this, 'addMetaBoxes']);


        }

        public function createPostType(): void
        {
            $labels = array(
                'name' => _x('Sliders', 'rc-slider'),
                'singular_name' => _x('Slider', 'rc-slider'),
                'search_items' => __('Search Sliders', 'rc-slide'),
            );

            $args = array(
                'labels' => $labels,
                'description' => 'Slider custom post type.',
                // 'supports'           => array( 'title', 'editor', 'author', 'thumbnail' ),
                'supports' => array('title', 'editor', 'thumbnail'),
                'hierarchical' => false,
                'show_ui' => true,
                'exclude_from_search' => false,
                'public' => true,
                'publicly_queryable' => true,
                'show_in_menu' => true,
                'show_in_admin_bar' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'slider'),
                'capability_type' => 'post',
                'can_export' => true,
                'has_archive' => true,
                'menu_position' => 5,
                //'taxonomies'         => array( 'category', 'post_tag' ),
                'show_in_rest' => true,
                'menu_icon' => 'dashicons-images-alt2'
            );

            register_post_type('rc-slider', $args);

        }

        public function addMetaBoxes(): void
        {
            add_meta_box(
                id: 'rc_slider_meta_box',
                title: 'Link Options',
                callback: [$this, 'addInnerMetaBoxes'],
                screen: 'rc-slider',
                context: 'normal',
                priority: 'high',

            );
        }
        public function addInnerMetaBoxes ($post):void {

        }

    }
}
