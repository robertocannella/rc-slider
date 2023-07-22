<?php

if (!class_exists( 'RC_Slider_Post_Type ') ) {
    class RC_Slider_Post_Type
    {
        public function __construct()
        {
            add_action( 'init' , [ $this, 'createPostType'] , 20);

        }
        public function createPostType (): void {
            $labels = array(
                'name'                  => _x( 'Sliders', 'rc-slider' ),
                'singular_name'         => _x( 'Slider',  'rc-slider' ),
         );

            $args = array(
                'labels'             => $labels,
                'description'        => 'Slider custom post type.',
                'supports'           => array( 'title', 'editor', 'author', 'thumbnail' ),
                'hierarchical'       => false,
                'show_ui'            => true,
                'public'             => true,
                'publicly_queryable' => true,
                'show_in_menu'       => true,
                'show_in_admin_bar'  => true,
                'query_var'          => true,
                'rewrite'            => array( 'slug' => 'slider' ),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'menu_position'      => 20,
                'taxonomies'         => array( 'category', 'post_tag' ),
                'show_in_rest'       => true
            );

            register_post_type('rc-slider', $args);

        }
    }

}
