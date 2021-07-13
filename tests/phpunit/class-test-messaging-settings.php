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

	/**
	 *
	 * @var array
	 * @var \WP_Post[] message ID's.
	*/
	protected static $id = [
		'unverified'                      => 'coil_unable_to_verify_message',
		'donation_bar'                    => 'coil_voluntary_donation_message',
		'pending'                         => 'coil_verifying_status_message',
		'fully_gated'                     => 'coil_fully_gated_content_message',
		'partially_gated'                 => 'coil_partially_gated_content_message',
		'button_text'                     => 'coil_learn_more_button_text',
		'button_link'                     => 'coil_learn_more_button_link',
		'fully_gated_excerpt_message'     => 'coil_fully_gated_excerpt_message',
		'partially_gated_excerpt_message' => 'coil_partially_gated_excerpt_message',

	];

	/**
	 *
	 * @var array
	 * @var \WP_Post[] message default wording.
	*/
	protected static $defaults = [
		'coil_voluntary_donation_message'      => 'This site is monetized using Coil. If you enjoy the content, consider supporting us by signing up for a Coil Membership. Here\'s how…',
		'coil_verifying_status_message'        => 'Verifying Web Monetization status. Please wait...',
		'coil_fully_gated_content_message'     => 'Unlock exclusive content with Coil. Need a Coil account?',
		'coil_partially_gated_content_message' => 'To keep reading, join Coil and install the browser extension. Visit coil.com for more information.',
		'coil_learn_more_button_text'          => 'Get Coil to access',
		'coil_learn_more_button_link'          => 'https://coil.com/',
	];

	/**
	 *
	 * @var array
	 * @var \WP_Post[] examples of custom messages.
	*/
	protected static $example_messages = [
		'coil_voluntary_donation_message'      => 'Please support me by joining Coil!',
		'coil_verifying_status_message'        => 'Please be patient.',
		'coil_fully_gated_content_message'     => 'This is exclusive to Coil members.',
		'coil_partially_gated_content_message' => 'Read more with Coil.',
		'coil_learn_more_button_text'          => 'Coil info',
		'coil_learn_more_button_link'          => 'https://coil.com/about',
	];

	/**
	 * Check that message defaults can be retrieved successfully.
	 *
	 * @return void
	 */
	public function test_retrieving_message_defaults() :  void {

		// Ensuring no custom messages are present in the database
		delete_option( 'coil_messaging_settings_group' );

		// Creating an array of the message defaults that were retrieved
		$retrieved_messages = [
			self::$id['donation_bar']    => Admin\get_messaging_setting_or_default( self::$id['donation_bar'] ),
			self::$id['pending']         => Admin\get_messaging_setting_or_default( self::$id['pending'] ),
			self::$id['fully_gated']     => Admin\get_messaging_setting_or_default( self::$id['fully_gated'] ),
			self::$id['partially_gated'] => Admin\get_messaging_setting_or_default( self::$id['partially_gated'] ),
			self::$id['button_text']     => Admin\get_messaging_setting_or_default( self::$id['button_text'] ),
			self::$id['button_link']     => Admin\get_messaging_setting_or_default( self::$id['button_link'] ),
		];

		// Checking that all defaults are correct
		$this->assertSame( self::$defaults, $retrieved_messages );
	}

	/**
	 * Check that custom messages can be retrieved successfully.
	 *
	 * @return void
	 */
	public function test_retrieving_custom_messages() :  void {

		// Adding custom messages to the database
		update_option( 'coil_messaging_settings_group', self::$example_messages );

		// Creating an array of the messages that were retrieved
		$retrieved_message = [
			self::$id['donation_bar']    => Admin\get_messaging_setting_or_default( self::$id['donation_bar'] ),
			self::$id['pending']         => Admin\get_messaging_setting_or_default( self::$id['pending'] ),
			self::$id['fully_gated']     => Admin\get_messaging_setting_or_default( self::$id['fully_gated'] ),
			self::$id['partially_gated'] => Admin\get_messaging_setting_or_default( self::$id['partially_gated'] ),
			self::$id['button_text']     => Admin\get_messaging_setting_or_default( self::$id['button_text'] ),
			self::$id['button_link']     => Admin\get_messaging_setting_or_default( self::$id['button_link'] ),
		];

		// Checking that all messages that were retrieved are correct
		$this->assertSame( self::$example_messages, $retrieved_message );
	}

	/**
	 * Check that a variety of custom and default messages can be retrieved successfully.
	 *
	 * @return void
	 */
	public function test_retrieving_custom_messages_mixed_with_defaults() :  void {

		// Adding custom messages to the database
		$custom_message = [
			self::$id['pending']     => self::$example_messages[ self::$id['pending'] ],
			self::$id['fully_gated'] => self::$example_messages[ self::$id['fully_gated'] ],
			self::$id['button_text'] => self::$example_messages[ self::$id['button_text'] ],
			// Leaving one option set to an empty string becasue this state occurs in the database once a custom message has been deleted
			self::$id['button_link'] => '',
		];
		update_option( 'coil_messaging_settings_group', $custom_message );

		// Creating an array of the messages that were retrieved
		$message = [
			self::$id['donation_bar']    => Admin\get_messaging_setting_or_default( self::$id['donation_bar'] ),
			self::$id['pending']         => Admin\get_messaging_setting_or_default( self::$id['pending'] ),
			self::$id['fully_gated']     => Admin\get_messaging_setting_or_default( self::$id['fully_gated'] ),
			self::$id['partially_gated'] => Admin\get_messaging_setting_or_default( self::$id['partially_gated'] ),
			self::$id['button_text']     => Admin\get_messaging_setting_or_default( self::$id['button_text'] ),
			self::$id['button_link']     => Admin\get_messaging_setting_or_default( self::$id['button_link'] ),
		];

		// Checking that all messages that were retrieved are correct
		$this->assertSame( self::$defaults[ self::$id['donation_bar'] ], $message[ self::$id['donation_bar'] ] );
		$this->assertSame( $custom_message[ self::$id['pending'] ], $message[ self::$id['pending'] ] );
		$this->assertSame( $custom_message[ self::$id['fully_gated'] ], $message[ self::$id['fully_gated'] ] );
		$this->assertSame( self::$defaults[ self::$id['partially_gated'] ], $message[ self::$id['partially_gated'] ] );
		$this->assertSame( $custom_message[ self::$id['button_text'] ], $message[ self::$id['button_text'] ] );
		$this->assertSame( self::$defaults[ self::$id['button_link'] ], $message[ self::$id['button_link'] ] );
	}

	/**
	 * Testing if a user has custom messages which they saved in the customizer that they are migrated successfully to the wp_options table
	 *
	 * @return void
	 */
	public function test_transfer_of_all_messages_from_customizer() :  void {

		// Adding custom messages to the theme_mod
		set_theme_mod( self::$id['pending'], self::$example_messages[ self::$id['pending'] ] );
		// Variable name was changed from coil_unsupported_message to fully_gated
		set_theme_mod( 'coil_unsupported_message', self::$example_messages[ self::$id['fully_gated'] ] );
		set_theme_mod( self::$id['button_text'], self::$example_messages[ self::$id['button_text'] ] );
		// Leaving one option set to an empty string becasue this state occurs in the database once a custom message has been deleted
		set_theme_mod( self::$id['button_link'], '' );
		set_theme_mod( self::$id['donation_bar'], self::$example_messages[ self::$id['donation_bar'] ] );
		set_theme_mod( 'coil_partial_gating_message', self::$example_messages[ self::$id['partially_gated'] ] );
		// Testing removal of a deprecated message
		set_theme_mod( self::$id['unverified'], 'Unable to verify' );
		set_theme_mod( self::$id['fully_gated_excerpt_message'], 'Fully gated excerpt' );
		set_theme_mod( self::$id['partially_gated_excerpt_message'], 'Fully gated excerpt' );

		// Transferring settings to the wp_options table
		Settings\transfer_customizer_message_settings();

		// Creating an array of the messages that were retrieved from the wp_options table.
		$message = [
			self::$id['donation_bar']    => Admin\get_messaging_setting_or_default( self::$id['donation_bar'] ),
			self::$id['pending']         => Admin\get_messaging_setting_or_default( self::$id['pending'] ),
			self::$id['fully_gated']     => Admin\get_messaging_setting_or_default( self::$id['fully_gated'] ),
			self::$id['partially_gated'] => Admin\get_messaging_setting_or_default( self::$id['partially_gated'] ),
			self::$id['button_text']     => Admin\get_messaging_setting_or_default( self::$id['button_text'] ),
			self::$id['button_link']     => Admin\get_messaging_setting_or_default( self::$id['button_link'] ),
		];

		// Checking that all messages that were retrieved are correct
		$this->assertSame( self::$example_messages[ self::$id['donation_bar'] ], $message[ self::$id['donation_bar'] ] );
		$this->assertSame( self::$example_messages[ self::$id['pending'] ], $message[ self::$id['pending'] ] );
		$this->assertSame( self::$example_messages[ self::$id['fully_gated'] ], $message[ self::$id['fully_gated'] ] );
		$this->assertSame( self::$example_messages[ self::$id['partially_gated'] ], $message[ self::$id['partially_gated'] ] );
		$this->assertSame( self::$example_messages[ self::$id['button_text'] ], $message[ self::$id['button_text'] ] );
		$this->assertSame( self::$defaults[ self::$id['button_link'] ], $message[ self::$id['button_link'] ] );

		// Checking that the theme_mod messages have been removed
		$this->assertFalse( get_theme_mod( self::$id['donation_bar'] ) );
		$this->assertFalse( get_theme_mod( self::$id['pending'] ) );
		$this->assertFalse( get_theme_mod( 'coil_unsupported_message' ) );
		$this->assertFalse( get_theme_mod( 'coil_partial_gating_message' ) );
		$this->assertFalse( get_theme_mod( self::$id['button_text'] ) );
		$this->assertFalse( get_theme_mod( self::$id['button_link'] ) );
		// Checking that deprecated message was removed
		$this->assertFalse( get_theme_mod( self::$id['unverified'] ) );
		$this->assertFalse( get_theme_mod( self::$id['fully_gated_excerpt_message'] ) );
		$this->assertFalse( get_theme_mod( self::$id['partially_gated_excerpt_message'] ) );
	}

	/**
	 * Testing if a user has no custom messages saved that the transfer function still works as expected.
	 *
	 * @return void
	 */
	public function test_transfer_of_no_messages_from_customizer() :  void {

		// Transferring settings to the wp_options table
		Settings\transfer_customizer_message_settings();

		// Creating an array of the messages that were retrieved from the wp_options table.
		$retrieved_messages = [
			self::$id['donation_bar']    => Admin\get_messaging_setting_or_default( self::$id['donation_bar'] ),
			self::$id['pending']         => Admin\get_messaging_setting_or_default( self::$id['pending'] ),
			self::$id['fully_gated']     => Admin\get_messaging_setting_or_default( self::$id['fully_gated'] ),
			self::$id['partially_gated'] => Admin\get_messaging_setting_or_default( self::$id['partially_gated'] ),
			self::$id['button_text']     => Admin\get_messaging_setting_or_default( self::$id['button_text'] ),
			self::$id['button_link']     => Admin\get_messaging_setting_or_default( self::$id['button_link'] ),
		];

		// Checking that all messages that were retrieved are correct
		$this->assertSame( self::$defaults, $retrieved_messages );

		// Checking that the theme_mod messages remained empty
		$this->assertFalse( get_theme_mod( self::$id['donation_bar'] ) );
		$this->assertFalse( get_theme_mod( self::$id['pending'] ) );
		$this->assertFalse( get_theme_mod( 'coil_unsupported_message' ) );
		$this->assertFalse( get_theme_mod( self::$id['partially_gated'] ) );
		$this->assertFalse( get_theme_mod( self::$id['button_text'] ) );
		$this->assertFalse( get_theme_mod( self::$id['button_link'] ) );
		// Checking that deprecated messages are also empty
		$this->assertFalse( get_theme_mod( self::$id['unverified'] ) );
		$this->assertFalse( get_theme_mod( self::$id['fully_gated_excerpt_message'] ) );
		$this->assertFalse( get_theme_mod( self::$id['partially_gated_excerpt_message'] ) );
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
			self::$id['donation_bar'] => self::$example_messages[ self::$id['donation_bar'] ],
			self::$id['pending']      => self::$example_messages[ self::$id['pending'] ],
			self::$id['button_text']  => '',
		];
		update_option( 'coil_messaging_settings_group', $settings_panel_messages );

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

		// Transferring settings to the wp_options table
		Settings\transfer_customizer_message_settings();

		// Creating an array of the messages that were retrieved from the wp_options table.
		$message = [
			self::$id['donation_bar']    => Admin\get_messaging_setting_or_default( self::$id['donation_bar'] ),
			self::$id['pending']         => Admin\get_messaging_setting_or_default( self::$id['pending'] ),
			self::$id['fully_gated']     => Admin\get_messaging_setting_or_default( self::$id['fully_gated'] ),
			self::$id['partially_gated'] => Admin\get_messaging_setting_or_default( self::$id['partially_gated'] ),
			self::$id['button_text']     => Admin\get_messaging_setting_or_default( self::$id['button_text'] ),
			self::$id['button_link']     => Admin\get_messaging_setting_or_default( self::$id['button_link'] ),
		];

		// Checking that all messages that were retrieved are correct
		$this->assertSame( self::$example_messages[ self::$id['donation_bar'] ], $message[ self::$id['donation_bar'] ] );
		$this->assertSame( 'Loading content', $message[ self::$id['pending'] ] );
		$this->assertSame( 'Fully gated', $message[ self::$id['fully_gated'] ] );
		$this->assertSame( self::$defaults[ self::$id['partially_gated'] ], $message[ self::$id['partially_gated'] ] );
		$this->assertSame( 'Learn More', $message[ self::$id['button_text'] ] );
		$this->assertSame( self::$defaults[ self::$id['button_link'] ], $message[ self::$id['button_link'] ] );

		// Checking that the theme_mod messages have been removed
		$this->assertFalse( get_theme_mod( self::$id['donation_bar'] ) );
		$this->assertFalse( get_theme_mod( self::$id['pending'] ) );
		$this->assertFalse( get_theme_mod( 'coil_unsupported_message' ) );
		$this->assertFalse( get_theme_mod( self::$id['partially_gated'] ) );
		$this->assertFalse( get_theme_mod( self::$id['button_text'] ) );
		$this->assertFalse( get_theme_mod( self::$id['button_link'] ) );
		// Checking that deprecated message was removed
		$this->assertFalse( get_theme_mod( self::$id['unverified'] ) );
		$this->assertFalse( get_theme_mod( self::$id['partially_gated_excerpt_message'] ) );
	}

	/**
	 * Check the special case in the transfer process where the unverified fully gated message replaces the unsupported fully gated message.
	 * This happens when the unverified message has been customized and the unsupported message has not.
	 *
	 * @return void
	 */
	public function test_transferring_fully_gated_message() :  void {

		// Adding custom messages to the theme_mod
		set_theme_mod( self::$id['unverified'], 'Unable to verify' );

		// Transferring the fully gated content message to the wp_options table
		Settings\transfer_customizer_message_settings();

		$retrieved_fully_gated_message = Admin\get_messaging_setting_or_default( self::$id['fully_gated'] );

		// Checking that the unverified message has been transferred instead of using the unsupported default message
		$this->assertSame( 'Unable to verify', $retrieved_fully_gated_message );
		// Checking that the theme_mod message was removed
		$this->assertFalse( get_theme_mod( self::$id['unverified'] ) );
	}
}
