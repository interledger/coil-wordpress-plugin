<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil\Admin;
use Coil\Settings;
use WP_UnitTestCase;

/**
 * Testing the custom message settings.
 */
class Test_Settings_Panel extends WP_UnitTestCase {

	/**
	 * Check that message defaults can be retreived successfully.
	 *
	 * @return void
	 */
	public function test_retreiving_message_defaults() :  void {

		// Setting simplified message ID's
		$id = [
			'unverified'      => 'coil_unable_to_verify_message',
			'donation_bar'    => 'coil_voluntary_donation_message',
			'pending'         => 'coil_verifying_status_message',
			'fully_gated'     => 'coil_fully_gated_content_message',
			'partially_gated' => 'coil_partially_gated_content_message',
			'button_text'     => 'coil_learn_more_button_text',
			'button_link'     => 'coil_learn_more_button_link',
		];

		// Ensuring no custom messages are present in the database
		delete_option( 'coil_messaging_settings_group' );

		// Creating an array of the message defaults that were retreived
		$message = [
			$id['unverified']      => Admin\get_messaging_setting_or_default( $id['unverified'] ),
			$id['donation_bar']    => Admin\get_messaging_setting_or_default( $id['donation_bar'] ),
			$id['pending']         => Admin\get_messaging_setting_or_default( $id['pending'] ),
			$id['fully_gated']     => Admin\get_messaging_setting_or_default( $id['fully_gated'] ),
			$id['partially_gated'] => Admin\get_messaging_setting_or_default( $id['partially_gated'] ),
			$id['button_text']     => Admin\get_messaging_setting_or_default( $id['button_text'] ),
			$id['button_link']     => Admin\get_messaging_setting_or_default( $id['button_link'] ),
		];

		// Checking that all defaults are correct
		$this->assertSame( 'You need a valid Coil account to see this content.', $message[ $id['unverified'] ] );
		$this->assertSame( 'This site is monetized using Coil. If you enjoy the content, consider supporting us by signing up for a Coil Membership. Here\'s how…', $message[ $id['donation_bar'] ] );
		$this->assertSame( 'Verifying Web Monetization status. Please wait...', $message[ $id['pending'] ] );
		$this->assertSame( 'Unlock exclusive content with Coil. Need a Coil account?', $message[ $id['fully_gated'] ] );
		$this->assertSame( 'To keep reading, join Coil and install the browser extension. Visit coil.com for more information.', $message[ $id['partially_gated'] ] );
		$this->assertSame( 'Get Coil to access', $message[ $id['button_text'] ] );
		$this->assertSame( 'https://coil.com/', $message[ $id['button_link'] ] );
	}

	/**
	 * Check that custom messages can be retreived successfully.
	 *
	 * @return void
	 */
	public function test_retreiving_custom_messages() :  void {

		// Setting simplified message ID's
		$id = [
			'unverified'      => 'coil_unable_to_verify_message',
			'donation_bar'    => 'coil_voluntary_donation_message',
			'pending'         => 'coil_verifying_status_message',
			'fully_gated'     => 'coil_fully_gated_content_message',
			'partially_gated' => 'coil_partially_gated_content_message',
			'button_text'     => 'coil_learn_more_button_text',
			'button_link'     => 'coil_learn_more_button_link',
		];

		// Adding custom messages to the database
		$custom_message = [
			$id['unverified']      => 'Unable to verify',
			$id['donation_bar']    => 'Voluntary donation',
			$id['pending']         => 'Loading content',
			$id['fully_gated']     => 'Fully gated',
			$id['partially_gated'] => 'Partially gated',
			$id['button_text']     => 'Learn More',
			$id['button_link']     => 'https://https://help.coil.com/docs/dev/web-monetization/index.html',
		];
		update_option( 'coil_messaging_settings_group', $custom_message );
		if ( ! update_option( 'coil_messaging_settings_group', $custom_message ) ) {
			add_option( 'coil_messaging_settings_group', $custom_message );
		}

		// Creating an array of the messages that were retreived
		$message = [
			$id['unverified']      => Admin\get_messaging_setting_or_default( $id['unverified'] ),
			$id['donation_bar']    => Admin\get_messaging_setting_or_default( $id['donation_bar'] ),
			$id['pending']         => Admin\get_messaging_setting_or_default( $id['pending'] ),
			$id['fully_gated']     => Admin\get_messaging_setting_or_default( $id['fully_gated'] ),
			$id['partially_gated'] => Admin\get_messaging_setting_or_default( $id['partially_gated'] ),
			$id['button_text']     => Admin\get_messaging_setting_or_default( $id['button_text'] ),
			$id['button_link']     => Admin\get_messaging_setting_or_default( $id['button_link'] ),
		];

		// Checking that all messages that were retreived are correct
		$this->assertSame( $custom_message[ $id['unverified'] ], $message[ $id['unverified'] ] );
		$this->assertSame( $custom_message[ $id['donation_bar'] ], $message[ $id['donation_bar'] ] );
		$this->assertSame( $custom_message[ $id['pending'] ], $message[ $id['pending'] ] );
		$this->assertSame( $custom_message[ $id['fully_gated'] ], $message[ $id['fully_gated'] ] );
		$this->assertSame( $custom_message[ $id['partially_gated'] ], $message[ $id['partially_gated'] ] );
		$this->assertSame( $custom_message[ $id['button_text'] ], $message[ $id['button_text'] ] );
		$this->assertSame( $custom_message[ $id['button_link'] ], $message[ $id['button_link'] ] );
	}

