<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil;
use Coil\Admin;
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
		Coil\maybe_update_database();

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
		update_option( 'coil_exclusive_settings_group', $settings_panel_messages );

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
		Coil\maybe_update_database();

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
	 *
	 * @return void
	 */
	public function test_transfer_of_appearance_settings_from_customizer() :  void {

		// Testing when both settings are set to false
		// Adding custom appearance settings to the theme_mod
		set_theme_mod( 'coil_show_donation_bar', true ); // Deprecated
		set_theme_mod( 'coil_title_padlock', false );

		// Transferring settings to the wp_options table
		Coil\maybe_update_database();

		// Creating an array of the appearance settings that were retrieved from the wp_options table.
		$appearance_settings = [
			'coil_title_padlock' => Admin\get_exlusive_post_setting( 'coil_title_padlock' ),
		];

		// Checking that all appearance settings that were retrieved are correct
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
		update_option( 'coil_exclusive_settings_group', [ 'coil_title_padlock' => false ] );
		set_theme_mod( 'coil_show_donation_bar', false ); // Deprecated

		// Transferrng settings to the wp_options table
		Coil\maybe_update_database();

		// Creating an array of the appearance settings that were retrieved from the wp_options table.
		$appearance_settings = [
			'coil_title_padlock' => Admin\get_exlusive_post_setting( 'coil_title_padlock' ),
		];

		// Checking that all appearance settings that were retrieved are correct
		$this->assertSame( false, $appearance_settings['coil_title_padlock'] );

		// Checking that the theme_mod appearance settings have been removed
		$this->assertFalse( get_theme_mod( 'coil_show_donation_bar' ) );
		$this->assertFalse( get_theme_mod( 'coil_title_padlock' ) );
	}

	/**
	 * Testing migration of messages stored in coil_messaging_settings_group.
	 *
	 * @return void
	 */
	public function test_transfer_of_coil_message_settings_group() :  void {

		// Messages saved in the deprecated option group
		$original_messages = [
			'coil_fully_gated_content_message' => 'Fully gated',
			'coil_learn_more_button_text'      => 'Learn more',
			'coil_learn_more_button_link'      => 'coil.com',
		];
		update_option( 'coil_messaging_settings_group', $original_messages );

		// Transferrng settings
		Coil\maybe_update_database();

		// Creating an array of the messages that were retrieved.
		$retrieved_messages = [
			'coil_paywall_message'     => Admin\get_paywall_text_settings_or_default( 'coil_paywall_message' ),
			'coil_paywall_button_text' => Admin\get_paywall_text_settings_or_default( 'coil_paywall_button_text' ),
			'coil_paywall_button_link' => Admin\get_paywall_text_settings_or_default( 'coil_paywall_button_link' ),
		];

		// Checking that all messages that were retrieved are correct
		$this->assertSame( $original_messages['coil_fully_gated_content_message'], $retrieved_messages['coil_paywall_message'] );
		$this->assertSame( $original_messages['coil_learn_more_button_text'], $retrieved_messages['coil_paywall_button_text'] );
		$this->assertSame( $original_messages['coil_learn_more_button_link'], $retrieved_messages['coil_paywall_button_link'] );

		// Checking that the coil_messaging_settings_group option group has been removed
		$this->assertFalse( get_option( 'coil_messaging_settings_group' ) );
	}

	/**
	 * Testing migration of payment pointer and CSS selector stored in coil_global_settings_group.
	 *
	 * @return void
	 */
	public function test_transfer_of_coil_global_settings_group() :  void {

		// Settings saved in the deprecated option group
		$original_settings = [
			'coil_payment_pointer_id' => 'Fully gated',
			'coil_content_container'  => 'Learn more',
		];
		update_option( 'coil_global_settings_group', $original_settings );

		// Transferrng settings
		Coil\maybe_update_database();

		// Creating an array of the settings that were retrieved.
		$retrieved_settings = [
			'coil_payment_pointer_id' => Admin\get_payment_pointer_setting(),
			'coil_content_container'  => Admin\get_css_selector(),
		];

		// Checking that all settings that were retrieved are correct
		$this->assertSame( $original_settings['coil_payment_pointer_id'], $retrieved_settings['coil_payment_pointer_id'] );
		$this->assertSame( $original_settings['coil_content_container'], $retrieved_settings['coil_content_container'] );

		// Checking that the coil_global_settings_group option group has been removed
		$this->assertFalse( get_option( 'coil_global_settings_group' ) );
	}

	/**
	 * Testing migration of monetization & visibility settings stored in coil_content_settings_posts_group.
	 *
	 * @return void
	 */
	public function test_transfer_of_coil_content_settings_posts_group() :  void {

		// Settings saved in the deprecated option group
		$original_settings = [
			'post' => 'gate-all',
			'page' => 'no',
		];
		update_option( 'coil_content_settings_posts_group', $original_settings );

		// Transferrng settings
		Coil\maybe_update_database();

		// Retrieved settings
		$exclusive_settings = Admin\get_exclusive_settings();
		$general_settings   = Admin\get_general_settings();

		// Retrieved statuses
		$post_monetization = $general_settings['post_monetization'];
		$post_visibility   = $exclusive_settings['post_visibility'];
		$page_monetization = $general_settings['page_monetization'];
		$page_visibility   = $exclusive_settings['page_visibility'];

		// Checking that all settings that were retrieved are correct
		$this->assertSame( 'monetized', $post_monetization );
		$this->assertSame( 'exclusive', $post_visibility );
		$this->assertSame( 'not-monetized', $page_monetization );
		$this->assertSame( 'public', $page_visibility );

		// Checking that the coil_content_settings_posts_group option group has been removed
		$this->assertFalse( get_option( 'coil_content_settings_posts_group' ) );
	}

	/**
	 * Testing migration of excerpt display settings stored in coil_content_settings_excerpt_group.
	 *
	 * @return void
	 */
	public function test_transfer_of_coil_content_settings_excerpt_group() :  void {

		// Settings saved in the deprecated option group
		$original_settings = [
			'post' => true,
			'page' => false,
		];
		update_option( 'coil_content_settings_excerpt_group', $original_settings );

		// Transferrng settings
		Coil\maybe_update_database();

		// Creating an array of the settings that were retrieved.
		$exclusive_options  = Admin\get_exclusive_settings();
		$retrieved_settings = [
			'post_excerpt' => $exclusive_options['post_excerpt'],
			'page_excerpt' => $exclusive_options['page_excerpt'],
		];

		// Checking that all settings that were retrieved are correct
		$this->assertSame( $original_settings['post'], $retrieved_settings['post_excerpt'] );
		$this->assertSame( $original_settings['page'], $retrieved_settings['page_excerpt'] );

		// Checking that the coil_content_settings_excerpt_group option group has been removed
		$this->assertFalse( get_option( 'coil_content_settings_excerpt_group' ) );
	}

	/**
	 * Testing migration of appearance settings stored in coil_appearance_settings_group.
	 *
	 * @return void
	 */
	public function test_transfer_of_coil_appearance_settings_group() :  void {

		// Settings saved in the deprecated option group
		$original_settings = [
			'coil_show_promotion_bar' => true, // Deprecated
			'coil_title_padlock'      => false,
		];
		update_option( 'coil_appearance_settings_group', $original_settings );

		// Transferrng settings
		Coil\maybe_update_database();

		// Creating an array of the settings that were retrieved.
		$retrieved_settings = [
			'coil_title_padlock' => Admin\get_exlusive_post_setting( 'coil_title_padlock' ),
		];

		// Checking that all settings that were retrieved are correct
		$this->assertSame( $original_settings['coil_title_padlock'], $retrieved_settings['coil_title_padlock'] );

		// Checking that the coil_appearance_settings_group option group has been removed
		$this->assertFalse( get_option( 'coil_appearance_settings_group' ) );
	}

}
