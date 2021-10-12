<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil\Admin;
use Coil\Settings;
use WP_UnitTestCase;

/**
 * Testing the functions that transfer data saved in the customizer or outdated option groups in the settings panel.
 */
class Test_Transfer_Functions extends WP_UnitTestCase {

	/*/**
	 *
	 * @var array
	 * @var \WP_Post[] message ID's.
	*/
	protected static $id = [
		'paywall_title'               => 'coil_paywall_title',
		'paywall_message'             => 'coil_paywall_message',
		'button_text'                 => 'coil_paywall_button_text',
		'button_link'                 => 'coil_paywall_button_link',
		'fully_gated_excerpt_message' => 'coil_fully_gated_excerpt_message',
	];

	/**
	 * Testing if a user has custom messages which they saved in the customizer that they are migrated successfully to the wp_options table
	 *
	 * @return void
	 */
	public function test_transfer_of_messages_from_customizer() :  void {

		// Adding custom messages to the theme_mod
		set_theme_mod( 'coil_unable_to_verify_message', 'Unable to verify' );
		set_theme_mod( 'coil_verifying_status_message', 'Loading content' );
		// Variable name was changed from coil_unsupported_message to fully_gated
		set_theme_mod( 'coil_unsupported_message', 'Fully gated' );
		set_theme_mod( 'coil_learn_more_button_text', 'Learn More' );
		// Leaving one option set to an empty string becasue this state occurs in the database once a custom message has been deleted
		set_theme_mod( 'coil_learn_more_button_link', '' );
		// Testing removal of a deprecated message
		set_theme_mod( self::$id['fully_gated_excerpt_message'], 'Fully gated excerpt' );

		// Transferrng settings to the wp_options table
		Settings\maybe_update_database();

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
		$this->assertFalse( get_theme_mod( 'coil_unable_to_verify_message' ) );
		$this->assertFalse( get_theme_mod( 'coil_voluntary_donation_message' ) );
		$this->assertFalse( get_theme_mod( 'coil_verifying_status_message' ) );
		$this->assertFalse( get_theme_mod( 'coil_unsupported_message' ) );
		$this->assertFalse( get_theme_mod( 'coil_partial_gating_message' ) );
		$this->assertFalse( get_theme_mod( 'coil_learn_more_button_text' ) );
		$this->assertFalse( get_theme_mod( 'coil_learn_more_button_link' ) );
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
			self::$id['paywall_title'] => 'Paywall Title',
			self::$id['button_text']   => 'Button text',
		];
		add_option( 'coil_exclusive_settings_group', $settings_panel_messages );

		// Adding custom messages to the theme_mod
		set_theme_mod( 'coil_unable_to_verify_message', 'Unable to verify' );
		set_theme_mod( 'coil_verifying_status_message', 'Loading content' );
		// Variable name was changed from coil_unsupported_message to fully_gated
		set_theme_mod( 'coil_unsupported_message', 'Fully gated' );
		set_theme_mod( 'coil_learn_more_button_text', 'Learn More' );
		// Leaving one option set to an empty string becasue this state occurs in the database once a custom message has been deleted
		set_theme_mod( 'coil_learn_more_button_link', '' );
		// Testing removal of a deprecated message
		set_theme_mod( 'coil_partially_gated_excerpt_message', 'Partially gated excerpt' );

		// Transferrng settings to the wp_options table
		Settings\maybe_update_database();

		// Creating an array of the messages that were retrieved from the wp_options table.
		$message = [
			self::$id['paywall_title']   => Admin\get_paywall_text_settings_or_default( self::$id['paywall_title'] ),
			self::$id['paywall_message'] => Admin\get_paywall_text_settings_or_default( self::$id['paywall_message'] ),
			self::$id['button_text']     => Admin\get_paywall_text_settings_or_default( self::$id['button_text'] ),
			self::$id['button_link']     => Admin\get_paywall_text_settings_or_default( self::$id['button_link'] ),
		];

		// Checking that all messages that were retrieved are correct
		$this->assertSame( 'Paywall Title', $message[ self::$id['paywall_title'] ] );
		$this->assertSame( 'Fully gated', $message[ self::$id['paywall_message'] ] );
		$this->assertSame( 'Learn More', $message[ self::$id['button_text'] ] );
		$this->assertSame( 'https://coil.com/', $message[ self::$id['button_link'] ] );

