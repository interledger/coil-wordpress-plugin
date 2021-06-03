<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil\Admin;
use WP_UnitTestCase;

/**
 * Testing the custom message settings.
 */
class Test_Messaging_Settings extends WP_UnitTestCase {

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
			$id['unverified']      => 'Unable to verify',
			$id['pending']         => 'Loading content',
			$id['fully_gated']     => 'Fully gated',
			$id['button_text']     => 'Learn More',
            // Leaving one option set to an empty string becasue this state occurs in the database once a custom message has been deleted
			$id['button_link']     => '',
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
}

