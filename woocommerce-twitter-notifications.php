<?php
/**
 * Plugin Name: WooCommerce Twitter Notifications
 * Plugin URI: http://www.woothemes.com/woocommerce/
 * Description: An extension for WooCommerce which sends order status updates to the customers via Twitter private messages.
 * Version: 1.0.0
 * Author: Nicola Mustone
 * Author URI: http://nicolamustone.it
 * Requires at least: 4.0
 * Tested up to: 4.1
 *
 * Text Domain: wc-twitter-notifications
 * Domain Path: /i18n/
 *
 * @author Nicola Mustone
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Include Woo functions
require_once 'woo-includes/woo-functions.php';

// Check if WooCommerce is active
if ( ! is_woocommerce_active() ) {
	add_action( 'admin_notices', 'wc_twitter_notifications_wc_inactive' );
	function wc_twitter_notifications_wc_inactive() {
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Twitter Notifications requires WooCommerce in order to work properly. <a href="%s" target="_blank">Please install WooCommerce</a>.', 'wc-twitter-notifications' ), 'http://wordpress.org/plugins/woocommerce/' ) . '</p></div>';
	}

	return;
}

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * WC_Twitter_Notifications class
 */
class WC_Twitter_Notifications {
	/**
	 * Plugin main file
	 */
	const WC_TWITTER_NOTIFICATIONS_FILE = __FILE__;

	/**
	 * Plugin version
	 */
	const WC_TWITTER_NOTIFICATIONS_VERSION = '1.0.0';

	/**
	 * OAuth settings
	 *
	 * @var array
	 */
	public $oauth = array();

	/**
	 * Twitter instance
	 *
	 * @var object
	 */
	public $twitter = null;

	/**
	 * @var    WC_Twitter_Notifications The single instance of the class
	 * @access protected
	 */
	protected static $_instance = null;

	/**
	 * Main WC_Twitter_Notifications Instance
	 *
	 * Ensures only one instance of WC_Twitter_Notifications is loaded or can be loaded.
	 *
	 * @static
	 * @see WC_Twitter_Notifications()
	 * @return WC_Twitter_Notifications - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wc-twitter-notifications' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wc-twitter-notifications' ), '1.0.0' );
	}

	/**
	 * __construct function
	 */
	public function __construct() {
		// Set up localization
		$this->load_plugin_textdomain();

		// Scripts & Styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'woocommerce_init', array( $this, 'init' ) );
	}

	/**
	 * Includes the required classes e starts the game
	 *
	 * @return void
	 */
	public function init() {
		$this->set_oauth_token();

		require_once $this->plugin_path() . '/includes/class-wc-twitter-notifications-errors-handler.php';
		require_once $this->plugin_path() . '/includes/class-wc-twitter-notifications-account.php';
		require_once $this->plugin_path() . '/lib/autoload.php';

		$this->twitter = new TwitterOAuth( $this->oauth['consumer_key'], $this->oauth['consumer_secret'], $this->oauth['access_token'], $this->oauth['access_token_secret'] );

		if ( is_admin() ) {
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'load_admin_settings' ) );
		}
	}

	/**
	 * Saves OAuth data into a property. Throws error notices if empty.
	 *
	 * @return void
	 */
	public function set_oauth_token() {
		$this->oauth['consumer_key']        = get_option( 'wc_twitter_notifications_consumer_key' );
		$this->oauth['consumer_secret']     = get_option( 'wc_twitter_notifications_consumer_secret' );
		$this->oauth['access_token']        = get_option( 'wc_twitter_notifications_access_token' );
		$this->oauth['access_token_secret'] = get_option( 'wc_twitter_notifications_access_token_secret' );

		foreach ( $this->oauth as $key => $value ) {
			if ( empty ( $value ) ) {
				add_action( 'admin_notices', array( 'WC_Twitter_Notifications_Errors_Handler', 'missing_' . $key ) );
			}
		}
	}

	/**
	 * Load the admin tab Twitter Notifications in WooCommerce > Settings
	 *
	 * @param array $settings
	 * @return array
	 */
	public function load_admin_settings( $settings ) {
		$settings[] = include( $this->plugin_path() . '/includes/class-wc-twitter-notifications-admin.php' );
		return $settings;
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales are found in:
	 *        WP_LANG_DIR/woocommerce-customer-messages/wc-twitter-notifications-LOCALE.mo
	 *        woocommerce-customer-messages/i18n/wc-twitter-notifications-LOCALE.mo (which if not found falls back to:)
	 *        WP_LANG_DIR/plugins/wc-twitter-notifications-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wc-twitter-notifications' );

		load_textdomain( 'wc-twitter-notifications', WP_LANG_DIR . '/woocommerce-customer-messages/wc-twitter-notifications-' . $locale . '.mo' );
		load_textdomain( 'wc-twitter-notifications', WP_LANG_DIR . '/plugins/wc-twitter-notifications-' . $locale . '.mo' );

		load_plugin_textdomain( 'wc-twitter-notifications', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n" );
	}

	/**
	 * Enqueues plugin scripts & styles
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$suffix = ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '.min' : '';
	}


	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}
	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
}

/**
 * Returns the main instance of WC Customer Messages to prevent the need to use globals.
 *
 * @return WC_Twitter_Notifications
 */
function WC_Twitter_Notifications() {
	return WC_Twitter_Notifications::instance();
}

// Let's start the game!
WC_Twitter_Notifications();
