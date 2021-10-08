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
class Test_Messaging_Settings extends WP_UnitTestCase {

	/*/**
	 *
	 * @var array
	 * @var \WP_Post[] message ID's.
	*/
	protected static $id = [
		'pending'                         => 'coil_verifying_status_message',
		'paywall_title'                   => 'coil_paywall_title',
		'paywall_message'                 => 'coil_paywall_message',
		'partially_gated'                 => 'coil_paywall_message',
		'button_text'                     => 'coil_paywall_button_text',
		'button_link'                     => 'coil_paywall_button_link',
		'fully_gated_excerpt_message'     => 'coil_fully_gated_excerpt_message',
		'partially_gated_excerpt_message' => 'coil_partially_gated_excerpt_message',
	];

	/**
	 * Check that message defaults can be retrieved successfully.
	 *
	 * @return void
	 */
	public function test_retrieving_message_defaults() :  void {

		// Ensuring no custom messages are present in the database
		delete_option( 'coil_exclusive_settings_group' );

		// Creating an array of the message defaults that were retrieved
		$defaults = [
			self::$id['paywall_title']   => 'Keep Reading with Coil',
			self::$id['paywall_message'] => 'Unlock exclusive content with Coil. Need a Coil account?',
			self::$id['button_text']     => 'Get Coil to access',
			self::$id['button_link']     => 'https://coil.com/',
		];

		// Creating an array of the message defaults that were retrieved
		$retrieved_messages = [
			self::$id['paywall_title']   => Admin\get_paywall_text_settings_or_default( self::$id['paywall_message'] ),
			self::$id['paywall_message'] => Admin\get_paywall_text_settings_or_default( self::$id['paywall_message'] ),
			self::$id['button_text']     => Admin\get_paywall_text_settings_or_default( self::$id['button_text'] ),
			self::$id['button_link']     => Admin\get_paywall_text_settings_or_default( self::$id['button_link'] ),
		];

		// Checking that all defaults are correct
		$this->assertSame( $defaults, $retrieved_messages );
	}

	/**
	 * Check that custom messages can be retrieved successfully.
	 *
	 * @return void
	 */
	public function test_retrieving_custom_messages() :  void {

		// Adding custom messages to the database
		$custom_message = [
			self::$id['paywall_title']   => 'Exclusive content',
			self::$id['paywall_message'] => 'Fully gated',
			self::$id['button_text']     => 'Learn More',
			self::$id['button_link']     => 'https://https://help.coil.com/docs/dev/web-monetization/index.html',
		];
		update_option( 'coil_exclusive_settings_group', $custom_message );

		// Creating an array of the messages that were retrieved
		$retrieved_message = [
			self::$id['paywall_title']   => Admin\get_paywall_text_settings_or_default( self::$id['paywall_title'] ),
			self::$id['paywall_message'] => Admin\get_paywall_text_settings_or_default( self::$id['paywall_message'] ),
			self::$id['button_text']     => Admin\get_paywall_text_settings_or_default( self::$id['button_text'] ),
			self::$id['button_link']     => Admin\get_paywall_text_settings_or_default( self::$id['button_link'] ),
		];

		// Checking that all messages that were retrieved are correct
		$this->assertSame( $custom_message, $retrieved_message );
	}

	/**
	 * Check that a variety of custom and default messages can be retrieved successfully.
	 *
	 * @return void
	 */
	public function test_retrieving_custom_messages_mixed_with_defaults() :  void {

		// Adding custom messages to the database
		$custom_message = [
			self::$id['paywall_message'] => 'Fully gated',
			self::$id['button_text']     => 'Learn More',
			// Leaving one option set to an empty string becasue this state occurs in the database once a custom message has been deleted
			self::$id['button_link']     => '',
		];
		update_option( 'coil_messaging_settings_group', $custom_message );

		// Creating an array of the messages that were retrieved
		$message = [
			self::$id['paywall_title'] => Admin\get_paywall_text_settings_or_default( self::$id['paywall_title'] ),
			self::$id['paywall_message'] => Admin\get_paywall_text_settings_or_default( self::$id['paywall_message'] ),
			self::$id['button_text']     => Admin\get_paywall_text_settings_or_default( self::$id['button_text'] ),
			self::$id['button_link']     => Admin\get_paywall_text_settings_or_default( self::$id['button_link'] ),
		];

		// Checking that all messages that were retrieved are correct
		$this->assertSame( 'Keep Reading with Coil', $message[ self::$id['paywall_title'] ] );
		$this->assertSame( $custom_message[ self::$id['paywall_message'] ], $message[ self::$id['paywall_message'] ] );
		$this->assertSame( $custom_message[ self::$id['button_text'] ], $message[ self::$id['button_text'] ] );
		$this->assertSame( 'https://coil.com/', $message[ self::$id['button_link'] ] );
	}

