<?php
/**
 * Account & Checkout fields
 *
 * @author Nicola Mustone
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Twitter_Notifications_Account {
	public function __construct() {
		add_filter( 'woocommerce_checkout_fields', array( $this, 'checkout_fields' ) );
		add_action( 'woocommerce_edit_account_form', array( $this, 'account_fields' ) );
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'save_checkout_fields' ) );
		add_action( 'woocommerce_save_account_details', array( $this, 'save_profile_fields' ) );

		add_action( 'show_user_profile', array( $this, 'profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'profile_fields' ) );

		add_action( 'personal_options_update', array( $this, 'save_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_profile_fields' ) );

	}

	public function checkout_fields( $fields ) {
		$user           = get_user_by( 'id', get_current_user_id() );
		$twitter_handle = esc_attr( get_the_author_meta( 'account_twitter_notifications', $user->ID ) );

		$fields['order']['twitter_notifications'] = array(
			'type'        => 'text',
			'label'       => __( 'Twitter username', 'wc-twitter-notifications' ),
			'placeholder' => __( 'username', 'wc-twitter-notifications' ),
			'description' => __( 'Write your Twitter username (without @) to receive order status updates via Twitter private messages.', 'wc-twitter-notifications' ),
			'class'       => array( 'form-row-wide'),
			'default'     => $twitter_handle
		);

		return $fields;
	}

	public function account_fields() {
		$user           = get_user_by( 'id', get_current_user_id() );
		$twitter_handle = esc_attr( get_the_author_meta( 'account_twitter_notifications', $user->ID ) );
		?>
		<p class="form-row form-row-wide">
			<label for="account_twitter_notifications"><?php _e( 'Twitter username', 'wc-twitter-notifications' ); ?></label>
			<input type="text" class="input-text" name="account_twitter_notifications" id="account_twitter_notifications" value="<?php echo esc_attr( get_the_author_meta( 'account_twitter_notifications', $user->ID ) ); ?>" />
		</p>
		<?php
	}

	public function profile_fields( $user ) {
		?>

		<h3><?php _e( 'Twitter notifications username', 'wc-twitter-notifications' ); ?></h3>

		<table class="form-table">
			<tr>
				<th><label for="account_twitter_notifications"><?php _e( 'Username', 'wc-twitter-notifications' ); ?></label></th>

				<td>
					<input type="text" name="account_twitter_notifications" id="account_twitter_notifications" value="<?php echo esc_attr( get_the_author_meta( 'account_twitter_notifications', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Write your Twitter username (without @) to receive order status updates via Twitter private messages.', 'wc-twitter-notifications' ); ?></span>
				</td>
			</tr>

		</table>
		<?php
	}

	public function save_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		update_usermeta( $user_id, 'account_twitter_notifications', wc_clean( $_POST['account_twitter_notifications'] ) );
	}

	public function save_checkout_fields( $posted ) {
		if ( isset( $posted['twitter_notifications'] ) && ! empty( $posted['twitter_notifications'] ) ) {
			$user_id = get_current_user_id();
			update_usermeta( $user_id, 'account_twitter_notifications', wc_clean( $posted['twitter_notifications'] ) );
		}
	}
}

new WC_Twitter_Notifications_Account();
