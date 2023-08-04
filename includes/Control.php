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
class Control {

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'init', array( __CLASS__, 'setup' ) );
	}

	/**
	 * Setup action for Woorricane
	 *
	 * @return void
	 */
	public static function setup() {
		if ( ! isset( $_REQUEST['woorricane_control'] ) ) {
			return;
		}

		if ( empty( $_REQUEST['action'] ) ) {
			return;
		}

		\nocache_headers();
		do_action( 'woorricane_control_' . $_REQUEST['action'] );
		exit;
	}

}
