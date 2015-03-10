<?php
/**
 * Admin
 *
 * @author Nicola Mustone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Twitter_Notifications_Admin extends WC_Settings_Page {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'twitter_notifications';
		$this->label = __( 'Twitter Notifications', 'wc-twitter-notifications' );
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		$currency_code_options = get_woocommerce_currencies();
		foreach ( $currency_code_options as $code => $name ) {
			$currency_code_options[ $code ] = $name . ' (' . get_woocommerce_currency_symbol( $code ) . ')';
		}
		$settings = apply_filters( 'wc_twitter_notifications_settings', array(
			array(
				'title' => __( 'OAuth Settings', 'wc-twitter-notifications' ),
				'type' => 'title',
				'desc' => '',
				'id' => 'twitter_notifications_options'
			),
			array(
				'title'    => __( 'Consumer key', 'wc-twitter-notifications' ),
				'desc'     => '',
				'id'       => 'wc_twitter_notifications_consumer_key',
				'css'      => 'min-width:350px',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'    => __( 'Consumer secret', 'wc-twitter-notifications' ),
				'desc'     => '',
				'id'       => 'wc_twitter_notifications_consumer_secret',
				'css'      => 'min-width:350px',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'    => __( 'Access token', 'wc-twitter-notifications' ),
				'desc'     => '',
				'id'       => 'wc_twitter_notifications_access_token',
				'css'      => 'min-width:350px',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array(
				'title'    => __( 'Access token secret', 'wc-twitter-notifications' ),
				'desc'     => '',
				'id'       => 'wc_twitter_notifications_access_token_secret',
				'css'      => 'min-width:350px',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' =>  true,
			),
			array( 'type' => 'sectionend', 'id' => 'pricing_options' )
		) );

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();
		WC_Admin_Settings::save_fields( $settings );
	}
}

return new WC_Twitter_Notifications_Admin();
