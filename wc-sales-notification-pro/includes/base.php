<?php

namespace WC_Sale_Notification_Pro;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Base {

    const MINIMUM_PHP_VERSION = '5.4';

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action( 'init', [ $this, 'i18n' ] );
        add_action( 'plugins_loaded', [ $this, 'init' ] );

        // After Active Plugin then redirect setting page
        register_activation_hook( WCPRO_SALENOTIFICATION_PL_ROOT, [ $this, 'plugin_activate_hook' ] );
        add_action('admin_init', [ $this, 'plugin_redirect_option_page' ] );

        // Init Hook
        add_action( 'init', [ $this, 'includes_files_under_hooks' ] );

        // Frontend Scripts
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ] );

    }

    /*
    * Load Text Domain
    */
    public function i18n() {
        load_plugin_textdomain( 'wc-sales-notification-pro' );
    }

    /*
    * Init Hook in Init
    */
    public function init() {

        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
            return;
        }

        // Include File
        $this->include_files();
        $this->wcsales_deactivate_lite();

        // Plugins Setting Page
        add_filter('plugin_action_links_'.WCPRO_SALENOTIFICATION_PLUGIN_BASE, [ $this, 'plugins_setting_links' ] );

    }

    /*
    * Deactive Free Version
    */
    public function wcsales_deactivate_lite() {
        if( is_plugin_active('wc-sales-notification/wc_sales_notification.php') ) {
            deactivate_plugins( '/wc-sales-notification/wc_sales_notification.php' );
        }
    }

    /*
    * Include File
    */
    public function include_files(){
        if ( ! function_exists('is_plugin_active') ){ include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); }
        include( WCPRO_SALENOTIFICATION_PL_PATH . 'admin/admin-setting.php');
        include( WCPRO_SALENOTIFICATION_PL_PATH . 'includes/helper_function.php');
    }

    /*
    * Include File Under Init Hook
    */
    public function includes_files_under_hooks(){
        if( wcsales_get_option_pro( 'enableresalenotification', 'wcsales_settings_tabs', 'off' ) == 'on' ){
            if( wcsales_get_option_pro( 'notification_content_type', 'wcsales_settings_tabs', 'actual' ) == 'fakes' ){
                include( WCPRO_SALENOTIFICATION_PL_PATH. 'includes/class.sale_notification_fake.php' );
            }else{
                include( WCPRO_SALENOTIFICATION_PL_PATH . 'includes/class.sale_notification.php' );
            }
        }

    }

    /**
     * Admin notice.
     * PHP required version.
     */
    public function admin_notice_minimum_php_version() {
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'wc-sales-notification-pro' ),
            '<strong>' . esc_html__( 'WC Sales Notification', 'wc-sales-notification-pro' ) . '</strong>',
            '<strong>' . esc_html__( 'PHP', 'wc-sales-notification-pro' ) . '</strong>',
             self::MINIMUM_PHP_VERSION
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    /*
    * Check Plugins is Installed or not
    */
    public function is_plugins_active( $pl_file_path = NULL ){
        $installed_plugins_list = get_plugins();
        return isset( $installed_plugins_list[$pl_file_path] );
    }

    /* 
    * Add settings link on plugin page.
    */
    public function plugins_setting_links( $links ) {
        $htbuilder_settings_link = '<a href="'.admin_url('admin.php?page=wcsalenotification').'">'.__( 'Settings', 'wc-sales-notification-pro' ).'</a>';
        array_unshift( $links, $htbuilder_settings_link );
        return $links; 
    }

    /* 
    * Plugins After Install
    * Redirect Setting page
    */
    public function plugin_activate_hook() {
        add_option('wc_sales_notification_do_activation_redirect', true);
    }
    public function plugin_redirect_option_page() {
        if ( get_option( 'wc_sales_notification_do_activation_redirect', false ) ) {
            delete_option('wc_sales_notification_do_activation_redirect');
            if( !isset( $_GET['activate-multi'] ) ){
                wp_redirect( admin_url('admin.php?page=wcsalenotification') );
            }
        }
    }

    /**
     * Enqueue frontend scripts
     */
    public function enqueue_frontend_scripts() {

        // CSS
        wp_enqueue_style(
            'wcsales-main',
            WCPRO_SALENOTIFICATION_ASSETS . 'css/wc_notification.css',
            NULL,
            WCPRO_SALENOTIFICATION_VERSION
        );
        wp_enqueue_style(
            'wcsales-animate',
            WCPRO_SALENOTIFICATION_ASSETS . 'css/animate.css',
            NULL,
            WCPRO_SALENOTIFICATION_VERSION
        );

    }


}