	/**
	 * Check that a variety of custom and default messages can be retreived successfully.
	 *
	 * @return void
	 */
	public function test_retreiving_custom_messages_and_defaults() :  void {
		// Setting simplified message ID's
		$id = [
			'unverified'      => 'coil_unable_to_verify_message',
			'donation_bar'    => 'coil_voluntary_donation_message',
			'pending'         => 'coil_verifying_status_message',
			'fully_gated'     => 'coil_fully_gated_content_message',
			'partially_gated' => 'coil_partially_gated_content_message',
			'button_text'     => 'coil_learn_more_button_text',
			'button_link'     => 'coil_learn_more_button_link',
		];

		// Adding custom messages to the database
		$custom_message = [
			$id['unverified']  => 'Unable to verify',
			$id['pending']     => 'Loading content',
			$id['fully_gated'] => 'Fully gated',
			$id['button_text'] => 'Learn More',
			// Leaving one option set to an empty string becasue this state occurs in the database once a custom message has been deleted
			$id['button_link'] => '',
		];
		update_option( 'coil_messaging_settings_group', $custom_message );
		if ( ! update_option( 'coil_messaging_settings_group', $custom_message ) ) {
			add_option( 'coil_messaging_settings_group', $custom_message );
		}

		// Creating an array of the messages that were retreived
		$message = [
			$id['unverified']      => Admin\get_messaging_setting_or_default( $id['unverified'] ),
			$id['donation_bar']    => Admin\get_messaging_setting_or_default( $id['donation_bar'] ),
			$id['pending']         => Admin\get_messaging_setting_or_default( $id['pending'] ),
			$id['fully_gated']     => Admin\get_messaging_setting_or_default( $id['fully_gated'] ),
			$id['partially_gated'] => Admin\get_messaging_setting_or_default( $id['partially_gated'] ),
			$id['button_text']     => Admin\get_messaging_setting_or_default( $id['button_text'] ),
			$id['button_link']     => Admin\get_messaging_setting_or_default( $id['button_link'] ),
		];

		// Checking that all messages that were retreived are correct
		$this->assertSame( $custom_message[ $id['unverified'] ], $message[ $id['unverified'] ] );
		$this->assertSame( 'This site is monetized using Coil. If you enjoy the content, consider supporting us by signing up for a Coil Membership. Here\'s how…', $message[ $id['donation_bar'] ] );
		$this->assertSame( $custom_message[ $id['pending'] ], $message[ $id['pending'] ] );
		$this->assertSame( $custom_message[ $id['fully_gated'] ], $message[ $id['fully_gated'] ] );
		$this->assertSame( 'To keep reading, join Coil and install the browser extension. Visit coil.com for more information.', $message[ $id['partially_gated'] ] );
		$this->assertSame( $custom_message[ $id['button_text'] ], $message[ $id['button_text'] ] );
		$this->assertSame( 'https://coil.com/', $message[ $id['button_link'] ] );
	}

