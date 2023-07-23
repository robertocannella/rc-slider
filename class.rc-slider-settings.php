<?php 

if( ! class_exists( 'RC_Slider_Settings' )){
    class RC_Slider_Settings{

        public static $options;

        public function __construct(){
            self::$options = get_option( 'rc_slider_options' );
            add_action( 'admin_init', array( $this, 'adminInit') );
        }

        public function adminInit():void{
            // More about settings API: http://presscoders.com/wordpress-settings-api-explained/
            register_setting( 'rc_slider_group', 'rc_slider_options',[$this, 'rcSliderValidate' ]);

            add_settings_section(   // PAGE 1
                id:'rc_slider_main_section',
                title: 'How does it work?',
                callback: null,
                page:'rc_slider_page1'
            );

            add_settings_field(
                'rc_slider_shortcode',
                'Shortcode',
                array( $this, 'rcSliderShortcodeCallback' ),
                'rc_slider_page1',
                'rc_slider_main_section'
            );

            // PAGE 2
            add_settings_section(
                'rc_slider_second_section',
                'Other Plugin Options',
                null,
                'rc_slider_page2'
            );
            add_settings_field(
                'rc_slider_title',
                'Slider Title',
                array( $this, 'rcSliderTitleCallback' ),
                'rc_slider_page2',
                'rc_slider_second_section'
            );

            add_settings_field(
                'rc_slider_bullets',
                'Display Bullets',
                array( $this, 'rcSliderBulletsCallback' ),
                'rc_slider_page2',
                'rc_slider_second_section'
            );

            add_settings_field(
                'rc_slider_style',
                'Slider Style',
                array( $this, 'rcSliderStyleCallback' ),
                'rc_slider_page2',
                'rc_slider_second_section',
                array(
                    'items' => array(
                        'style-1',
                        'style-2'
                    ),
                    'label_for' => 'rc_slider_style'
                )

            );

        }

        public function rcSliderShortcodeCallback() :void{
            ?>
            <span>Use the shortcode [rc_slider] to display the slider in any page/post/widget</span>
            <?php
        }
        public function rcSliderTitleCallback(): void
        {
            ?>
            <input
                    type="text"
                    name="rc_slider_options[rc_slider_title]"
                    id="rc_slider_title"
                    value="<?php echo isset( self::$options['rc_slider_title'] ) ? esc_attr( self::$options['rc_slider_title'] ) : ''; ?>"
            >
            <?php
        }

        public function rcSliderBulletsCallback(): void
        {
            ?>
            <input
                    type="checkbox"
                    name="rc_slider_options[rc_slider_bullets]"
                    id="rc_slider_bullets"
                    value="1"
                <?php
                if( isset( self::$options['rc_slider_bullets'] ) ){
                    checked( "1", self::$options['rc_slider_bullets'], true );
                }
                ?>
            />
            <label for="rc_slider_bullets">Whether to display bullets or not</label>

            <?php
        }

        public function rcSliderStyleCallback( $args ) : void{
            ?>
            <select
                    id="rc`_slider_style"
                    name="rc_slider_options[rc_slider_style]">
                <?php
                foreach( $args['items'] as $item ):
                    ?>
                    <option value="<?php echo esc_attr( $item ); ?>"
                        <?php
                        isset( self::$options['rc_slider_style'] ) ? selected( $item, self::$options['rc_slider_style'], true ) : '';
                        ?>
                    >
                        <?php echo esc_html( ucfirst( $item ) ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php
        }
        public function rcSliderValidate( $input ): array
        {
            // Use switch for different types of fields: text|url|number
            $new_input = array();
            foreach( $input as $key => $value ){
                switch ($key){
                    case 'rc_slider_title':
                        if( empty( $value )){
                            $value = 'Please enter a value.';
                        }
                        $new_input[$key] = sanitize_text_field( $value );
                        break;
                    default:
                        $new_input[$key] = sanitize_text_field( $value );
                        break;
                }
            }
            return $new_input;
        }
    }
}

