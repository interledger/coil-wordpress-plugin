<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil;
use Coil\Admin;
use WP_UnitTestCase;

/**
 * Testing the streaming support widget settings.
 */
class Test_Coil_Button_Settings extends WP_UnitTestCase {

	/**
	 * Testing if the streaming support widget shows by default.
	 *
	 * @return void
	 */
	public function test_if_default_streaming_widget_display_is_enabled() :  void {

		// Database defaults must first be setup
		Coil\maybe_update_database();
		$defaults        = Admin\get_streaming_widget_defaults();
		$retrieved_value = Admin\get_streaming_widget_setting( 'streaming_widget_toggle' );

		// The default is true
		$this->assertSame( $defaults['streaming_widget_toggle'], true );
		$this->assertSame( $defaults['streaming_widget_toggle'], $retrieved_value );
	}

	/**
	 * Testing if the streaming support widget display setting is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_streaming_widget_display_setting_is_retrieved_successfully() :  void {

		$streaming_widget_display = [ 'streaming_widget_toggle' => false ];
		update_option( 'streaming_widget_settings_group', $streaming_widget_display );

		$streaming_widget_settings = Admin\get_streaming_widget_setting( 'streaming_widget_toggle' );

		$this->assertSame( false, $streaming_widget_settings );

		$streaming_widget_display = [ 'streaming_widget_toggle' => true ];
		update_option( 'streaming_widget_settings_group', $streaming_widget_display );

		$streaming_widget_settings = Admin\get_streaming_widget_setting( 'streaming_widget_toggle' );

		$this->assertSame( true, $streaming_widget_settings );
	}

	/**
	 * Testing the streaming support widget message and link defaults.
	 *
	 * @return void
	 */
	public function test_streaming_widget_message_and_link_defaults() :  void {
		// Database defaults must first be setup
		Coil\maybe_update_database();
		$default_values  = Admin\get_streaming_widget_defaults();
		$retrieved_value = [
			'default_streaming_widget_text'   => Admin\get_streaming_widget_setting( 'streaming_widget_text', true ),
			'default_streaming_widget_link'   => Admin\get_streaming_widget_setting( 'streaming_widget_link', true ),
			'default_coil_member_button_text' => Admin\get_streaming_widget_setting( 'members_streaming_widget_text', true ),
		];

		$this->assertSame( $default_values['streaming_widget_text'], 'Support us with Coil' );
		$this->assertSame( $default_values['streaming_widget_text'], $retrieved_value['default_streaming_widget_text'] );
		$this->assertSame( $default_values['streaming_widget_link'], 'https://coil.com/' );
		$this->assertSame( $default_values['streaming_widget_link'], $retrieved_value['default_streaming_widget_link'] );
		$this->assertSame( $default_values['members_streaming_widget_text'], 'Thanks for your support!' );
		$this->assertSame( $default_values['members_streaming_widget_text'], $retrieved_value['default_coil_member_button_text'] );
	}

	/**
	 * Testing if the streaming support widget message and link settings are retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_streaming_widget_message_and_link_settings_are_retrieved_successfully() :  void {
		// Set custom streaming support widget messages
		$streaming_widget_settings = [
			'streaming_widget_text'         => 'Coil Eyes Only',
			'streaming_widget_link'         => 'https://example.com',
			'members_streaming_widget_text' => 'Thanks!',
		];
		update_option( 'streaming_widget_settings_group', $streaming_widget_settings );

		$button_text        = Admin\get_streaming_widget_setting( 'streaming_widget_text' );
		$button_link        = Admin\get_streaming_widget_setting( 'streaming_widget_link' );
		$member_button_text = Admin\get_streaming_widget_setting( 'members_streaming_widget_text' );

		$this->assertSame( $streaming_widget_settings['streaming_widget_text'], $button_text );
		$this->assertSame( $streaming_widget_settings['streaming_widget_link'], $button_link );
		$this->assertSame( $streaming_widget_settings['members_streaming_widget_text'], $member_button_text );
	}

	/**
	 * Testing the streaming support widget shows to members by default.
	 *
	 * @return void
	 */
	public function test_streaming_widget_member_display_default() :  void {
		// Database defaults must first be setup
		Coil\maybe_update_database();
		$defaults        = Admin\get_streaming_widget_defaults();
		$retrieved_value = Admin\get_streaming_widget_setting( 'streaming_widget_member_display' );

		// The default is true
		$this->assertSame( $defaults['streaming_widget_member_display'], true );
		$this->assertSame( $defaults['streaming_widget_member_display'], $retrieved_value );
	}

