<?php
/**
 * Part of Woo Mercado Pago Module
 * Author - Mercado Pago
 * Developer
 * Copyright - Copyright(c) MercadoPago [https://www.mercadopago.com]
 * License - https://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 *
 * @package MercadoPago
 * @category Includes
 * @author Mercado Pago
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_WooMercadoPago_Preference_Ticket
 */
class WC_WooMercadoPago_Preference_Ticket extends WC_WooMercadoPago_Preference_Abstract {


	/**
	 * WC_WooMercadoPago_PreferenceTicket constructor.
	 *
	 * @param WC_WooMercadoPago_Payment_Abstract $payment Payment.
	 * @param object                             $order Order.
	 * @param mixed                              $ticket_checkout Ticket checkout.
	 */
	public function __construct( $payment, $order, $ticket_checkout ) {
		parent::__construct( $payment, $order, $ticket_checkout );
		$this->preference                       = $this->make_commum_preference();
		$this->preference['date_of_expiration'] = $this->get_date_of_expiration( $payment );
		$this->preference['transaction_amount'] = $this->get_transaction_amount();
		$this->preference['description']        = implode( ', ', $this->list_of_items );
		$this->preference['payment_method_id']  = $this->checkout['paymentMethodId'];
		$this->preference['payer']['email']     = $this->get_email();

		if ( 'BRL' === $this->site_data[ $this->site_id ]['currency'] ) {
			$this->preference['payer']['first_name']               = $this->checkout['firstname'];
			$this->preference['payer']['last_name']                = 14 === strlen( $this->checkout['docNumber'] ) ? $this->checkout['lastname'] : $this->checkout['firstname'];
			$this->preference['payer']['identification']['type']   = 14 === strlen( $this->checkout['docNumber'] ) ? 'CPF' : 'CNPJ';
			$this->preference['payer']['identification']['number'] = $this->checkout['docNumber'];
			$this->preference['payer']['address']['street_name']   = $this->checkout['address'];
			$this->preference['payer']['address']['street_number'] = $this->checkout['number'];
			$this->preference['payer']['address']['neighborhood']  = $this->checkout['city'];
			$this->preference['payer']['address']['city']          = $this->checkout['city'];
			$this->preference['payer']['address']['federal_unit']  = $this->checkout['state'];
			$this->preference['payer']['address']['zip_code']      = $this->checkout['zipcode'];
		}

		if ( 'UYU' === $this->site_data[ $this->site_id ]['currency'] ) {
			$this->preference['payer']['identification']['type']   = $ticket_checkout['docType'];
			$this->preference['payer']['identification']['number'] = $ticket_checkout['docNumber'];
		}

		if ( 'webpay' === $ticket_checkout['paymentMethodId'] ) {
			$this->preference['callback_url']                                 = get_site_url();
			$this->preference['transaction_details']['financial_institution'] = '1234';
			$this->preference['additional_info']['ip_address']                = '127.0.0.1';
			$this->preference['payer']['identification']['type']              = 'RUT';
			$this->preference['payer']['identification']['number']            = '0';
			$this->preference['payer']['entity_type']                         = 'individual';
		}

		$this->preference['external_reference']           = $this->get_external_reference();
		$this->preference['additional_info']['items']     = $this->items;
		$this->preference['additional_info']['payer']     = $this->get_payer_custom();
		$this->preference['additional_info']['shipments'] = $this->shipments_receiver_address();
		$this->preference['additional_info']['payer']     = $this->get_payer_custom();

		if (
			isset( $this->checkout['discount'] ) && ! empty( $this->checkout['discount'] ) &&
			isset( $this->checkout['coupon_code'] ) && ! empty( $this->checkout['coupon_code'] ) &&
			$this->checkout['discount'] > 0 && 'woo-mercado-pago-ticket' === WC()->session->chosen_payment_method
		) {
			$this->preference['additional_info']['items'][] = $this->add_discounts();
			$this->preference                               = array_merge( $this->preference, $this->add_discounts_campaign() );
		}

		$internal_metadata            = parent::get_internal_metadata();
		$merge_array                  = array_merge( $internal_metadata, $this->get_internal_metadata_ticket() );
		$this->preference['metadata'] = $merge_array;
	}

	/**
	 * Get date of expiration
	 *
	 * @param WC_WooMercadoPago_Ticket_Gateway $payment Payment.
	 * @return string date
	 */
	public function get_date_of_expiration( WC_WooMercadoPago_Ticket_Gateway $payment = null ) {
		$date_expiration = ! is_null( $payment )
			? $payment->get_option_mp( 'date_expiration' )
			: $this->get_option( 'date_expiration', '' );

		if ( '' !== $date_expiration ) {
			return gmdate( 'Y-m-d\TH:i:s.000O', strtotime( '+' . $date_expiration . ' days' ) );
		}
	}

	/**
	 * Get items build array
	 *
	 * @return array
	 */
	public function get_items_build_array() {
		$items = parent::get_items_build_array();
		foreach ( $items as $key => $item ) {
			if ( isset( $item['currency_id'] ) ) {
				unset( $items[ $key ]['currency_id'] );
			}
		}

		return $items;
	}

	/**
	 * Get internal metadata ticket
	 *
	 * @return array
	 */
	public function get_internal_metadata_ticket() {
		return array(
			'checkout'      => 'custom',
			'checkout_type' => 'ticket',
		);
	}
}
