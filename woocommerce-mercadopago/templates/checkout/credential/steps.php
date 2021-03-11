<?php
/**
 * Part of Woo Mercado Pago Module
 * Author - Mercado Pago
 * Developer
 * Copyright - Copyright(c) MercadoPago [https://www.mercadopago.com]
 * License - https://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 *
 * @package MercadoPago
 * @category Template
 * @author Mercado Pago
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="mp-row">
	<h4 class="mp-title-checkout-body mp-pb-20"><?php echo esc_html( $title ); ?></h4>
	<div class="mp-col-md-2 mp-text-center mp-pb-10">
		<p class="mp-number-checkout-body">1</p>
		<p class="mp-text-steps mp-text-center mp-px-20">
			<b><?php echo esc_html( $upload_credentials_highlight ); ?></b>
			<?php echo esc_html( $upload_credentials ); ?>
		</p>
	</div>

	<div class="mp-col-md-2 mp-text-center mp-pb-10">
		<p class="mp-number-checkout-body">2</p>
		<p class="mp-text-steps mp-text-center mp-px-20">
			<b><?php echo esc_html( $approve_account_highlight ); ?></b>
			<?php echo esc_html( $approve_account ); ?>
		</p>
	</div>

	<div class="mp-col-md-2 mp-text-center mp-pb-10">
		<p class="mp-number-checkout-body">3</p>
		<p class="mp-text-steps mp-text-center mp-px-20">
			<b><?php echo esc_html( $basic_information_highlight ); ?></b>
			<?php echo esc_html( $basic_information ); ?>
		</p>
	</div>

	<div class="mp-col-md-2 mp-text-center mp-pb-10">
		<p class="mp-number-checkout-body">4</p>
		<p class="mp-text-steps mp-text-center mp-px-20">
			<b><?php echo esc_html( $payment_preference_highlight ); ?></b>
			<?php echo esc_html( $payment_preference ); ?>
		</p>
	</div>

	<div class="mp-col-md-2 mp-text-center mp-pb-10">
		<p class="mp-number-checkout-body">5</p>
		<p class="mp-text-steps mp-text-center mp-px-20">
			<b><?php echo esc_html( $advanced_settings_highlight ); ?></b>
			<?php echo esc_html( $advanced_settings ); ?>
		</p>
	</div>
</div>