	/**
	 * Testing if the streaming support widget member display option can be retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_streaming_widget_member_display_setting_can_be_retrieved_successfully() :  void {
		// Set the streaming support widget to show for Coil members
		$streaming_widget_settings = [ 'streaming_widget_member_display' => false ];
		update_option( 'streaming_widget_settings_group', $streaming_widget_settings );

		$member_button_display = Admin\get_streaming_widget_setting( 'streaming_widget_member_display' );

		$this->assertSame( false, $member_button_display );

		// Set the streaming support widget to be hidden from Coil members
		$streaming_widget_settings = [ 'streaming_widget_member_display' => true ];
		update_option( 'streaming_widget_settings_group', $streaming_widget_settings );

		$member_button_display = Admin\get_streaming_widget_setting( 'streaming_widget_member_display' );

		$this->assertSame( true, $member_button_display );
	}

	/**
	 * Testing the streaming support widget color theme default.
	 *
	 * @return void
	 */
	public function test_streaming_widget_color_theme_default() :  void {
		// Database defaults must first be setup
		Coil\maybe_update_database();
		$defaults        = Admin\get_streaming_widget_defaults();
		$retrieved_value = Admin\get_streaming_widget_setting( 'streaming_widget_color_theme' );

		// The default is dark
		$this->assertSame( $defaults['streaming_widget_color_theme'], 'dark' );
		$this->assertSame( $defaults['streaming_widget_color_theme'], $retrieved_value );
	}

	/**
	 * Testing if the streaming support widget color theme option can be retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_streaming_widget_color_theme_can_be_retrieved_successfully() :  void {
		// Set the streaming support widget theme as light
		$streaming_widget_settings = [ 'streaming_widget_color_theme' => 'light' ];
		update_option( 'streaming_widget_settings_group', $streaming_widget_settings );

		$retrieved_color_theme = Admin\get_streaming_widget_setting( 'streaming_widget_color_theme' );

		$this->assertSame( 'light', $retrieved_color_theme );

		// Set the streaming support widget theme as dark
		$streaming_widget_settings = [ 'streaming_widget_color_theme' => 'dark' ];
		update_option( 'streaming_widget_settings_group', $streaming_widget_settings );

		$retrieved_color_theme = Admin\get_streaming_widget_setting( 'streaming_widget_color_theme' );

		$this->assertSame( 'dark', $retrieved_color_theme );
	}

	/**
	 * Testing the streaming support widget size default.
	 *
	 * @return void
	 */
	public function test_streaming_widget_size_default() :  void {
		// Database defaults must first be setup
		Coil\maybe_update_database();
		$defaults        = Admin\get_streaming_widget_defaults();
		$retrieved_value = Admin\get_streaming_widget_setting( 'streaming_widget_size' );

		// The default is large
		$this->assertSame( $defaults['streaming_widget_size'], 'large' );
		$this->assertSame( $defaults['streaming_widget_size'], $retrieved_value );
	}

	/**
	 * Testing if the streaming support widget size can be retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_streaming_widget_size_can_be_retrieved_successfully() :  void {
		// Set the streaming support widget to be small
		$streaming_widget_settings = [ 'streaming_widget_size' => 'small' ];
		update_option( 'streaming_widget_settings_group', $streaming_widget_settings );

		$retrieved_widget_size = Admin\get_streaming_widget_setting( 'streaming_widget_size' );

		$this->assertSame( 'small', $retrieved_widget_size );

		// Set the streaming support widget to be large
		$streaming_widget_settings = [ 'streaming_widget_size' => 'large' ];
		update_option( 'streaming_widget_settings_group', $streaming_widget_settings );

		$retrieved_widget_size = Admin\get_streaming_widget_setting( 'streaming_widget_size' );

		$this->assertSame( 'large', $retrieved_widget_size );
	}

	/**
	 * Testing the streaming support widget position default.
	 *
	 * @return void
	 */
	public function test_streaming_widget_position_default() :  void {
		// Database defaults must first be setup
		Coil\maybe_update_database();
		$defaults        = Admin\get_streaming_widget_defaults();
		$retrieved_value = Admin\get_streaming_widget_setting( 'streaming_widget_position' );

		// The default is bottom-right
		$this->assertSame( $defaults['streaming_widget_position'], 'bottom-right' );
		$this->assertSame( $defaults['streaming_widget_position'], $retrieved_value );
	}