	/**
	 * Testing if a user has custom messages which they saved in the customizer that they are migrated successfully to the wp_options table
	 *
	 * @return void
	 */
	public function test_transfer_of_messages_from_customizer() :  void {

		// Adding custom messages to the theme_mod
		set_theme_mod( self::$id['unverified'], 'Unable to verify' );
		set_theme_mod( self::$id['pending'], 'Loading content' );
		// Variable name was changed from coil_unsupported_message to fully_gated
		set_theme_mod( 'coil_unsupported_message', 'Fully gated' );
		set_theme_mod( self::$id['button_text'], 'Learn More' );
		// Leaving one option set to an empty string becasue this state occurs in the database once a custom message has been deleted
		set_theme_mod( self::$id['button_link'], '' );
		// Testing removal of a deprecated message
		set_theme_mod( self::$id['fully_gated_excerpt_message'], 'Fully gated excerpt' );

		// Transferrng settings to the wp_options table
		Settings\transfer_customizer_message_settings();

		// Creating an array of the messages that were retrieved from the wp_options table.
		$message = [
			self::$id['paywall_message'] => Admin\get_paywall_text_settings_or_default( self::$id['paywall_message'] ),
			self::$id['button_text']     => Admin\get_paywall_text_settings_or_default( self::$id['button_text'] ),
			self::$id['button_link']     => Admin\get_paywall_text_settings_or_default( self::$id['button_link'] ),
		];

		// Checking that all messages that were retrieved are correct
		$this->assertSame( 'Fully gated', $message[ self::$id['paywall_message'] ] );
		$this->assertSame( 'Learn More', $message[ self::$id['button_text'] ] );
		$this->assertSame( 'https://coil.com/', $message[ self::$id['button_link'] ] );

		// Checking that the theme_mod messages have been removed
		$this->assertFalse( get_theme_mod( self::$id['unverified'] ) );
		$this->assertFalse( get_theme_mod( 'coil_voluntary_donation_message' ) );
		$this->assertFalse( get_theme_mod( self::$id['pending'] ) );
		$this->assertFalse( get_theme_mod( self::$id['paywall_message'] ) );
		$this->assertFalse( get_theme_mod( self::$id['partially_gated'] ) );
		$this->assertFalse( get_theme_mod( self::$id['button_text'] ) );
		$this->assertFalse( get_theme_mod( self::$id['button_link'] ) );
		$this->assertFalse( get_theme_mod( self::$id['fully_gated_excerpt_message'] ) );

	}

	/**
	 * Testing if a user has custom messages which they saved in the customizer
	 * and custom messages saved in the settings panel that they are migrated successfully to the wp_options table
	 *
	 * @return void
	 */
	public function test_transfer_of_messages_from_customizer_when_messages_also_exist_in_settings_panel() :  void {

		// Adding custom messages to the database from the settings panel
		$settings_panel_messages = [
			self::$id['button_text'] => 'Button text',
		];
		update_option( 'coil_exclusive_settings_group', $settings_panel_messages );

		// Adding custom messages to the theme_mod
		set_theme_mod( self::$id['unverified'], 'Unable to verify' );
		set_theme_mod( self::$id['pending'], 'Loading content' );
		// Variable name was changed from coil_unsupported_message to fully_gated
		set_theme_mod( 'coil_unsupported_message', 'Fully gated' );
		set_theme_mod( self::$id['button_text'], 'Learn More' );
		// Leaving one option set to an empty string becasue this state occurs in the database once a custom message has been deleted
		set_theme_mod( self::$id['button_link'], '' );
		// Testing removal of a deprecated message
		set_theme_mod( self::$id['partially_gated_excerpt_message'], 'Partially gated excerpt' );

		// Transferrng settings to the wp_options table
		Settings\transfer_customizer_message_settings();

		// Creating an array of the messages that were retrieved from the wp_options table.
		$message = [
			self::$id['paywall_message'] => Admin\get_paywall_text_settings_or_default( self::$id['paywall_message'] ),
			self::$id['button_text']     => Admin\get_paywall_text_settings_or_default( self::$id['button_text'] ),
			self::$id['button_link']     => Admin\get_paywall_text_settings_or_default( self::$id['button_link'] ),
		];

		// Checking that all messages that were retrieved are correct
		$this->assertSame( 'Fully gated', $message[ self::$id['paywall_message'] ] );
		$this->assertSame( 'Learn More', $message[ self::$id['button_text'] ] );
		$this->assertSame( 'https://coil.com/', $message[ self::$id['button_link'] ] );

		// Checking that the theme_mod messages have been removed
		$this->assertFalse( get_theme_mod( self::$id['unverified'] ) );
		$this->assertFalse( get_theme_mod( 'coil_voluntary_donation_message' ) );
		$this->assertFalse( get_theme_mod( self::$id['pending'] ) );
		$this->assertFalse( get_theme_mod( self::$id['paywall_message'] ) );
		$this->assertFalse( get_theme_mod( self::$id['partially_gated'] ) );
		$this->assertFalse( get_theme_mod( self::$id['button_text'] ) );
		$this->assertFalse( get_theme_mod( self::$id['button_link'] ) );
		$this->assertFalse( get_theme_mod( self::$id['partially_gated_excerpt_message'] ) );
	}
}
