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
 * Class WC_WooMercadoPago_PreferenceCustom
 */
class WC_WooMercadoPago_Preference_Custom extends WC_WooMercadoPago_Preference_Abstract {

	/**
	 * WC_WooMercadoPago_PreferenceCustom constructor.
	 *
	 * @param WC_WooMercadoPago_Payment_Abstract $payment Payment.
	 * @param object                             $order Order.
	 * @param array|null                         $custom_checkout Custom checkout.
	 */
	public function __construct( $payment, $order, $custom_checkout ) {
		parent::__construct( $payment, $order, $custom_checkout );
		$this->preference                       = $this->make_commum_preference();
		$this->preference['transaction_amount'] = $this->get_transaction_amount();
		$this->preference['token']              = $this->checkout['token'];
		$this->preference['description']        = implode( ', ', $this->list_of_items );
		$this->preference['installments']       = (int) $this->checkout['installments'];
		$this->preference['payment_method_id']  = $this->checkout['paymentMethodId'];
		$this->preference['payer']['email']     = $this->get_email();
		if ( array_key_exists( 'token', $this->checkout ) ) {
			$this->preference['metadata']['token'] = $this->checkout['token'];
			if ( ! empty( $this->checkout['CustomerId'] ) ) {
				$this->preference['payer']['id'] = $this->checkout['CustomerId'];
			}
			if ( ! empty( $this->checkout['issuer'] ) ) {
				$this->preference['issuer_id'] = (int) $this->checkout['issuer'];
			}
		}

		$this->preference['additional_info']['items']     = $this->items;
		$this->preference['additional_info']['payer']     = $this->get_payer_custom();
		$this->preference['additional_info']['shipments'] = $this->shipments_receiver_address();

		if (
			isset( $this->checkout['discount'] ) && ! empty( $this->checkout['discount'] ) &&
			isset( $this->checkout['coupon_code'] ) && ! empty( $this->checkout['coupon_code'] ) &&
			$this->checkout['discount'] > 0 && 'woo-mercado-pago-custom' === WC()->session->chosen_payment_method
		) {
			$this->preference['additional_info']['items'][] = $this->add_discounts();
			$this->preference                               = array_merge( $this->preference, $this->add_discounts_campaign() );
		}

		$internal_metadata            = parent::get_internal_metadata();
		$merge_array                  = array_merge( $internal_metadata, $this->get_internal_metadata_custom() );
		$this->preference['metadata'] = $merge_array;

	}

	/**
	 * Ship cost item
	 *
	 * @return array
	 */
	public function ship_cost_item() {
		$item = parent::ship_cost_item();
		if ( isset( $item['currency_id'] ) ) {
			unset( $item['currency_id'] );
		}
		return $item;
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
	 * Get internal metadata custom
	 *
	 * @return array
	 */
	public function get_internal_metadata_custom() {
		return array(
			'checkout'      => 'custom',
			'checkout_type' => 'credit_card',
		);
	}
}