	/**
	 * Testing if a user has settings which they saved in the customizer that they are migrated successfully to the wp_options table
	 *
	 * @return void
	 */
	public function test_transfer_of_messages_from_customizer() :  void {

		// Setting simplified message ID's
		$id = [
			'unverified'      => 'coil_unable_to_verify_message',
			'donation_bar'    => 'coil_voluntary_donation_message',
			'pending'         => 'coil_verifying_status_message',
			'fully_gated'     => 'coil_fully_gated_content_message',
			'partially_gated' => 'coil_partially_gated_content_message',
			'button_text'     => 'coil_learn_more_button_text',
			'button_link'     => 'coil_learn_more_button_link',
		];

		//transfer_customizer_message_settings()
		// Adding custom messages to the theme_mod
		set_theme_mod( $id['unverified'], 'Unable to verify' );
		set_theme_mod( $id['pending'], 'Loading content' );
		set_theme_mod( 'coil_unsupported_message', 'Fully gated' );
		set_theme_mod( $id['button_text'], 'Learn More' );
		// Leaving one option set to an empty string becasue this state occurs in the database once a custom message has been deleted
		set_theme_mod( $id['button_link'], '' );

		// Transferrng settings to the wp_options table
		Settings\transfer_customizer_message_settings();

		// Creating an array of the messages that were retreived from the wp_options table.
		$message = [
			$id['unverified']      => Admin\get_messaging_setting_or_default( $id['unverified'] ),
			$id['donation_bar']    => Admin\get_messaging_setting_or_default( $id['donation_bar'] ),
			$id['pending']         => Admin\get_messaging_setting_or_default( $id['pending'] ),
			$id['fully_gated']     => Admin\get_messaging_setting_or_default( $id['fully_gated'] ),
			$id['partially_gated'] => Admin\get_messaging_setting_or_default( $id['partially_gated'] ),
			$id['button_text']     => Admin\get_messaging_setting_or_default( $id['button_text'] ),
			$id['button_link']     => Admin\get_messaging_setting_or_default( $id['button_link'] ),
		];

		// Checking that all messages that were retreived are correct
		$this->assertSame( 'Unable to verify', $message[ $id['unverified'] ] );
		$this->assertSame( 'This site is monetized using Coil. If you enjoy the content, consider supporting us by signing up for a Coil Membership. Here\'s how…', $message[ $id['donation_bar'] ] );
		$this->assertSame( 'Loading content', $message[ $id['pending'] ] );
		$this->assertSame( 'Fully gated', $message[ $id['fully_gated'] ] );
		$this->assertSame( 'To keep reading, join Coil and install the browser extension. Visit coil.com for more information.', $message[ $id['partially_gated'] ] );
		$this->assertSame( 'Learn More', $message[ $id['button_text'] ] );
		$this->assertSame( 'https://coil.com/', $message[ $id['button_link'] ] );

		// Checking that the theme_mod messages have been removed
		$this->assertFalse( get_theme_mod( $id['unverified'] ) );
		$this->assertFalse( get_theme_mod( $id['pending'] ) );
		$this->assertFalse( get_theme_mod( $id['fully_gated'] ) );
		$this->assertFalse( get_theme_mod( $id['button_text'] ) );
		$this->assertFalse( get_theme_mod( $id['button_link'] ) );
	}

	/**
	 * Testing if the the default payment pointer is an empty string.
	 *
	 * @return void
	 */
	public function test_if_default_payment_pointer_is_an_empty_string() :  void {

		$this->assertSame( '', Admin\get_global_settings( 'coil_payment_pointer_id' ) );
	}

	/**
	 * Testing if the payment pointer value has been saved successfully to the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_payment_pointer_saves_successfully() :  void {

		$payment_pointer = [ 'coil_payment_pointer_id' => '$wallet.example.com/bob' ];
		update_option( 'coil_global_settings_group', $payment_pointer );
		if ( ! update_option( 'coil_global_settings_group', $payment_pointer ) ) {
			add_option( 'coil_global_settings_group', $payment_pointer );
		}

		$this->assertSame( $payment_pointer['coil_payment_pointer_id'], Admin\get_global_settings( 'coil_payment_pointer_id' ) );
	}

	/**
	 * Testing if the default content container can be retreived successfully from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_default_content_container_is_retreived_successfully() :  void {

		$this->assertSame( '.content-area .entry-content', Admin\get_global_settings( 'coil_content_container' ) );
	}

	/**
	 * Testing if the content container value has been successfully saved to the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_content_container_saves_successfully() :  void {

		$content_container = [ 'coil_content_container' => '.content-area .entry-content, .post-story' ];
		update_option( 'coil_global_settings_group', $content_container );
		if ( ! update_option( 'coil_global_settings_group', $content_container ) ) {
			add_option( 'coil_global_settings_group', $content_container );
		}

		$this->assertSame( $content_container['coil_content_container'], Admin\get_global_settings( 'coil_content_container' ) );
	}
}

