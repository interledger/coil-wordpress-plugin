<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil\Admin;
use WP_UnitTestCase;

/**
 * Testing the exclusive settings.
 */
class Test_Exclusive_Settings extends WP_UnitTestCase {

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
			self::$id['paywall_message'] => 'We partnered with Coil to offer exclusive content. Access this and other great content with a Coil membership.',
			self::$id['button_text']     => 'Become a Coil Member',
			self::$id['button_link']     => 'https://coil.com/',
		];

		// Creating an array of the message defaults that were retrieved
		$retrieved_messages = [
			self::$id['paywall_title']   => Admin\get_paywall_text_settings_or_default( self::$id['paywall_title'] ),
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
		delete_option( 'coil_exclusive_settings_group' );
		add_option( 'coil_exclusive_settings_group', $custom_message );

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
		delete_option( 'coil_exclusive_settings_group' );
		add_option( 'coil_exclusive_settings_group', $custom_message );

		// Creating an array of the messages that were retrieved
		$message = [
			self::$id['paywall_title']   => Admin\get_paywall_text_settings_or_default( self::$id['paywall_title'] ),
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
	 * Testing if the CTA box's default color theme is set to 'light'.
	 *
	 * @return void
	 */
	public function test_if_default_message_theme_is_light() {

		$theme_setting = Admin\get_paywall_appearance_setting( 'coil_message_color_theme' );

		$this->assertSame( 'light', $theme_setting );
	}

	/**
	 * Testing if the CTA box's default color theme is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_color_theme_setting_is_retrieved_successfully() {

		$dark_color_theme = [ 'coil_message_color_theme' => 'dark' ];
		update_option( 'coil_exclusive_settings_group', $dark_color_theme );

		$retrieved_color_theme = Admin\get_paywall_appearance_setting( 'coil_message_color_theme' );

		$this->assertSame( $dark_color_theme['coil_message_color_theme'], $retrieved_color_theme );

		$light_color_theme = [ 'coil_message_color_theme' => 'light' ];
		update_option( 'coil_exclusive_settings_group', $light_color_theme );

		$retrieved_color_theme = Admin\get_paywall_appearance_setting( 'coil_message_color_theme' );

		$this->assertSame( $light_color_theme['coil_message_color_theme'], $retrieved_color_theme );

	}

	/**
	 * Testing if the CTA box's Coil branding selection is set to false.
	 *
	 * @return void
	 */
	public function test_if_default_message_branding_option_is_false() {

		$branding_setting = Admin\get_paywall_appearance_setting( 'coil_message_branding' );

		$this->assertSame( 'coil_logo', $branding_setting );
	}

	/**
	 * Testing if the CTA box's Coil branding selection is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_message_branding_option_is_retrieved_successfully() {

		$branding_unchecked = [ 'coil_message_branding' => false ];
		update_option( 'coil_exclusive_settings_group', $branding_unchecked );

		$retrieved_branding = Admin\get_paywall_appearance_setting( 'coil_message_branding' );

		$this->assertSame( $branding_unchecked['coil_message_branding'], $retrieved_branding );

		$branding_checked = [ 'coil_message_branding' => true ];
		update_option( 'coil_exclusive_settings_group', $branding_checked );

		$retrieved_branding = Admin\get_paywall_appearance_setting( 'coil_message_branding' );

		$this->assertSame( $branding_checked['coil_message_branding'], $retrieved_branding );

	}

	/**
	 * Testing if the CTA box's default font is set to false.
	 *
	 * @return void
	 */
	public function test_if_default_theme_font_is_false() {

		$default_font = Admin\get_inherited_font_setting( 'coil_message_font' );

		$this->assertSame( false, $default_font );
	}

	/**
	 * Testing if the CTA box's font selection is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_theme_font_setting_is_retrieved_successfully() {

		$default_font = [ 'coil_message_font' => false ];
		update_option( 'coil_exclusive_settings_group', $default_font );

		$retrieved_font = Admin\get_inherited_font_setting( 'coil_message_font' );

		$this->assertSame( $default_font['coil_message_font'], $retrieved_font );

		$theme_based_font = [ 'coil_message_font' => true ];
		update_option( 'coil_exclusive_settings_group', $theme_based_font );

		$retrieved_color_theme = Admin\get_inherited_font_setting( 'coil_message_font' );

		$this->assertSame( $theme_based_font['coil_message_font'], $retrieved_color_theme );

	}

	/**
	 * Testing if the padlock icon shows next to geted post titles by default.
	 *
	 * @return void
	 */
	public function test_if_default_padlock_display_is_enabled() :  void {

		$padlock_setting = Admin\get_exlusive_post_setting( 'coil_title_padlock' );

		$this->assertSame( true, $padlock_setting );
	}

	/**
	 * Testing if the padlock icon setting is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_padlock_display_setting_is_retrieved_successfully() :  void {

		$padlock_display = [ 'coil_title_padlock' => false ];
		update_option( 'coil_exclusive_settings_group', $padlock_display );

		$padlock_setting = Admin\get_exlusive_post_setting( 'coil_title_padlock' );

		$this->assertSame( false, $padlock_setting );

		$padlock_display = [ 'coil_title_padlock' => true ];
		update_option( 'coil_exclusive_settings_group', $padlock_display );

		$padlock_setting = Admin\get_exlusive_post_setting( 'coil_title_padlock' );

		$this->assertSame( true, $padlock_setting );
	}

	/**
	 * Testing if the default content container can be retrieved successfully from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_default_content_container_is_retrieved_successfully() :  void {

		$this->assertSame( '.content-area .entry-content', Admin\get_css_selector( 'coil_content_container' ) );
	}

	/**
	 * Testing if the content container can be retrieved successfully from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_content_container_is_retrieved_successfully() :  void {

		$content_container = [ 'coil_content_container' => '.content-area .entry-content, .post-story' ];
		update_option( 'coil_exclusive_settings_group', $content_container );

		$retrieved_content_container = Admin\get_css_selector( 'coil_content_container' );

		$this->assertSame( $content_container['coil_content_container'], $retrieved_content_container );
	}

}
