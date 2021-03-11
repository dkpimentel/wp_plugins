<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

require( __DIR__ . '/classes/class.settings-api.php' );

class WC_Pro_Sale_Notification_Admin_Settings {

    private $settings_api;

    function __construct() {
        $this->settings_api = new WC_Pro_Sale_Notification_Settings_API();
        add_action( 'admin_init', [ $this, 'admin_init' ] );
        add_action( 'admin_menu', [ $this, 'admin_menu' ], 220 );
        add_action( 'admin_init', [$this, 'register_setting_fileds' ] );
        add_action('admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
        add_action( 'wsa_form_bottom_wcsales_general_tabs', [ $this, 'html_general_tabs' ] );
        add_action( 'wsa_form_bottom_wcsales_plugins_tabs', [ $this, 'html_our_plugins_library_tabs' ] );
        add_action( 'wsa_form_bottom_wcsales_fakes_data_tabs', [ $this, 'html_fake_data_tabs' ] );
    }

    // Admin Initialize
    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->admin_get_settings_sections() );
        $this->settings_api->set_fields( $this->admin_fields_settings() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    // Plugins menu Register
    function admin_menu() {

        $menu = 'add_menu_' . 'page';
        $menu(
            'wcsalenotification_panel',
            __( 'Sales Notification', 'wc-sales-notification-pro' ),
            __( 'Sales Notification', 'wc-sales-notification-pro' ),
            'wcsalenotification_page',
            NULL,
            'dashicons-megaphone',
            100
        );
        
        add_submenu_page(
            'wcsalenotification_page',
            __( 'Settings', 'wc-sales-notification-pro' ),
            __( 'Settings', 'wc-sales-notification-pro' ),
            'manage_options',
            'wcsalenotification',
            array ( $this, 'plugin_page' )
        );

    }

    // Admin Scripts
    public function enqueue_admin_scripts(){

        // wp core styles
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
        // wp core scripts
        wp_enqueue_script( 'jquery-ui-dialog' );

        //styles
        wp_enqueue_style( 'wcsales-admin', WCPRO_SALENOTIFICATION_PL_URL . 'admin/assets/css/admin_optionspanel.css', FALSE, WCPRO_SALENOTIFICATION_VERSION );
        
        //scripts
        wp_enqueue_script( 'wcsales-admin', WCPRO_SALENOTIFICATION_PL_URL . 'admin/assets/js/admin_scripts.js', array('jquery'), WCPRO_SALENOTIFICATION_VERSION, TRUE );

        $datalocalize = array(
            'contenttype' => wcsales_get_option_pro( 'notification_content_type','wcsales_settings_tabs', 'actual' ),
        );
        wp_localize_script( 'wcsales-admin', 'admin_wclocalize_data', $datalocalize );

    }

    // Options page Section register
    function admin_get_settings_sections() {
        $sections = array(
            
            array(
                'id'    => 'wcsales_general_tabs',
                'title' => esc_html__( 'General', 'wc-sales-notification-pro' )
            ),

            array(
                'id'    => 'wcsales_settings_tabs',
                'title' => esc_html__( 'Settings', 'wc-sales-notification-pro' )
            ),

            array(
                'id'    => 'wcsales_fakes_data_tabs',
                'title' => esc_html__( 'Fake Data', 'wc-sales-notification-pro' )
            ),

            array(
                'id'    => 'wcsales_plugins_tabs',
                'title' => esc_html__( 'Our Plugins', 'wc-sales-notification-pro' )
            ),

        );
        return $sections;
    }

    // Options page field register
    protected function admin_fields_settings() {

        $settings_fields = array(

            'wcsales_general_tabs' => array(),
            
            'wcsales_settings_tabs' => array(

                array(
                    'name'  => 'enableresalenotification',
                    'label'  => __( 'Enable / Disable Sale Notification', 'wc-sales-notification-pro' ),
                    'desc'  => __( 'Enable', 'wc-sales-notification-pro' ),
                    'type'  => 'checkbox',
                    'default' => 'off',
                    'class'=>'woolentor_table_row',
                ),

                array(
                    'name'    => 'notification_content_type',
                    'label'   => __( 'Notification Content Type', 'wc-sales-notification-pro' ),
                    'desc'    => __( 'Select Content Type', 'wc-sales-notification-pro' ),
                    'type'    => 'radio',
                    'default' => 'actual',
                    'options' => array(
                        'actual' => __('Real','wc-sales-notification-pro'),
                        'fakes'  => __('Fakes','wc-sales-notification-pro'),
                    )
                ),

                array(
                    'name'    => 'notification_pos',
                    'label'   => __( 'Position', 'wc-sales-notification-pro' ),
                    'desc'    => __( 'Sale Notification Position on frontend.', 'wc-sales-notification-pro' ),
                    'type'    => 'select',
                    'default' => 'bottomleft',
                    'options' => array(
                        'topleft'       =>__( 'Top Left','wc-sales-notification-pro' ),
                        'topright'      =>__( 'Top Right','wc-sales-notification-pro' ),
                        'bottomleft'    =>__( 'Bottom Left','wc-sales-notification-pro' ),
                        'bottomright'   =>__( 'Bottom Right','wc-sales-notification-pro' ),
                    ),
                ),

                array(
                    'name'    => 'notification_layout',
                    'label'   => __( 'Image Position', 'wc-sales-notification-pro' ),
                    'desc'    => __( 'Notification Layout.', 'wc-sales-notification-pro' ),
                    'type'    => 'select',
                    'default' => 'imageleft',
                    'options' => array(
                        'imageleft'       =>__( 'Image Left','wc-sales-notification-pro' ),
                        'imageright'      =>__( 'Image Right','wc-sales-notification-pro' ),
                    ),
                    'class'       => 'notification_real'
                ),

                array(
                    'name'    => 'notification_loadduration',
                    'label'   => __( 'Loading Time', 'wc-sales-notification-pro' ),
                    'desc'    => __( 'Notification Loading duration.', 'wc-sales-notification-pro' ),
                    'type'    => 'select',
                    'default' => '3',
                    'options' => array(
                        '2'       =>__( '2 seconds','wc-sales-notification-pro' ),
                        '3'       =>__( '3 seconds','wc-sales-notification-pro' ),
                        '4'       =>__( '4 seconds','wc-sales-notification-pro' ),
                        '5'       =>__( '5 seconds','wc-sales-notification-pro' ),
                        '6'       =>__( '6 seconds','wc-sales-notification-pro' ),
                        '7'       =>__( '7 seconds','wc-sales-notification-pro' ),
                        '8'       =>__( '8 seconds','wc-sales-notification-pro' ),
                        '9'       =>__( '9 seconds','wc-sales-notification-pro' ),
                        '10'       =>__( '10 seconds','wc-sales-notification-pro' ),
                        '20'       =>__( '20 seconds','wc-sales-notification-pro' ),
                        '30'       =>__( '30 seconds','wc-sales-notification-pro' ),
                        '40'       =>__( '40 seconds','wc-sales-notification-pro' ),
                        '50'       =>__( '50 seconds','wc-sales-notification-pro' ),
                        '60'       =>__( '1 minute','wc-sales-notification-pro' ),
                        '90'       =>__( '1.5 minutes','wc-sales-notification-pro' ),
                        '120'       =>__( '2 minutes','wc-sales-notification-pro' ),
                    ),
                ),

                array(
                    'name'    => 'notification_time_int',
                    'label'   => __( 'Time Interval', 'wc-sales-notification-pro' ),
                    'desc'    => __( 'Time between notifications.', 'wc-sales-notification-pro' ),
                    'type'    => 'select',
                    'default' => '4',
                    'options' => array(
                        '2'       =>__( '2 seconds','wc-sales-notification-pro' ),
                        '4'       =>__( '4 seconds','wc-sales-notification-pro' ),
                        '5'       =>__( '5 seconds','wc-sales-notification-pro' ),
                        '6'       =>__( '6 seconds','wc-sales-notification-pro' ),
                        '7'       =>__( '7 seconds','wc-sales-notification-pro' ),
                        '8'       =>__( '8 seconds','wc-sales-notification-pro' ),
                        '9'       =>__( '9 seconds','wc-sales-notification-pro' ),
                        '10'       =>__( '10 seconds','wc-sales-notification-pro' ),
                        '20'       =>__( '20 seconds','wc-sales-notification-pro' ),
                        '30'       =>__( '30 seconds','wc-sales-notification-pro' ),
                        '40'       =>__( '40 seconds','wc-sales-notification-pro' ),
                        '50'       =>__( '50 seconds','wc-sales-notification-pro' ),
                        '60'       =>__( '1 minute','wc-sales-notification-pro' ),
                        '90'       =>__( '1.5 minutes','wc-sales-notification-pro' ),
                        '120'       =>__( '2 minutes','wc-sales-notification-pro' ),
                    ),
                ),

                array(
                    'name'              => 'notification_limit',
                    'label'             => __( 'Limit', 'wc-sales-notification-pro' ),
                    'desc'              => __( 'Order Limit for notification.', 'wc-sales-notification-pro' ),
                    'min'               => 1,
                    'max'               => 100,
                    'default'           => '5',
                    'step'              => '1',
                    'type'              => 'number',
                    'sanitize_callback' => 'number',
                    'class'       => 'notification_real',
                ),

                array(
                    'name'    => 'notification_uptodate',
                    'label'   => __( 'Order Upto', 'wc-sales-notification-pro' ),
                    'desc'    => __( 'Do not show purchases older than.', 'wc-sales-notification-pro' ),
                    'type'    => 'select',
                    'default' => '7',
                    'options' => array(
                        '1'   =>__( '1 day','wc-sales-notification-pro' ),
                        '2'   =>__( '2 days','wc-sales-notification-pro' ),
                        '3'   =>__( '3 days','wc-sales-notification-pro' ),
                        '4'   =>__( '4 days','wc-sales-notification-pro' ),
                        '5'   =>__( '5 days','wc-sales-notification-pro' ),
                        '6'   =>__( '6 days','wc-sales-notification-pro' ),
                        '7'   =>__( '1 week','wc-sales-notification-pro' ),
                        '10'  =>__( '10 days','wc-sales-notification-pro' ),
                        '14'  =>__( '2 weeks','wc-sales-notification-pro' ),
                        '21'  =>__( '3 weeks','wc-sales-notification-pro' ),
                        '28'  =>__( '4 weeks','wc-sales-notification-pro' ),
                        '35'  =>__( '5 weeks','wc-sales-notification-pro' ),
                        '42'  =>__( '6 weeks','wc-sales-notification-pro' ),
                        '49'  =>__( '7 weeks','wc-sales-notification-pro' ),
                        '56'  =>__( '8 weeks','wc-sales-notification-pro' ),
                    ),
                    'class'       => 'notification_real',
                ),

                array(
                    'name'    => 'notification_inanimation',
                    'label'   => __( 'Animation In', 'wc-sales-notification-pro' ),
                    'desc'    => __( 'Notification Enter Animation.', 'wc-sales-notification-pro' ),
                    'type'    => 'select',
                    'default' => 'fadeInLeft',
                    'options' => array(
                        'bounce'   =>__( 'bounce','wc-sales-notification-pro' ),
                        'flash'   =>__( 'flash','wc-sales-notification-pro' ),
                        'pulse'   =>__( 'pulse','wc-sales-notification-pro' ),
                        'rubberBand'   =>__( 'rubberBand','wc-sales-notification-pro' ),
                        'shake'   =>__( 'shake','wc-sales-notification-pro' ),
                        'swing'   =>__( 'swing','wc-sales-notification-pro' ),
                        'tada'   =>__( 'tada','wc-sales-notification-pro' ),
                        'wobble'  =>__( 'wobble','wc-sales-notification-pro' ),
                        'jello'  =>__( 'jello','wc-sales-notification-pro' ),
                        'heartBeat'  =>__( 'heartBeat','wc-sales-notification-pro' ),
                        'bounceIn'  =>__( 'bounceIn','wc-sales-notification-pro' ),
                        'bounceInDown'  =>__( 'bounceInDown','wc-sales-notification-pro' ),
                        'bounceInLeft'  =>__( 'bounceInLeft','wc-sales-notification-pro' ),
                        'bounceInRight'  =>__( 'bounceInRight','wc-sales-notification-pro' ),
                        'bounceInUp'  =>__( 'bounceInUp','wc-sales-notification-pro' ),
                        'fadeIn'  =>__( 'fadeIn','wc-sales-notification-pro' ),
                        'fadeInDown'  =>__( 'fadeInDown','wc-sales-notification-pro' ),
                        'fadeInDownBig'  =>__( 'fadeInDownBig','wc-sales-notification-pro' ),
                        'fadeInLeft'  =>__( 'fadeInLeft','wc-sales-notification-pro' ),
                        'fadeInLeftBig'  =>__( 'fadeInLeftBig','wc-sales-notification-pro' ),
                        'fadeInRight'  =>__( 'fadeInRight','wc-sales-notification-pro' ),
                        'fadeInRightBig'  =>__( 'fadeInRightBig','wc-sales-notification-pro' ),
                        'fadeInUp'  =>__( 'fadeInUp','wc-sales-notification-pro' ),
                        'fadeInUpBig'  =>__( 'fadeInUpBig','wc-sales-notification-pro' ),
                        'flip'  =>__( 'flip','wc-sales-notification-pro' ),
                        'flipInX'  =>__( 'flipInX','wc-sales-notification-pro' ),
                        'flipInY'  =>__( 'flipInY','wc-sales-notification-pro' ),
                        'lightSpeedIn'  =>__( 'lightSpeedIn','wc-sales-notification-pro' ),
                        'rotateIn'  =>__( 'rotateIn','wc-sales-notification-pro' ),
                        'rotateInDownLeft'  =>__( 'rotateInDownLeft','wc-sales-notification-pro' ),
                        'rotateInDownRight'  =>__( 'rotateInDownRight','wc-sales-notification-pro' ),
                        'rotateInUpLeft'  =>__( 'rotateInUpLeft','wc-sales-notification-pro' ),
                        'rotateInUpRight'  =>__( 'rotateInUpRight','wc-sales-notification-pro' ),
                        'slideInUp'  =>__( 'slideInUp','wc-sales-notification-pro' ),
                        'slideInDown'  =>__( 'slideInDown','wc-sales-notification-pro' ),
                        'slideInLeft'  =>__( 'slideInLeft','wc-sales-notification-pro' ),
                        'slideInRight'  =>__( 'slideInRight','wc-sales-notification-pro' ),
                        'zoomIn'  =>__( 'zoomIn','wc-sales-notification-pro' ),
                        'zoomInDown'  =>__( 'zoomInDown','wc-sales-notification-pro' ),
                        'zoomInLeft'  =>__( 'zoomInLeft','wc-sales-notification-pro' ),
                        'zoomInRight'  =>__( 'zoomInRight','wc-sales-notification-pro' ),
                        'zoomInUp'  =>__( 'zoomInUp','wc-sales-notification-pro' ),
                        'hinge'  =>__( 'hinge','wc-sales-notification-pro' ),
                        'jackInTheBox'  =>__( 'jackInTheBox','wc-sales-notification-pro' ),
                        'rollIn'  =>__( 'rollIn','wc-sales-notification-pro' ),
                        'rollOut'  =>__( 'rollOut','wc-sales-notification-pro' ),
                    ),
                ),

                array(
                    'name'    => 'notification_outanimation',
                    'label'   => __( 'Animation Out', 'wc-sales-notification-pro' ),
                    'desc'    => __( 'Notification Out Animation.', 'wc-sales-notification-pro' ),
                    'type'    => 'select',
                    'default' => 'fadeOutRight',
                    'options' => array(
                        'bounce'   =>__( 'bounce','wc-sales-notification-pro' ),
                        'flash'   =>__( 'flash','wc-sales-notification-pro' ),
                        'pulse'   =>__( 'pulse','wc-sales-notification-pro' ),
                        'rubberBand'   =>__( 'rubberBand','wc-sales-notification-pro' ),
                        'shake'   =>__( 'shake','wc-sales-notification-pro' ),
                        'swing'   =>__( 'swing','wc-sales-notification-pro' ),
                        'tada'   =>__( 'tada','wc-sales-notification-pro' ),
                        'wobble'  =>__( 'wobble','wc-sales-notification-pro' ),
                        'jello'  =>__( 'jello','wc-sales-notification-pro' ),
                        'heartBeat'  =>__( 'heartBeat','wc-sales-notification-pro' ),
                        'bounceOut'  =>__( 'bounceOut','wc-sales-notification-pro' ),
                        'bounceOutDown'  =>__( 'bounceOutDown','wc-sales-notification-pro' ),
                        'bounceOutLeft'  =>__( 'bounceOutLeft','wc-sales-notification-pro' ),
                        'bounceOutRight'  =>__( 'bounceOutRight','wc-sales-notification-pro' ),
                        'bounceOutUp'  =>__( 'bounceOutUp','wc-sales-notification-pro' ),
                        'fadeOut'  =>__( 'fadeOut','wc-sales-notification-pro' ),
                        'fadeOutDown'  =>__( 'fadeOutDown','wc-sales-notification-pro' ),
                        'fadeOutDownBig'  =>__( 'fadeOutDownBig','wc-sales-notification-pro' ),
                        'fadeOutLeft'  =>__( 'fadeOutLeft','wc-sales-notification-pro' ),
                        'fadeOutLeftBig'  =>__( 'fadeOutLeftBig','wc-sales-notification-pro' ),
                        'fadeOutRight'  =>__( 'fadeOutRight','wc-sales-notification-pro' ),
                        'fadeOutRightBig'  =>__( 'fadeOutRightBig','wc-sales-notification-pro' ),
                        'fadeOutUp'  =>__( 'fadeOutUp','wc-sales-notification-pro' ),
                        'fadeOutUpBig'  =>__( 'fadeOutUpBig','wc-sales-notification-pro' ),
                        'flip'  =>__( 'flip','wc-sales-notification-pro' ),
                        'flipOutX'  =>__( 'flipOutX','wc-sales-notification-pro' ),
                        'flipOutY'  =>__( 'flipOutY','wc-sales-notification-pro' ),
                        'lightSpeedOut'  =>__( 'lightSpeedOut','wc-sales-notification-pro' ),
                        'rotateOut'  =>__( 'rotateOut','wc-sales-notification-pro' ),
                        'rotateOutDownLeft'  =>__( 'rotateOutDownLeft','wc-sales-notification-pro' ),
                        'rotateOutDownRight'  =>__( 'rotateOutDownRight','wc-sales-notification-pro' ),
                        'rotateOutUpLeft'  =>__( 'rotateOutUpLeft','wc-sales-notification-pro' ),
                        'rotateOutUpRight'  =>__( 'rotateOutUpRight','wc-sales-notification-pro' ),
                        'slideOutUp'  =>__( 'slideOutUp','wc-sales-notification-pro' ),
                        'slideOutDown'  =>__( 'slideOutDown','wc-sales-notification-pro' ),
                        'slideOutLeft'  =>__( 'slideOutLeft','wc-sales-notification-pro' ),
                        'slideOutRight'  =>__( 'slideOutRight','wc-sales-notification-pro' ),
                        'zoomOut'  =>__( 'zoomOut','wc-sales-notification-pro' ),
                        'zoomOutDown'  =>__( 'zoomOutDown','wc-sales-notification-pro' ),
                        'zoomOutLeft'  =>__( 'zoomOutLeft','wc-sales-notification-pro' ),
                        'zoomOutRight'  =>__( 'zoomOutRight','wc-sales-notification-pro' ),
                        'zoomOutUp'  =>__( 'zoomOutUp','wc-sales-notification-pro' ),
                        'hinge'  =>__( 'hinge','wc-sales-notification-pro' ),
                    ),
                ),
                
                array(
                    'name'  => 'background_color',
                    'label' => __( 'Background Color', 'wc-sales-notification-pro' ),
                    'desc' => wp_kses_post( 'Notification Background Color.', 'wc-sales-notification-pro' ),
                    'type' => 'color',
                ),

                array(
                    'name'  => 'heading_color',
                    'label' => __( 'Heading Color', 'wc-sales-notification-pro' ),
                    'desc' => wp_kses_post( 'Notification Heading Color.', 'wc-sales-notification-pro' ),
                    'type' => 'color',
                ),

                array(
                    'name'  => 'content_color',
                    'label' => __( 'Content Color', 'wc-sales-notification-pro' ),
                    'desc' => wp_kses_post( 'Notification Content Color.', 'wc-sales-notification-pro' ),
                    'type' => 'color',
                ),

                array(
                    'name'  => 'cross_color',
                    'label' => __( 'Cross Icon Color', 'wc-sales-notification-pro' ),
                    'desc' => wp_kses_post( 'Notification Cross Icon Color.', 'wc-sales-notification-pro' ),
                    'type' => 'color'
                ),

            ),

            'wcsales_fakes_data_tabs' => array(),
            'wcsales_plugins_tabs' => array(),

        );
        
        return array_merge( $settings_fields );
    }

    // Admin Menu Page Render
    function plugin_page() {

        echo '<div class="wrap">';
            echo '<h2>'.esc_html__( 'WC Sales Notification Settings','wc-sales-notification-pro' ).'</h2>';
            $this->save_message();
            $this->settings_api->show_navigation();
            $this->settings_api->show_forms();
        echo '</div>';

    }

    // Save Options Message
    function save_message() {
        if( isset($_GET['settings-updated']) ) { ?>
            <div class="updated notice is-dismissible"> 
                <p><strong><?php esc_html_e('Successfully Settings Saved.', 'wc-sales-notification-pro') ?></strong></p>
            </div>
            <?php
        }
    }

    function html_fake_data_tabs(){
        ob_start();

        $fakadata = array();
        $fake_title = get_option( 'fake_title' );
        $fake_price = get_option( 'fake_price' );
        $fake_buyer = get_option( 'fake_buyer' );
        $fake_desc  = get_option( 'fake_description' );
        $fake_image  = get_option( 'fake_image' );
        $count      = count( $fake_title );

        for ( $i = 0; $i < $count; $i++ ) {
            if ( $fake_title[$i] != '' ){
                $fakadata[$i]['fake_title'] = $fake_title[$i];
                $fakadata[$i]['fake_price'] = $fake_price[$i];
                $fakadata[$i]['fake_buyer'] = $fake_buyer[$i];
                $fakadata[$i]['fake_description'] = $fake_desc[$i];
                $fakadata[$i]['fake_image'] = $fake_image[$i];
            }
        }

        ?>
            <table id="htrepeatable-fieldset" class="htopt_meta_box_table" width="100%">
                <thead>
                    <tr>
                        <th><?php echo esc_html__( "Title", 'wc-sales-notification-pro' );?></th>
                        <th><?php echo esc_html__( "Price", 'wc-sales-notification-pro' );?></th>
                        <th><?php echo esc_html__( "Buyer", 'wc-sales-notification-pro' );?></th>
                        <th><?php echo esc_html__( "Description", 'wc-sales-notification-pro' );?></th>
                        <th><?php echo esc_html__( "Image", 'wc-sales-notification-pro' );?></th>
                        <th><?php echo esc_html__( "Action", 'wc-sales-notification-pro' );?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    if ( $fakadata ) :
                        foreach ( $fakadata as $datainfo ) {
                            ?>
                            <tr>
                                <td>
                                    <input type="text" placeholder="<?php echo esc_attr__( 'Enter Title', 'wc-sales-notification-pro' ); ?>" value="<?php echo $datainfo['fake_title'] ? $datainfo['fake_title'] : ''; ?>" name="fake_title[]" />
                                </td>
                                <td>
                                    <input type="text" placeholder="<?php echo esc_attr__( 'Enter Price', 'wc-sales-notification-pro' ); ?>" value="<?php echo $datainfo['fake_price'] ? $datainfo['fake_price'] : ''; ?>" name="fake_price[]" />
                                </td>
                                <td>
                                    <input type="text" placeholder="<?php echo esc_attr__( 'Enter Buyer Name', 'wc-sales-notification-pro' ); ?>" value="<?php echo $datainfo['fake_buyer'] ? $datainfo['fake_buyer'] : ''; ?>" name="fake_buyer[]" />
                                </td>
                                <td>
                                    <textarea name="fake_description[]" placeholder="<?php echo esc_attr__( 'Enter Description', 'wc-sales-notification-pro' ); ?>"><?php echo $datainfo['fake_description'] ? $datainfo['fake_description'] : ''; ?></textarea>
                                </td>
                                <td>
                                    <div class="htmedia_display">
                                        <?php
                                            if( !empty( $datainfo['fake_image'] ) ){
                                                echo '<img src="'.esc_url( $datainfo['fake_image'] ).'" alt="'.esc_attr( $datainfo['fake_title'] ).'">';
                                            }else{
                                                echo '<img src="'.WCPRO_SALENOTIFICATION_PL_URL.'/admin/assets/images/fake_data_placeholder.png" alt="'.esc_attr( $datainfo['fake_title'] ).'">';
                                            }
                                        ?>
                                    </div>
                                    <input type="hidden" class="wpsa-url" name="fake_image[]" value="<?php echo $datainfo['fake_image'] ? $datainfo['fake_image'] : ''; ?>" />
                                    <input type="button" class="button wpsa-browse" value="<?php echo esc_attr__( 'Upload Image', 'wc-sales-notification-pro' ); ?>" />
                                    <input type="button" class="button wpsa-remove" value="<?php echo esc_attr__( 'Remove Image', 'wc-sales-notification-pro' ); ?>" />
                                </td>
                                <td width="10%"><a class="button remove-row" href="#1"><?php esc_html_e( 'Remove', 'wc-sales-notification-pro' ); ?></a></td>
                            </tr>
                            <?php
                        }

                    else :
                    // show a blank one
                ?>
                    <tr>
                        <td>
                            <input type="text" placeholder="<?php echo esc_attr__( 'Enter Title', 'wc-sales-notification-pro' ); ?>" name="fake_title[]" />
                        </td>
                        <td>
                            <input type="text" placeholder="<?php echo esc_attr__( 'Enter Price', 'wc-sales-notification-pro' ); ?>" name="fake_price[]" />
                        </td>
                        <td>
                            <input type="text" placeholder="<?php echo esc_attr__( 'Enter Buyer Name', 'wc-sales-notification-pro' ); ?>" name="fake_buyer[]" />
                        </td>
                        <td>
                            <textarea name="fake_description[]" placeholder="<?php echo esc_attr__( 'Enter Description', 'wc-sales-notification-pro' ); ?>"></textarea>
                        </td>
                        <td>
                            <div class="htmedia_display"></div>
                            <input type="hidden" class="wpsa-url" name="fake_image[]" />
                            <input type="button" class="button wpsa-browse" value="<?php echo esc_attr__( 'Upload Image', 'wc-sales-notification-pro' ); ?>" />
                        </td>
                        <td>
                            <a class="button remove-row button-disabled" href="#">
                                <?php esc_html_e( 'Remove', 'wc-sales-notification-pro' ); ?>
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
                    <!-- empty hidden one for jQuery -->
                    <tr class="empty-row screen-reader-text">
                        <td>
                            <input type="text" placeholder="<?php echo esc_attr__( 'Enter Title', 'wc-sales-notification-pro' ); ?>" name="fake_title[]" />
                        </td>
                        <td>
                            <input type="text" placeholder="<?php echo esc_attr__( 'Enter Price', 'wc-sales-notification-pro' ); ?>" name="fake_price[]" />
                        </td>
                        <td>
                            <input type="text" placeholder="<?php echo esc_attr__( 'Enter Buyer Name', 'wc-sales-notification-pro' ); ?>" name="fake_buyer[]" />
                        </td>
                        <td>
                            <textarea name="fake_description[]" placeholder="<?php echo esc_attr__( 'Enter Description', 'wc-sales-notification-pro' ); ?>"></textarea>
                        </td>
                        <td>
                            <div class="htmedia_display"></div>
                            <input type="hidden" class="wpsa-url" name="fake_image[]" />
                            <input type="button" class="button wpsa-browse" value="<?php echo esc_attr__( 'Upload Image', 'wc-sales-notification-pro' ); ?>" />
                        </td>
                        <td><a class="button remove-row" href="#"><?php esc_html_e( 'Remove', 'wc-sales-notification-pro' ); ?></a></td>
                    </tr>

                </tbody>
            </table>
            <p style="text-align:right;"><a id="add-row" class="button" href="#"><?php esc_html_e( 'Add Another', 'wc-sales-notification-pro' ); ?></a></p>

        <?php
        echo ob_get_clean();
    }

    // Setting Fileds Register
    function register_setting_fileds() {
        register_setting( 'wcsales_fakes_data_tabs', 'fake_title' );
        register_setting( 'wcsales_fakes_data_tabs', 'fake_price' );
        register_setting( 'wcsales_fakes_data_tabs', 'fake_buyer' );
        register_setting( 'wcsales_fakes_data_tabs', 'fake_description' );
        register_setting( 'wcsales_fakes_data_tabs', 'fake_image' );
    }

    // General tab
    function html_general_tabs(){
        ob_start();
        ?>
            <div class="wcsales-general-tabs">

                <div class="wcsales-document-section">
                    <div class="wcsales-column">
                        <a href="https://hasthemes.com/" target="_blank">
                            <img src="<?php echo WCPRO_SALENOTIFICATION_PL_URL; ?>/admin/assets/images/video-tutorial.jpg" alt="<?php esc_attr_e( 'Video Tutorial', 'wc-sales-notification-pro' ); ?>">
                        </a>
                    </div>
                    <div class="wcsales-column">
                        <a href="https://hasthemes.com/" target="_blank">
                            <img src="<?php echo WCPRO_SALENOTIFICATION_PL_URL; ?>/admin/assets/images/online-documentation.jpg" alt="<?php esc_attr_e( 'Online Documentation', 'wc-sales-notification-pro' ); ?>">
                        </a>
                    </div>
                    <div class="wcsales-column">
                        <a href="https://hasthemes.com/contact-us/" target="_blank">
                            <img src="<?php echo WCPRO_SALENOTIFICATION_PL_URL; ?>/admin/assets/images/contact-us.jpg" alt="<?php esc_attr_e( 'Contact Us', 'wc-sales-notification-pro' ); ?>">
                        </a>
                    </div>
                </div>

            </div>
        <?php
        echo ob_get_clean();
    }

    // Plugins Library
    function html_our_plugins_library_tabs() {
        ob_start();
        ?>
        <div class="wcsales-plugins-laibrary">
            <div class="wcsales-plugins-area">
                <h3><?php esc_html_e( 'Premium Plugins', 'wc-sales-notification-pro' ); ?></h3>
                <div class="htoptions-plugins-row">
                    
                    <div class="htoptions-single-plugins"><img src="<?php echo WCPRO_SALENOTIFICATION_PL_URL; ?>/admin/assets/images/preview_woolentor-pro.jpg" alt="">
                        <div class="htoptions-plugins-content">
                            <a href="https://hasthemes.com/plugins/woolentor-pro-woocommerce-page-builder/" target="_blank">
                                <h3><?php echo esc_html__( 'WooLentor - WooCommerce Page Builder and WooCommerce Elementor Addon', 'wc-sales-notification-pro' ); ?></h3>
                            </a>
                            <a href="https://woolentor.com/" class="htoptions-button" target="_blank"><?php echo esc_html__( 'Preview', 'wc-sales-notification-pro' ); ?></a>
                            <a href="https://hasthemes.com/plugins/woolentor-pro-woocommerce-page-builder/" class="htoptions-button" target="_blank"><?php echo esc_html__( 'Buy Now', 'wc-sales-notification-pro' ); ?></a>
                        </div>
                    </div>
                    
                    <div class="htoptions-single-plugins"><img src="<?php echo WCPRO_SALENOTIFICATION_PL_URL; ?>/admin/assets/images/htmega_preview.jpg" alt="">
                        <div class="htoptions-plugins-content">
                            <a href="https://hasthemes.com/plugins/ht-mega-pro/" target="_blank">
                                <h3><?php echo esc_html__( 'HT Mega – Absolute Addons for Elementor Page Builder', 'wc-sales-notification-pro' ); ?></h3>
                            </a>
                            <a href="http://demo.wphash.com/htmega/" class="htoptions-button" target="_blank"><?php echo esc_html__( 'Preview', 'wc-sales-notification-pro' ); ?></a>
                            <a href="https://hasthemes.com/plugins/ht-mega-pro/" class="htoptions-button" target="_blank"><?php echo esc_html__( 'Buy Now', 'wc-sales-notification-pro' ); ?></a>
                        </div>
                    </div>
                    
                    <div class="htoptions-single-plugins"><img src="<?php echo WCPRO_SALENOTIFICATION_PL_URL; ?>/admin/assets/images/hasbarpro-preview.jpg" alt="">
                        <div class="htoptions-plugins-content">
                            <a href="https://hasthemes.com/wordpress-notification-bar-plugin/" target="_blank">
                                <h3><?php echo esc_html__( 'HashBar Pro - WordPress Notification Bar plugin', 'wc-sales-notification-pro' ); ?></h3>
                            </a>
                            <a href="http://demo.wphash.com/hashbar/" class="htoptions-button" target="_blank"><?php echo esc_html__( 'Preview', 'wc-sales-notification-pro' ); ?></a>
                            <a href="https://hasthemes.com/wordpress-notification-bar-plugin/" class="htoptions-button" target="_blank"><?php echo esc_html__( 'Buy Now', 'wc-sales-notification-pro' ); ?></a>
                        </div>
                    </div>

                    <div class="htoptions-single-plugins"><img src="<?php echo WCPRO_SALENOTIFICATION_PL_URL; ?>/admin/assets/images/htbuilder_preview.jpg" alt="">
                        <div class="htoptions-plugins-content">
                            <a href="https://hasthemes.com/plugins/ht-builder-wordpress-theme-builder-for-elementor/" target="_blank">
                                <h3><?php echo esc_html__( 'HT Builder - WordPress Theme Builder for Elementor', 'wc-sales-notification-pro' ); ?></h3>
                            </a>
                            <a href="https://hasthemes.com/plugins/ht-builder-wordpress-theme-builder-for-elementor/" class="htoptions-button" target="_blank"><?php echo esc_html__( 'Buy Now', 'wc-sales-notification-pro' ); ?></a>
                        </div>
                    </div>
                    
                    <div class="htoptions-single-plugins"><img src="<?php echo WCPRO_SALENOTIFICATION_PL_URL; ?>/admin/assets/images/htscript-preview.png" alt="">
                        <div class="htoptions-plugins-content">
                            <a href="https://hasthemes.com/plugins/insert-headers-and-footers-code-ht-script/" target="_blank">
                                <h3><?php echo esc_html__( 'HT Script Pro - Insert Header & Footer Code', 'wc-sales-notification-pro' ); ?></h3>
                            </a>
                            <a href="https://hasthemes.com/plugins/insert-headers-and-footers-code-ht-script/" class="htoptions-button" target="_blank"><?php echo esc_html__( 'Buy Now', 'wc-sales-notification-pro' ); ?></a>
                        </div>
                    </div>
                    
                    <div class="htoptions-single-plugins"><img src="<?php echo WCPRO_SALENOTIFICATION_PL_URL; ?>/admin/assets/images/wc-builder_pro.jpg" alt="">
                        <div class="htoptions-plugins-content">
                            <a href="https://hasthemes.com/plugins/wc-builder-woocoomerce-page-builder-for-wpbakery/" target="_blank">
                                <h3><?php echo esc_html__( 'WC Builder - WooCommerce Page Builder for WP Bakery', 'wc-sales-notification-pro' ); ?></h3>
                            </a>
                            <a href="https://hasthemes.com/plugins/wc-builder-woocoomerce-page-builder-for-wpbakery/" class="htoptions-button" target="_blank"><?php echo esc_html__( 'Buy Now', 'wc-sales-notification-pro' ); ?></a>
                        </div>
                    </div>

                </div>

                <h3><?php esc_html_e( 'Free Plugins', 'wc-sales-notification-pro' ); ?></h3>
                <div class="htoptions-plugins-row">
                    <?php wcsales_get_org_plugins_pro();?>
                </div>

            </div>
        </div>
        <?php
        echo ob_get_clean();
    }
    

}

new WC_Pro_Sale_Notification_Admin_Settings();