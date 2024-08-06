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
		add_action( 'pre_wp_mail', '__return_true' );
	}

}
