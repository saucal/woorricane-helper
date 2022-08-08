<?php
/**
 * WooCommerce Hooks
 *
 * @package     WoorricaneHelper/Customizations
 * @version     1.0.0
 */

namespace WoorricaneHelper\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Class.
 */
class Locks {

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'woorricane_control_lock', array( __CLASS__, 'lock' ) );
		add_action( 'woorricane_control_unlock', array( __CLASS__, 'unlock' ) );

		add_filter( 'woocommerce_add_to_cart_quantity', array( __CLASS__, 'wait_for_cart_lock' ), 10, 2 );
		add_action( 'woocommerce_checkout_order_created', array( __CLASS__, 'wait_for_checkout_lock' ), -100 );
		add_action( 'woocommerce_checkout_order_created', array( __CLASS__, 'wait_for_after_reserve_lock' ), 100 );
	}

	/**
	 * Set a specific lock
	 *
	 * @return void
	 */
	public static function lock() {
		touch( self::get_lock_file_path( $_REQUEST['lock'] ) );
		echo 'Flood it now!';
	}

	/**
	 * Unset a specific lock
	 *
	 * @return void
	 */
	public static function unlock() {
		unlink( self::get_lock_file_path( $_REQUEST['unlock'] ) );
		echo 'See if it held up!';
	}

	/**
	 * Log file path
	 *
	 * @param string $lock Name of the lock file to get.
	 *
	 * @return string
	 */
	private static function get_lock_file_path( $lock ) {
		$dir = \wp_upload_dir();
		$dir = implode( \DIRECTORY_SEPARATOR, array( $dir['basedir'], 'woorricane' ) );
		if ( ! \file_exists( $dir ) ) {
			if ( ! wp_mkdir_p( $dir ) ) {
				return;
			}
		}

		return implode( \DIRECTORY_SEPARATOR, array( $dir, $lock ) );
	}

	/**
	 * Maybe wait for the lock to be released
	 *
	 * @param string $file Lock to wait for.
	 *
	 * @return void
	 */
	private static function maybe_wait( $file ) {
		$file = self::get_lock_file_path( $file );
		while ( \file_exists( $file ) ) {
			usleep( 10 );
		}
	}

	/**
	 * Filter usage to sync add to cart action
	 *
	 * @param float $qty Quantity of product to be added to the cart.
	 * @param int   $product_id Product ID (or variation) to be added to the cart.
	 *
	 * @return float $qty variable
	 */
	public static function wait_for_cart_lock( $qty, $product_id ) {
		self::maybe_wait( 'cart' );
		return $qty;
	}

	/**
	 * Wait for checkout lock
	 */
	public static function wait_for_checkout_lock() {
		self::maybe_wait( 'checkout' );
	}

	/**
	 * Wait for after reserve lock
	 */
	public static function wait_for_after_reserve_lock() {
		self::maybe_wait( 'after_reserve' );
	}

}