	/**
	 * Testing if the streaming support widget position can be retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_streaming_widget_position_can_be_retrieved_successfully() :  void {
		// Set the streaming support widget position to top-right
		$streaming_widget_settings = [ 'streaming_widget_position' => 'top-right' ];
		update_option( 'streaming_widget_settings_group', $streaming_widget_settings );

		$retrieved_button_position = Admin\get_streaming_widget_setting( 'streaming_widget_position' );

		$this->assertSame( 'top-right', $retrieved_button_position );

		// Set the streaming support widget position to bottom-left
		$streaming_widget_settings = [ 'streaming_widget_position' => 'bottom-left' ];
		update_option( 'streaming_widget_settings_group', $streaming_widget_settings );

		$retrieved_button_position = Admin\get_streaming_widget_setting( 'streaming_widget_position' );

		$this->assertSame( 'bottom-left', $retrieved_button_position );
	}

	/**
	 * Testing the streaming support widget margin defaults.
	 *
	 * @return void
	 */
	public function test_streaming_widget_margin_defaults() :  void {
		// Database defaults must first be setup
		Coil\maybe_update_database();
		$expected_values = [
			'streaming_widget_top_margin'    => '',
			'streaming_widget_right_margin'  => '',
			'streaming_widget_bottom_margin' => '',
			'streaming_widget_left_margin'   => '',
		];
		$retrieved_value = [
			'streaming_widget_top_margin'    => Admin\get_streaming_widget_setting( 'streaming_widget_top_margin' ),
			'streaming_widget_right_margin'  => Admin\get_streaming_widget_setting( 'streaming_widget_right_margin' ),
			'streaming_widget_bottom_margin' => Admin\get_streaming_widget_setting( 'streaming_widget_bottom_margin' ),
			'streaming_widget_left_margin'   => Admin\get_streaming_widget_setting( 'streaming_widget_left_margin' ),
		];

		// The default is always '-'
		$this->assertSame( $expected_values['streaming_widget_top_margin'], $retrieved_value['streaming_widget_top_margin'] );
		$this->assertSame( $expected_values['streaming_widget_right_margin'], $retrieved_value['streaming_widget_right_margin'] );
		$this->assertSame( $expected_values['streaming_widget_bottom_margin'], $retrieved_value['streaming_widget_bottom_margin'] );
		$this->assertSame( $expected_values['streaming_widget_left_margin'], $retrieved_value['streaming_widget_left_margin'] );
	}

	/**
	 * Testing if the streaming support widget margins can be retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_streaming_widget_margins_can_be_retrieved_successfully() :  void {
		// Set the streaming support widget to have custom margin sizes
		$streaming_widget_settings = [
			'streaming_widget_top_margin'    => '0',
			'streaming_widget_right_margin'  => '5',
			'streaming_widget_bottom_margin' => 'abc', // incorrect input
			'streaming_widget_left_margin'   => '', // no input
		];
		update_option( 'streaming_widget_settings_group', $streaming_widget_settings );

		$retrieved_margin_settings = [
			'streaming_widget_top_margin'    => Admin\get_streaming_widget_setting( 'streaming_widget_top_margin' ),
			'streaming_widget_right_margin'  => Admin\get_streaming_widget_setting( 'streaming_widget_right_margin' ),
			'streaming_widget_bottom_margin' => Admin\get_streaming_widget_setting( 'streaming_widget_bottom_margin' ),
			'streaming_widget_left_margin'   => Admin\get_streaming_widget_setting( 'streaming_widget_left_margin' ),
		];

		$this->assertSame( '0', $retrieved_margin_settings['streaming_widget_top_margin'] );
		$this->assertSame( $streaming_widget_settings['streaming_widget_right_margin'], $retrieved_margin_settings['streaming_widget_right_margin'] );
		// When invalid input is given a null string is returned
		$this->assertSame( '', $retrieved_margin_settings['streaming_widget_bottom_margin'] );
		$this->assertSame( $streaming_widget_settings['streaming_widget_left_margin'], $retrieved_margin_settings['streaming_widget_left_margin'] );
	}

	/**
	 * Testing if the streaming support widget post-type visibility settings can be retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_streaming_widget_visibility_settings_can_be_retrieved_successfully() :  void {
		// Create a post
		$post_obj = self::factory()->post->create_and_get();

		// Set the streaming support widget to show on posts
		$streaming_widget_settings = [ 'post_streaming_widget_visibility' => 'show' ];
		update_option( 'streaming_widget_settings_group', $streaming_widget_settings );

		$button_status = Admin\get_streaming_widget_status( $post_obj->ID );

		$this->assertSame( 'show-streaming-widget', $button_status );

		// Set the streaming support widget to be hidden on posts
		$streaming_widget_settings = [ 'post_streaming_widget_visibility' => 'hide' ];
		update_option( 'streaming_widget_settings_group', $streaming_widget_settings );

		$button_status = Admin\get_streaming_widget_status( $post_obj->ID );

		$this->assertSame( '', $button_status );
	}
}