		// Checking that the theme_mod messages have been removed
		$this->assertFalse( get_theme_mod( 'coil_unable_to_verify_message' ) );
		$this->assertFalse( get_theme_mod( 'coil_voluntary_donation_message' ) );
		$this->assertFalse( get_theme_mod( 'coil_verifying_status_message' ) );
		$this->assertFalse( get_theme_mod( 'coil_unsupported_message' ) );
		$this->assertFalse( get_theme_mod( 'coil_partial_gating_message' ) );
		$this->assertFalse( get_theme_mod( 'coil_learn_more_button_text' ) );
		$this->assertFalse( get_theme_mod( 'coil_learn_more_button_link' ) );
		$this->assertFalse( get_theme_mod( 'coil_partially_gated_excerpt_message' ) );
	}

	/**
	 * Testing if a user has donation bar and padlock display settings which they saved in the customizer that they are migrated successfully to the wp_options table
	 * In the case where both settings had been set in the customizer and no settings had been added to the settings panel.
	 *
	 * @return void
	 */
	public function test_transfer_of_appearance_settings_from_customizer_when_settings_disabled() :  void {

		// Testing when both settings are set to false
		// Adding custom appearance settings to the theme_mod
		set_theme_mod( 'coil_show_donation_bar', false );
		set_theme_mod( 'coil_title_padlock', false );

		// Transferrng settings to the wp_options table
		Settings\maybe_update_database();

		// Creating an array of the appearance settings that were retrieved from the wp_options table.
		$appearance_settings = [
			'coil_show_donation_bar' => Admin\get_coil_button_setting( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_exlusive_post_setting( 'coil_title_padlock' ),
		];

		// Checking that all appearance settings that were retrieved are correct
		$this->assertSame( false, $appearance_settings['coil_show_donation_bar'] );
		$this->assertSame( false, $appearance_settings['coil_title_padlock'] );

		// Checking that the theme_mod appearance settings have been removed
		$this->assertFalse( get_theme_mod( 'coil_show_donation_bar' ) );
		$this->assertFalse( get_theme_mod( 'coil_title_padlock' ) );
	}

	/**
	 * Testing if a user has donation bar and padlock display settings which they saved in the customizer that they are migrated successfully to the wp_options table
	 * In the case where both settings had been set in the customizer and no settings had been added to the settings panel.
	 *
	 * @return void
	 */
	public function test_transfer_of_appearance_settings_from_customizer_when_settings_enabled() :  void {

		// Adding custom appearance settings to the theme_mod
		set_theme_mod( 'coil_show_donation_bar', true );
		set_theme_mod( 'coil_title_padlock', true );

		// Transferrng settings to the wp_options table
		Settings\maybe_update_database();

		// Creating an array of the appearance settings that were retrieved from the wp_options table.
		$appearance_settings = [
			'coil_show_donation_bar' => Admin\get_coil_button_setting( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_exlusive_post_setting( 'coil_title_padlock' ),
		];

		// Checking that all appearance settings that were retrieved are correct
		$this->assertSame( true, $appearance_settings['coil_show_donation_bar'] );
		$this->assertSame( true, $appearance_settings['coil_title_padlock'] );

		// Checking that the theme_mod appearance settings have been removed
		$this->assertFalse( get_theme_mod( 'coil_show_donation_bar' ) );
		$this->assertFalse( get_theme_mod( 'coil_title_padlock' ) );
	}

	/**
	 * Testing if a user has donation bar and padlock display settings which they saved in the customizer that they are migrated successfully to the wp_options table
	 * In the case where both settings had been set in the customizer and no settings had been added to the settings panel.
	 *
	 * @return void
	 */
	public function test_transfer_of_appearance_settings_from_customizer_when_settings_are_mixed() :  void {

		// Testing when one setting is set to true and the other to false
		// Adding custom appearance settings to the theme_mod
		set_theme_mod( 'coil_show_donation_bar', true );
		set_theme_mod( 'coil_title_padlock', false );

		// Transferrng settings to the wp_options table
		Settings\maybe_update_database();

		// Creating an array of the appearance settings that were retrieved from the wp_options table.
		$appearance_settings = [
			'coil_show_donation_bar' => Admin\get_coil_button_setting( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_exlusive_post_setting( 'coil_title_padlock' ),
		];

		// Checking that all appearance settings that were retrieved are correct
		$this->assertSame( true, $appearance_settings['coil_show_donation_bar'] );
		$this->assertSame( false, $appearance_settings['coil_title_padlock'] );

		// Checking that the theme_mod appearance settings have been removed
		$this->assertFalse( get_theme_mod( 'coil_show_donation_bar' ) );
		$this->assertFalse( get_theme_mod( 'coil_title_padlock' ) );
	}

	/**
	 * Testing if a user has donation bar and padlock display settings which they saved in the customizer that they are migrated successfully to the wp_options table
	 * In cases where some settings are saved to the customizer and others have been saved to the settings panel.
	 *
	 * @return void
	 */
	public function test_transfer_of_appearance_settings_from_customizer_where_settings_have_been_saved_in_both_locations() :  void {

		// Adding custom appearance settings to the database and theme_mod
		delete_option( 'coil_exclusive_settings_group' );
		add_option( 'coil_exclusive_settings_group', [ 'coil_title_padlock' => false ] );
		set_theme_mod( 'coil_show_donation_bar', false );

		// Transferrng settings to the wp_options table
		Settings\maybe_update_database();

		// Creating an array of the appearance settings that were retrieved from the wp_options table.
		$appearance_settings = [
			'coil_show_donation_bar' => Admin\get_coil_button_setting( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_exlusive_post_setting( 'coil_title_padlock' ),
		];

		// Checking that all appearance settings that were retrieved are correct
		$this->assertSame( false, $appearance_settings['coil_show_donation_bar'] );
		$this->assertSame( false, $appearance_settings['coil_title_padlock'] );

		// Checking that the theme_mod appearance settings have been removed
		$this->assertFalse( get_theme_mod( 'coil_show_donation_bar' ) );
		$this->assertFalse( get_theme_mod( 'coil_title_padlock' ) );
	}
}
