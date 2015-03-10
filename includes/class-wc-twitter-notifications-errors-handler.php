<?php
/**
 * Admin
 *
 * @author Nicola Mustone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Twitter_Notifications_Errors_Handler {
	private static function missing_admin_data( $data ) {
		echo '<div class="error"><p>' .
			sprintf(
				__( 'OAuth error: %s is empty. Please check your %sWooCommerce Twitter Notifications settings%s', 'wc-twitter-notification'),
				$data,
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=twitter_notifications' ) . '">',
				'</a>'
			) . '</p></div>';
	}

	public static function missing_consumer_key() {
		self::missing_admin_data( __( 'Consumer Key', 'wc-twitter-notifications' ) );
	}

	public static function missing_consumer_secret() {
		self::missing_admin_data( __( 'Consumer Secret', 'wc-twitter-notifications' ) );
	}

	public static function missing_access_token() {
		self::missing_admin_data( __( 'Access Token', 'wc-twitter-notifications' ) );
	}

	public static function missing_access_token_secret() {
		self::missing_admin_data( __( 'Access Token Secret', 'wc-twitter-notifications' ) );
	}
}
