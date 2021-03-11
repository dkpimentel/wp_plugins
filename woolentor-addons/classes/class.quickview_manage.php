<?php

namespace WooLentor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
*  Quickview Manager
*/
class Quick_View_Manager{

    private static $instance = null;
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    function __construct(){

        add_action( 'woolentor_footer_render_content', [ $this, 'quick_view_html' ], 10 );

        // Open Quickview
        add_action( 'wp_ajax_woolentor_quickview', [ $this, 'wc_quickview' ] );
        add_action( 'wp_ajax_nopriv_woolentor_quickview', [ $this, 'wc_quickview' ] );

        // Ajax Add to Cart Callback
        add_action( 'wp_ajax_quickview_ajax_add_to_cart', [ $this, 'ajax_add_to_cart' ] );
        add_action( 'wp_ajax_nopriv_quickview_ajax_add_to_cart', [ $this, 'ajax_add_to_cart' ] );

    }


    // Quick View Markup
    public function quick_view_html(){
        echo '<div class="woocommerce" id="htwlquick-viewmodal"><div class="htwl-modal-dialog product"><div class="htwl-modal-content"><button type="button" class="htcloseqv"><span class="sli sli-close"></span></button><div class="htwl-modal-body"></div></div></div></div>';
    }

    // Add to cart Ajax callback function
    public function ajax_add_to_cart() {
        $product_id         = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
        $quantity           = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );
        $variation_id       = absint( $_POST['variation_id'] );
        $passed_validation  = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
        $product_status     = get_post_status( $product_id );

        if ( $passed_validation && \WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ) && 'publish' === $product_status ) {
            do_action( 'woocommerce_ajax_added_to_cart', $product_id );
            if ( 'yes' === get_option('woocommerce_cart_redirect_after_add') ) {
                wc_add_to_cart_message( array( $product_id => $quantity ), true );
            }
            \WC_AJAX::get_refreshed_fragments();
        } else {
            $data = array(
                'error' => true,
                'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
            );
            echo wp_send_json($data);
        }
        wp_die();
    }

    // Open Quick view Ajax Callback
    function wc_quickview() {
        if ( isset( $_POST['id'] ) && (int) $_POST['id'] ) {
            global $post, $product, $woocommerce;
            $id      = ( int ) $_POST['id'];
            $post    = get_post( $id );
            $product = get_product( $id );
            if ( $product ) { 
                include ( apply_filters( 'woolentor_quickview_tmp', WOOLENTOR_ADDONS_PL_PATH.'includes/quickview-content.php' ) ); 
            }
        }
        wp_die();
    }


}

Quick_View_Manager::instance();