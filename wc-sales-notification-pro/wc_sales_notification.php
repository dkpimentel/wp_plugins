<?php
/**
 * Plugin Name: WC Sales Notification Pro
 * Description: WooCommerce Sales Notification for WordPress.
 * Plugin URI:  https://hasthemes.com/plugins/
 * Version:     1.0.0
 * Author:      HasThemes
 * Author URI:  https://hasthemes.com/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wc-sales-notification-pro
 * Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'WCPRO_SALENOTIFICATION_VERSION', '1.0.0' );
define( 'WCPRO_SALENOTIFICATION_PL_ROOT', __FILE__ );
define( 'WCPRO_SALENOTIFICATION_PL_URL', plugin_dir_url(  WCPRO_SALENOTIFICATION_PL_ROOT ) );
define( 'WCPRO_SALENOTIFICATION_PL_PATH', plugin_dir_path( WCPRO_SALENOTIFICATION_PL_ROOT ) );
define( 'WCPRO_SALENOTIFICATION_PLUGIN_BASE', plugin_basename( WCPRO_SALENOTIFICATION_PL_ROOT ) );
define( 'WCPRO_SALENOTIFICATION_ASSETS', trailingslashit( WCPRO_SALENOTIFICATION_PL_URL . 'assets' ) );

require ( WCPRO_SALENOTIFICATION_PL_PATH . 'includes/base.php' );

\WC_Sale_Notification_Pro\Base::instance();