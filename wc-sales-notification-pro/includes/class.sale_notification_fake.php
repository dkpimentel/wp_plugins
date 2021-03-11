<?php
/**
* Class Sale Notification
*/
class WC_Pro_Sales_Notification{

    private static $_instance = null;
    public static function instance(){
        if( is_null( self::$_instance ) ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    function __construct(){

        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'inline_styles' ] );
        add_action( 'wp_footer', [ $this, 'ajax_request' ] );
        
    }

    public function enqueue_scripts(){
        wp_enqueue_script( 'wcsales-mainjs', WCPRO_SALENOTIFICATION_ASSETS . 'js/main.js', array(), null, true );
        wp_localize_script( 'wcsales-mainjs', 'notification_fake_data', $this->fakes_notification_data() );
    }

    public function fakes_notification_data(){
        $fakadata = array();
        $fake_title = get_option( 'fake_title' );
        $fake_price = get_option( 'fake_price' );
        $fake_buyer = get_option( 'fake_buyer' );
        $fake_desc  = get_option( 'fake_description' );
        $fake_image  = get_option( 'fake_image' );
        $count      = count( $fake_title );
        for ( $i = 0; $i < $count; $i++ ) {
            if ( $fake_title[$i] != '' ){
                $fakadata[$i]['fake_title'] = !empty( $fake_title[$i] ) ? $fake_title[$i] : '';
                $fakadata[$i]['fake_price'] = !empty( $fake_price[$i] ) ? $fake_price[$i] : '';
                $fakadata[$i]['fake_buyer'] = !empty( $fake_buyer[$i] ) ? $fake_buyer[$i] : '';
                $fakadata[$i]['fake_description'] = !empty( $fake_desc[$i] ) ? $fake_desc[$i] : '';
                $fakadata[$i]['fake_image'] = !empty( $fake_image[$i] ) ? $fake_image[$i] : WCPRO_SALENOTIFICATION_PL_URL.'/admin/assets/images/fake_data_placeholder.png';
            }
        }
        return $fakadata;
    }

    // Inline CSS
    function inline_styles() {
        $bgcolor = wcsales_get_option_pro( 'background_color','wcsales_settings_tabs', '#ffffff' );
        $headingcolor = wcsales_get_option_pro( 'heading_color','wcsales_settings_tabs', '#000000' );
        $contentcolor = wcsales_get_option_pro( 'content_color','wcsales_settings_tabs', '#7e7e7e' );
        $crosscolor = wcsales_get_option_pro( 'cross_color','wcsales_settings_tabs', '#000000' );
        $custom_css = "
            .wcsales-notification-content{
                background: {$bgcolor} !important;
            }
            .wcnotification_content h4,.wcnotification_content h6{
                color: {$headingcolor} !important;
            }
            .wcnotification_content p,.wcsales-buyername{
                color: {$contentcolor} !important;
            }
            .wccross{
                color: {$crosscolor} !important;
            }";
        wp_add_inline_style( 'wcsales-main', $custom_css );
    }

    // Ajax request
    function ajax_request() {
        
        $intervaltime  = (int)wcsales_get_option_pro( 'notification_time_int','wcsales_settings_tabs', '4' )*1000;
        $duration      = (int)wcsales_get_option_pro( 'notification_loadduration','wcsales_settings_tabs', '3' )*1000;
        $inanimation   = wcsales_get_option_pro( 'notification_inanimation','wcsales_settings_tabs', 'fadeInLeft' );
        $outanimation  = wcsales_get_option_pro( 'notification_outanimation','wcsales_settings_tabs', 'fadeOutRight' );
        $notposition  = wcsales_get_option_pro( 'notification_pos','wcsales_settings_tabs', 'bottomleft' );
        $notlayout  = wcsales_get_option_pro( 'notification_layout','wcsales_settings_tabs', 'imageleft' );

        //Set Your Nonce
        $ajax_nonce = wp_create_nonce( "wcsales-ajax-request" );
        ?>
            <script>
                jQuery( document ).ready( function( $ ) {

                    var notposition = '<?php echo $notposition; ?>',
                        notlayout = ' '+'<?php echo $notlayout; ?>';

                    $('body').append('<div class="wcsales-sale-notification"><div class="wcsales-notification-content '+notposition+notlayout+'"></div></div>');

                    var intervaltime = <?php echo $intervaltime; ?>,
                        i = 0,
                        duration = <?php echo $duration; ?>,
                        inanimation = '<?php echo $inanimation; ?>',
                        outanimation = '<?php echo $outanimation; ?>';

                    window.setTimeout( function(){
                        setInterval(function() {
                            if( notification_fake_data.length > 0 ){
                                if( i == notification_fake_data.length ){ i = 0; }

                                var title = ( notification_fake_data[i].fake_title.length !== '' ) ? '<h4>'+notification_fake_data[i].fake_title+'</h4>' : '',
                                    description = ( notification_fake_data[i].fake_description.length !== '' ) ? '<p>'+notification_fake_data[i].fake_description+'</p>' : '',
                                    price = ( notification_fake_data[i].fake_price.length !== '' ) ? '<h6>'+notification_fake_data[i].fake_price+'</h6>' : '',
                                    buyer = ( notification_fake_data[i].fake_buyer.length !== '' ) ? '<span class="wcsales-buyername">'+notification_fake_data[i].fake_buyer+'</span>' : '';

                                $('.wcsales-notification-content').html('');
                                $('.wcsales-notification-content').css('padding','15px');
                                var ordercontent = `<div class="wcnotification_image"><img src="${notification_fake_data[i].fake_image}" alt="${notification_fake_data[i].fake_title}" /></div>
                                    <div class="wcnotification_content">${title + description + price + buyer}</div>
                                    <span class="wccross">&times;</span>`;
                                $('.wcsales-notification-content').append( ordercontent ).addClass('animated '+inanimation ).removeClass(outanimation);
                                setTimeout(function() {
                                    $('.wcsales-notification-content').removeClass(inanimation).addClass(outanimation);
                                }, intervaltime-500 );
                                i++;
                            }
                        }, intervaltime );

                    }, duration );

                    // Close Button
                    $('.wcsales-notification-content').on('click', '.wccross', function(e){
                        e.preventDefault()
                        $(this).closest('.wcsales-notification-content').removeClass(inanimation).addClass(outanimation);
                    });

                });
            </script>
        <?php 
    }



}

WC_Pro_Sales_Notification::instance();