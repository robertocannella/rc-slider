<?php

if (!class_exists( 'RC_Slider_Post_Type ') ) {
    class RC_Slider_Post_Type
    {
        public function __construct()
        {
            add_action('init', [$this, 'createPostType'], 10);
            add_action('add_meta_boxes', [$this, 'addMetaBoxes']);
            add_action( 'save_post', [$this, 'savePost'], 10, 2 );



            // ADMIN COLUMNS to include sortable meta data
            add_filter( 'manage_rc-slider_posts_columns', [$this, 'rcSliderCPTColumns']);
            add_action( 'manage_rc-slider_posts_custom_column',
                callback: [$this, 'rcSliderCustomColumns'],
                priority: 10,
                accepted_args:2);
            add_filter( 'manage_edit-rc-slider_sortable_columns', [$this, 'rcSliderSortableColumns'] );

            // Custom Search - JOINS posts meta table with posts table ON rc-slider.post_id = ID

            add_action( 'pre_get_posts', array( $this, 'customSearchQuery' ) );

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
                'show_in_menu' => false, // we have custom menu - turn this off
                'show_in_admin_bar' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'slider'),
                'capability_type' => 'post',
                'can_export' => true,
                'has_archive' => true,
                'menu_position' => 5,
                'taxonomies'         => array( 'category', 'post_tag' ),
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
            require_once ( RC_SLIDER_PATH . 'views/rc-slider_metabox.php');
        }

        public function savePost($post_id ): void{


            if (!$this->validateUser($post_id)) return;


            if( isset( $_POST['action'] ) && $_POST['action'] == 'editpost' ){
                $old_link_text = get_post_meta( $post_id, 'rc_slider_link_text', true );
                $new_link_text = $_POST['rc_slider_link_text'];
                $old_link_url = get_post_meta( $post_id, 'rc_slider_link_url', true );
                $new_link_url = $_POST['rc_slider_link_url'];

                if( empty( $new_link_text )){
                    update_post_meta( $post_id, 'rc_slider_link_text', 'Add some text' );
                }else{
                    update_post_meta( $post_id, 'rc_slider_link_text', sanitize_text_field( $new_link_text ), $old_link_text );
                }

                if( empty( $new_link_url )){
                    update_post_meta( $post_id, 'rc_slider_link_url', '#' );
                }else{
                    update_post_meta( $post_id, 'rc_slider_link_url', sanitize_text_field( $new_link_url ), $old_link_url );
                }


            }
        }
        public function rcSliderCPTColumns($columns)   {
            $columns['rc_slider_link_text'] = esc_html( 'Link Text', 'rc-slider');
            $columns['rc_slider_link_url'] = esc_html( 'Link URL', 'rc-slider');

            return $columns;
        }
        public function rcSliderCustomColumns($column, $post_id):void {
            switch( $column ){
                case 'rc_slider_link_text':
                    echo esc_html( get_post_meta( $post_id, 'rc_slider_link_text', true ) );
                    break;
                case 'rc_slider_link_url':
                    echo esc_url( get_post_meta( $post_id, 'rc_slider_link_url', true ) );
                    break;
            }
        }

        public function rcSliderSortableColumns($columns) {
            $columns['rc_slider_link_text'] = 'rc_slider_link_text';
            $columns['rc_slider_link_url'] = 'rc_slider_link_url';
            return $columns;
        }
        private function validateUser($post_id):bool {
            if( isset( $_POST['rc_slider_nonce'] ) ){
                if( ! wp_verify_nonce( $_POST['rc_slider_nonce'], 'rc_slider_nonce' ) ){
                    return false;
                }
            }

            if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                return false;
            }

            if( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'rc-slider' ){
                if( ! current_user_can( 'edit_page', $post_id ) ){
                    return false;
                }elseif( ! current_user_can( 'edit_post', $post_id ) ){
                    return false;
                }
            }
            return true;
        }

        /**
         * @param $query
         * @return void
         * Custom Search Query. Combines meta field with main search. May need to update if searching by category.
         */
        public function customSearchQuery($query): void {
            global $pagenow, $wpdb;

            if ( is_search() ) {
                // Prevent duplicates in the search results
                // global $wp_query;
                // error_log(print_r($wp_query->get_queried_object(),true));

                add_filter( 'posts_distinct', function( $distinct ) {
                    return "DISTINCT";
                });

                // Modify the WHERE clause to include custom meta fields in the search
                add_filter( 'posts_where', function( $where ) use ( $wpdb ) {
                    $search_term = get_search_query();
                    if ( !empty($search_term) ) {
                        $where = preg_replace(
                            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
                            "(".$wpdb->posts.".post_title LIKE $1) OR (rc_slide_pm.meta_value LIKE $1)", $where );
                    }
                    return $where;
                });

                add_action( 'posts_join', function( $join ) use ( $wpdb ) {
                    // Check if the wp_postmeta table is already joined
                    if (strpos($join, $wpdb->postmeta) === false) {
                        // If not joined, then add the LEFT JOIN clause with the unique alias 'pm'
                        $join .= ' LEFT JOIN ' . $wpdb->postmeta . ' AS rc_slide_pm ON ' . $wpdb->posts . '.ID = rc_slide_pm.post_id ';
                    }
                    return $join;
                });
            }
        }
        /**
         * @param $join
         * @return mixed|string
         * Search by meta values only
         */
        public function alterMainQuery($query) {
            $post_type = 'rc-slider';
            $search_term = $query->query_vars['s'];
            $custom_fields = ['rc_slider_link_text', 'rc_slider_link_url'];


            if (!is_admin()) {
                return;
            }

            if (!$query->is_main_query()) {
                return;
            }

            if ($query->query['post_type'] != $post_type) {
                return;
            }

           $query->query_vars['s'] = '';

            if ($search_term != '') {
                $meta_query = array('relation' => 'OR');
                foreach ($custom_fields as $custom_field) {
                    $meta_query[] = array(
                        'key' => $custom_field,
                        'value' => $search_term,
                        'compare' => 'LIKE'
                    );
                }

                $query->set('meta_query', $meta_query);
            }
        }

    }
}
