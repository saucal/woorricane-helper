<?php
/**
 * WooCommerce Hooks
 *
 * @package     WoorricaneHelper/Customizations
 * @version     1.0.0
 */

namespace WoorricaneHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Class.
 */
class DisableEmails {

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'pre_wp_mail', array( __CLASS__, 'soft_sending' ), \PHP_INT_MIN );
	}

	public static function soft_sending() {
		// Sleep for a random number of miliseconds between 200 and 1000.
		usleep( wp_rand( 200000, 1000000 ) );
		return true;
	}

}
