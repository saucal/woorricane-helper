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
class CheckoutSetup {

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public static function hooks() {
		add_action( 'woorricane_control_cleanup', array( __CLASS__, 'cleanup' ) );
		add_action( 'woorricane_control_prepare_product', array( __CLASS__, 'prepare_product' ) );
		add_action( 'woocommerce_checkout_order_created', array( __CLASS__, 'record_server_ip' ), 10, 1 );
	}

	/**
	 * Record server IP
	 *
	 * @param \WC_Order $order Order object.
	 * @return void
	 */
	public static function record_server_ip( $order ) {
		$order->add_meta_data( 'serverip', $_SERVER['SERVER_ADDR'] );
		$order->save();
	}

	/**
	 * Cleanup all orders
	 *
	 * @return void
	 */
	public static function cleanup() {
		global $wpdb;
		$wpdb->query( 'DELETE FROM `wp_posts` WHERE post_type IN ("shop_coupon", "shop_order", "shop_order_refund", "shop_subscription")' );

		$wpdb->query(
			'DELETE pm FROM wp_postmeta pm
			LEFT JOIN wp_posts o ON pm.post_id = o.ID
			WHERE o.ID IS NULL'
		);

		$wpdb->query(
			'DELETE oi FROM wp_woocommerce_order_items oi
			LEFT JOIN wp_posts o ON oi.order_id = o.ID
			WHERE o.ID IS NULL'
		);

		$wpdb->query(
			'DELETE dpp FROM wp_woocommerce_downloadable_product_permissions dpp
			LEFT JOIN wp_posts o ON dpp.order_id = o.ID
			WHERE o.ID IS NULL'
		);

		$wpdb->query(
			'DELETE oim FROM wp_woocommerce_order_itemmeta oim
			LEFT JOIN wp_woocommerce_order_items oi ON oi.order_item_id = oim.order_item_id
			WHERE oi.order_id IS NULL'
		);

		$wpdb->query(
			'DELETE c FROM wp_comments c
			LEFT JOIN wp_posts o ON c.comment_post_ID = o.ID
			WHERE o.ID IS NULL'
		);

		$wpdb->query(
			'DELETE cm FROM wp_commentmeta cm
			LEFT JOIN wp_comments c ON c.comment_ID = cm.comment_id
			WHERE c.comment_ID IS NULL'
		);

		$wpdb->query(
			'DELETE u FROM wp_users u
			INNER JOIN wp_usermeta um ON u.ID = um.user_id AND um.meta_key LIKE "wp_%capabilities"
			WHERE um.meta_value NOT LIKE "%\"administrator\"%"'
		);

		$wpdb->query(
			'DELETE um FROM wp_usermeta um
			LEFT JOIN wp_users u ON um.user_id = u.ID
			WHERE u.ID IS NULL'
		);

		$wpdb->query(
			'UPDATE wp_posts p
			LEFT JOIN wp_users u on p.post_author = u.ID
			SET post_author = (SELECT ID FROM wp_users u2 ORDER BY ID ASC LIMIT 1)
			WHERE u.ID IS NULL'
		);
		echo 'Cleaned up!';
	}

	/**
	 * Prepare product for checkout run
	 *
	 * @return void
	 */
	public static function prepare_product() {
		$product = \wc_get_product( $_REQUEST['prepare_product'] );
		$product->set_stock_quantity( isset( $_REQUEST['stock'] ) ? $_REQUEST['stock'] : 1 );
		$product->save();
		echo 'All set up!';
	}

}
