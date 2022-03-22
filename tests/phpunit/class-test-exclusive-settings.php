<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil\Admin;
use Coil\Gating;
use WP_UnitTestCase;
use WP_UnitTest_Factory;

/**
 * Testing the exclusive settings.
 */
class Test_Exclusive_Settings extends WP_UnitTestCase {

	/**
	 *
	 * @var array
	 * @var \WP_Post[] message ID's.
	*/
	protected static $id = [
		'paywall_title'   => 'coil_paywall_title',
		'paywall_message' => 'coil_paywall_message',
		'button_text'     => 'coil_paywall_button_text',
		'button_link'     => 'coil_paywall_button_link',
	];

	/**
	 * Basic post for testing with.
	 *
	 * @var \WP_Post[] Standard post objects.
	 */
	protected static $example_post = null;

	/**
	 * Create fake data before tests run.
	 *
	 * @param WP_UnitTest_Factory $factory Helper that creates fake data.
	 *
	 * @return void
	 */
	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) : void {

		self::$example_post = $factory->post->create_and_get();
	}

	/**
	 * Delete fake data after tests run.
	 *
	 * @return void
	 */
	public static function wpTearDownAfterClass() : void {

		wp_delete_post( self::$example_post->ID, true );

		$example_post = null;
	}

	/**
	 * Testing if the exclusive content on / off toggle defaults to true.
	 *
	 * @return void
	 */
	public function test_if_default_exclusive_content_is_true() {

		$exclusive_content_enabled = Admin\is_exclusive_content_enabled();

		$this->assertSame( true, $exclusive_content_enabled );
	}

	/**
	 * Testing if the exclusive content on / off toggle setting is retrieved successfully.
	 *
	 * @return void
	 */
	public function test_if_default_exclusive_content_toggle_is_retrieved_successfully() {

		// Ensuring exclusive content has been disabled
		$settings                          = get_option( 'coil_exclusive_settings_group', [] );
		$settings['coil_exclusive_toggle'] = false;
		update_option( 'coil_exclusive_settings_group', $settings );

		$exclusive_content_enabled = Admin\is_exclusive_content_enabled();

		$this->assertSame( false, $exclusive_content_enabled );

		// Ensuring exclusive content has been enabled
		$settings                          = get_option( 'coil_exclusive_settings_group', [] );
		$settings['coil_exclusive_toggle'] = true;
		update_option( 'coil_exclusive_settings_group', $settings );

		$exclusive_content_enabled = Admin\is_exclusive_content_enabled();

		$this->assertSame( true, $exclusive_content_enabled );
	}

	/**
	 * Check that message defaults can be retrieved successfully.
	 *
	 * @return void
	 */
	public function test_retrieving_message_defaults() :  void {

		// Ensuring no custom messages are present in the database
		delete_option( 'coil_exclusive_settings_group' );

		// Creating an array of the message defaults
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
		update_option( 'coil_exclusive_settings_group', $custom_message );

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
	 * Testing if the CTA box's color theme is retrieved correctly from the wp_options table.
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
	 * Testing if the CTA box's branding selection defaults to show the Coil logo.
	 *
	 * @return void
	 */
	public function test_if_default_message_branding_option_is_coil_logo() {

		$branding_setting = Admin\get_paywall_appearance_setting( 'coil_message_branding' );

		$this->assertSame( 'coil_logo', $branding_setting );
	}

	/**
	 * Testing if the CTA box's branding selection is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_message_branding_option_is_retrieved_successfully() {

		$branding_state = [ 'coil_message_branding' => 'site_logo' ];
		update_option( 'coil_exclusive_settings_group', $branding_state );

		$retrieved_branding = Admin\get_paywall_appearance_setting( 'coil_message_branding' );

		$this->assertSame( $branding_state['coil_message_branding'], $retrieved_branding );

		$branding_state = [ 'coil_message_branding' => 'no_logo' ];
		update_option( 'coil_exclusive_settings_group', $branding_state );

		$retrieved_branding = Admin\get_paywall_appearance_setting( 'coil_message_branding' );

		$this->assertSame( $branding_state['coil_message_branding'], $retrieved_branding );

	}

	/**
	 * Testing if the CTA box defaults to the plugin's font rather than inheriting the theme's font.
	 * The font value should be set to false.
	 *
	 * @return void
	 */
	public function test_if_default_theme_font_is_false() {

		$theme_based_font = Admin\get_paywall_appearance_setting( 'coil_message_font' );

		$this->assertSame( false, $theme_based_font );
	}

	/**
	 * Testing if the CTA box's font selection is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_theme_font_setting_is_retrieved_successfully() {

		$theme_based_font = [ 'coil_message_font' => false ];
		update_option( 'coil_exclusive_settings_group', $theme_based_font );

		$retrieved_font = Admin\get_paywall_appearance_setting( 'coil_message_font' );

		$this->assertSame( $theme_based_font['coil_message_font'], $retrieved_font );

		$theme_based_font = [ 'coil_message_font' => true ];
		update_option( 'coil_exclusive_settings_group', $theme_based_font );

		$retrieved_font = Admin\get_paywall_appearance_setting( 'coil_message_font' );

		$this->assertSame( $theme_based_font['coil_message_font'], $retrieved_font );

	}

	/**
	 * Testing if the padlock icon shows next to geted post titles by default.
	 *
	 * @return void
	 */
	public function test_default_padlock_settings() :  void {

		$padlock_display  = Admin\get_exlusive_post_setting( 'coil_title_padlock' );
		$padlock_position = Admin\get_exlusive_post_setting( 'coil_padlock_icon_position' );
		$padlock_style    = Admin\get_exlusive_post_setting( 'coil_padlock_icon_style' );

		$this->assertSame( true, $padlock_display );
		$this->assertSame( 'before', $padlock_position );
		$this->assertSame( 'lock', $padlock_style );
	}

	/**
	 * Testing if the padlock icon setting is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_padlock_settings_can_be_retrieved_successfully() :  void {

		$padlock_settings = [
			'coil_title_padlock'         => false,
			'coil_padlock_icon_position' => 'after',
			'coil_padlock_icon_style'    => 'bonus',
		];
		update_option( 'coil_exclusive_settings_group', $padlock_settings );

		$padlock_display  = Admin\get_exlusive_post_setting( 'coil_title_padlock' );
		$padlock_position = Admin\get_exlusive_post_setting( 'coil_padlock_icon_position' );
		$padlock_style    = Admin\get_exlusive_post_setting( 'coil_padlock_icon_style' );

		$this->assertSame( $padlock_settings['coil_title_padlock'], $padlock_display );
		$this->assertSame( $padlock_settings['coil_padlock_icon_position'], $padlock_position );
		$this->assertSame( $padlock_settings['coil_padlock_icon_style'], $padlock_style );

		$padlock_settings = [
			'coil_title_padlock'         => true,
			'coil_padlock_icon_position' => 'before',
			'coil_padlock_icon_style'    => 'exclusive',
		];
		update_option( 'coil_exclusive_settings_group', $padlock_settings );

		$padlock_display  = Admin\get_exlusive_post_setting( 'coil_title_padlock' );
		$padlock_position = Admin\get_exlusive_post_setting( 'coil_padlock_icon_position' );
		$padlock_style    = Admin\get_exlusive_post_setting( 'coil_padlock_icon_style' );

		$this->assertSame( $padlock_settings['coil_title_padlock'], $padlock_display );
		$this->assertSame( $padlock_settings['coil_padlock_icon_position'], $padlock_position );
		$this->assertSame( $padlock_settings['coil_padlock_icon_style'], $padlock_style );
	}

	/**
	 * Testing if the excerpt display default is false.
	 *
	 * @return void
	 */
	public function test_if_default_excerpt_display_is_false() {

		$excerpt_display = Gating\is_excerpt_visible( self::$example_post->ID );

		$this->assertSame( false, $excerpt_display );
	}

	/**
	 * Testing if the excerpt display setting is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_excerpt_display_setting_is_retrieved_successfully() {

		// Testing case when excerpt display is set to true
		$set_excerpt_display = [ 'post_excerpt' => true ];
		update_option( 'coil_exclusive_settings_group', $set_excerpt_display );

		$retrieved_excerpt_display = Gating\is_excerpt_visible( self::$example_post->ID );

		$this->assertSame( true, $retrieved_excerpt_display );

		// Testing case when excerpt display is set to false
		$set_excerpt_display = [ 'post_excerpt' => false ];
		update_option( 'coil_exclusive_settings_group', $set_excerpt_display );

		$retrieved_excerpt_display = Gating\is_excerpt_visible( self::$example_post->ID );

		$this->assertSame( false, $retrieved_excerpt_display );

	}

	/**
	 * Testing if the default content container can be retrieved successfully from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_default_content_container_is_retrieved_successfully() :  void {

		$css_selector = Admin\get_css_selector();

		$this->assertSame( '.content-area .entry-content', $css_selector );
	}

	/**
	 * Testing if the content container can be retrieved successfully from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_content_container_is_retrieved_successfully() :  void {

		$content_container = [ 'coil_content_container' => '.content-area .entry-content, .post-story' ];
		update_option( 'coil_exclusive_settings_group', $content_container );

		$retrieved_content_container = Admin\get_css_selector();

		$this->assertSame( $content_container['coil_content_container'], $retrieved_content_container );
	}

}
