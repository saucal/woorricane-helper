<?php
/**
 * Dummy Gateway
 *
 * @package     WoorricaneHelper/WooCommerce
 */

namespace WoorricaneHelper\WooCommerce;

/**
 * Dummy Gateway Class
 */
class DummyGateway extends \WC_Payment_Gateway {

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_filter( 'woocommerce_payment_gateways', array( __CLASS__, 'register_gateway' ) );
	}

	/**
	 * Register Dummy Woo Gateway
	 *
	 * @param array $gateways List of gateways.
	 *
	 * @return array
	 */
	public static function register_gateway( $gateways ) {
		$gateways[] = static::class;
		return $gateways;
	}

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {

		$this->id                 = 'dummy';
		$this->has_fields         = false;
		$this->method_title       = __( 'Dummy Gateway', 'woorricane-helper' );
		$this->method_description = __( 'Dummy Gateway for use with Woorricane', 'woorricane-helper' );
		$this->enabled            = 'yes';
		$this->title              = $this->method_title;
		$this->description        = $this->method_description;

		// Actions.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id Order ID being checked out.
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		$order->payment_complete();

		// Return thank you redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

}
