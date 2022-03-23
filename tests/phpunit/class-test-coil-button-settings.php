<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil;
use Coil\Admin;
use WP_UnitTestCase;

/**
 * Testing the Coil button settings.
 */
class Test_Coil_Button_Settings extends WP_UnitTestCase {

	/**
	 * Testing if the Coil button shows by default.
	 *
	 * @return void
	 */
	public function test_if_default_coil_button_display_is_enabled() :  void {

		// Database defaults must first be setup
		Coil\maybe_update_database();
		$defaults        = Admin\get_coil_button_defaults();
		$retrieved_value = Admin\get_coil_button_setting( 'coil_button_toggle' );

		// The default is true
		$this->assertSame( $defaults['coil_button_toggle'], true );
		$this->assertSame( $defaults['coil_button_toggle'], $retrieved_value );
	}

	/**
	 * Testing if the Coil button display setting is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_coil_button_display_setting_is_retrieved_successfully() :  void {

		$coil_button_display = [ 'coil_button_toggle' => false ];
		update_option( 'coil_button_settings_group', $coil_button_display );

		$coil_button_settings = Admin\get_coil_button_setting( 'coil_button_toggle' );

		$this->assertSame( false, $coil_button_settings );

		$coil_button_display = [ 'coil_button_toggle' => true ];
		update_option( 'coil_button_settings_group', $coil_button_display );

		$coil_button_settings = Admin\get_coil_button_setting( 'coil_button_toggle' );

		$this->assertSame( true, $coil_button_settings );
	}

	/**
	 * Testing the Coil button message and link defaults.
	 *
	 * @return void
	 */
	public function test_coil_button_message_and_link_defaults() :  void {
		// Database defaults must first be setup
		Coil\maybe_update_database();
		$default_values  = Admin\get_coil_button_defaults();
		$retrieved_value = [
			'default_coil_button_text'        => Admin\get_coil_button_setting( 'coil_button_text', true ),
			'default_coil_button_link'        => Admin\get_coil_button_setting( 'coil_button_link', true ),
			'default_coil_member_button_text' => Admin\get_coil_button_setting( 'coil_members_button_text', true ),
		];

		$this->assertSame( $default_values['coil_button_text'], 'Support us with Coil' );
		$this->assertSame( $default_values['coil_button_text'], $retrieved_value['default_coil_button_text'] );
		$this->assertSame( $default_values['coil_button_link'], 'https://coil.com/' );
		$this->assertSame( $default_values['coil_button_link'], $retrieved_value['default_coil_button_link'] );
		$this->assertSame( $default_values['coil_members_button_text'], 'Thanks for your support!' );
		$this->assertSame( $default_values['coil_members_button_text'], $retrieved_value['default_coil_member_button_text'] );
	}

	/**
	 * Testing if the Coil button message and link settings are retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_coil_button_message_and_link_settings_are_retrieved_successfully() :  void {
		// Set custom Coil button messages
		$coil_button_settings = [
			'coil_button_text'         => 'Coil Eyes Only',
			'coil_button_link'         => 'https://example.com',
			'coil_members_button_text' => 'Thanks!',
		];
		update_option( 'coil_button_settings_group', $coil_button_settings );

		$button_text        = Admin\get_coil_button_setting( 'coil_button_text' );
		$button_link        = Admin\get_coil_button_setting( 'coil_button_link' );
		$member_button_text = Admin\get_coil_button_setting( 'coil_members_button_text' );

		$this->assertSame( $coil_button_settings['coil_button_text'], $button_text );
		$this->assertSame( $coil_button_settings['coil_button_link'], $button_link );
		$this->assertSame( $coil_button_settings['coil_members_button_text'], $member_button_text );
	}

	/**
	 * Testing the Coil button shows to members by default.
	 *
	 * @return void
	 */
	public function test_coil_button_member_display_default() :  void {
		// Database defaults must first be setup
		Coil\maybe_update_database();
		$defaults        = Admin\get_coil_button_defaults();
		$retrieved_value = Admin\get_coil_button_setting( 'coil_button_member_display' );

		// The default is true
		$this->assertSame( $defaults['coil_button_member_display'], true );
		$this->assertSame( $defaults['coil_button_member_display'], $retrieved_value );
	}

	/**
	 * Testing if the Coil button member display option can be retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_coil_button_member_display_setting_can_be_retrieved_successfully() :  void {
		// Set the Coil button to show for Coil members
		$coil_button_settings = [ 'coil_button_member_display' => false ];
		update_option( 'coil_button_settings_group', $coil_button_settings );

		$member_button_display = Admin\get_coil_button_setting( 'coil_button_member_display' );

		$this->assertSame( false, $member_button_display );

		// Set the Coil button to be hidden from Coil members
		$coil_button_settings = [ 'coil_button_member_display' => true ];
		update_option( 'coil_button_settings_group', $coil_button_settings );

		$member_button_display = Admin\get_coil_button_setting( 'coil_button_member_display' );

		$this->assertSame( true, $member_button_display );
	}

	/**
	 * Testing the Coil button color theme default.
	 *
	 * @return void
	 */
	public function test_coil_button_color_theme_default() :  void {
		// Database defaults must first be setup
		Coil\maybe_update_database();
		$defaults        = Admin\get_coil_button_defaults();
		$retrieved_value = Admin\get_coil_button_setting( 'coil_button_color_theme' );

		// The default is dark
		$this->assertSame( $defaults['coil_button_color_theme'], 'dark' );
		$this->assertSame( $defaults['coil_button_color_theme'], $retrieved_value );
	}

	/**
	 * Testing if the Coil button color theme option can be retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_coil_button_color_theme_can_be_retrieved_successfully() :  void {
		// Set the Coil button theme as light
		$coil_button_settings = [ 'coil_button_color_theme' => 'light' ];
		update_option( 'coil_button_settings_group', $coil_button_settings );

		$retrieved_color_theme = Admin\get_coil_button_setting( 'coil_button_color_theme' );

		$this->assertSame( 'light', $retrieved_color_theme );

		// Set the Coil button theme as dark
		$coil_button_settings = [ 'coil_button_color_theme' => 'dark' ];
		update_option( 'coil_button_settings_group', $coil_button_settings );

		$retrieved_color_theme = Admin\get_coil_button_setting( 'coil_button_color_theme' );

		$this->assertSame( 'dark', $retrieved_color_theme );
	}

	/**
	 * Testing the Coil button size default.
	 *
	 * @return void
	 */
	public function test_coil_button_size_default() :  void {
		// Database defaults must first be setup
		Coil\maybe_update_database();
		$defaults        = Admin\get_coil_button_defaults();
		$retrieved_value = Admin\get_coil_button_setting( 'coil_button_size' );

		// The default is large
		$this->assertSame( $defaults['coil_button_size'], 'large' );
		$this->assertSame( $defaults['coil_button_size'], $retrieved_value );
	}

	/**
	 * Testing if the Coil button size can be retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_coil_button_size_can_be_retrieved_successfully() :  void {
		// Set the Coil button to be small
		$coil_button_settings = [ 'coil_button_size' => 'small' ];
		update_option( 'coil_button_settings_group', $coil_button_settings );

		$retrieved_button_size = Admin\get_coil_button_setting( 'coil_button_size' );

		$this->assertSame( 'small', $retrieved_button_size );

		// Set the Coil button to be large
		$coil_button_settings = [ 'coil_button_size' => 'large' ];
		update_option( 'coil_button_settings_group', $coil_button_settings );

		$retrieved_button_size = Admin\get_coil_button_setting( 'coil_button_size' );

		$this->assertSame( 'large', $retrieved_button_size );
	}

	/**
	 * Testing the Coil button position default.
	 *
	 * @return void
	 */
	public function test_coil_button_position_default() :  void {
		// Database defaults must first be setup
		Coil\maybe_update_database();
		$defaults        = Admin\get_coil_button_defaults();
		$retrieved_value = Admin\get_coil_button_setting( 'coil_button_position' );

		// The default is bottom-right
		$this->assertSame( $defaults['coil_button_position'], 'bottom-right' );
		$this->assertSame( $defaults['coil_button_position'], $retrieved_value );
	}

	/**
	 * Testing if the Coil button position can be retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_coil_button_position_can_be_retrieved_successfully() :  void {
		// Set the Coil button position to top-right
		$coil_button_settings = [ 'coil_button_position' => 'top-right' ];
		update_option( 'coil_button_settings_group', $coil_button_settings );

		$retrieved_button_position = Admin\get_coil_button_setting( 'coil_button_position' );

		$this->assertSame( 'top-right', $retrieved_button_position );

		// Set the Coil button position to bottom-left
		$coil_button_settings = [ 'coil_button_position' => 'bottom-left' ];
		update_option( 'coil_button_settings_group', $coil_button_settings );

		$retrieved_button_position = Admin\get_coil_button_setting( 'coil_button_position' );

		$this->assertSame( 'bottom-left', $retrieved_button_position );
	}

	/**
	 * Testing the Coil button margin defaults.
	 *
	 * @return void
	 */
	public function test_coil_button_margin_defaults() :  void {
		// Database defaults must first be setup
		Coil\maybe_update_database();
		$expected_values = [
			'coil_button_top_margin'    => '',
			'coil_button_right_margin'  => '',
			'coil_button_bottom_margin' => '',
			'coil_button_left_margin'   => '',
		];
		$retrieved_value = [
			'coil_button_top_margin'    => Admin\get_coil_button_setting( 'coil_button_top_margin' ),
			'coil_button_right_margin'  => Admin\get_coil_button_setting( 'coil_button_right_margin' ),
			'coil_button_bottom_margin' => Admin\get_coil_button_setting( 'coil_button_bottom_margin' ),
			'coil_button_left_margin'   => Admin\get_coil_button_setting( 'coil_button_left_margin' ),
		];

		// The default is always '-'
		$this->assertSame( $expected_values['coil_button_top_margin'], $retrieved_value['coil_button_top_margin'] );
		$this->assertSame( $expected_values['coil_button_right_margin'], $retrieved_value['coil_button_right_margin'] );
		$this->assertSame( $expected_values['coil_button_bottom_margin'], $retrieved_value['coil_button_bottom_margin'] );
		$this->assertSame( $expected_values['coil_button_left_margin'], $retrieved_value['coil_button_left_margin'] );
	}

	/**
	 * Testing if the Coil button margins can be retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_coil_button_margins_can_be_retrieved_successfully() :  void {
		// Set the Coil button to have custom margin sizes
		$coil_button_settings = [
			'coil_button_top_margin'    => '0',
			'coil_button_right_margin'  => '5',
			'coil_button_bottom_margin' => 'abc', // incorrect input
			'coil_button_left_margin'   => '', // no input
		];
		update_option( 'coil_button_settings_group', $coil_button_settings );

		$retrieved_margin_settings = [
			'coil_button_top_margin'    => Admin\get_coil_button_setting( 'coil_button_top_margin' ),
			'coil_button_right_margin'  => Admin\get_coil_button_setting( 'coil_button_right_margin' ),
			'coil_button_bottom_margin' => Admin\get_coil_button_setting( 'coil_button_bottom_margin' ),
			'coil_button_left_margin'   => Admin\get_coil_button_setting( 'coil_button_left_margin' ),
		];

		// When the input is considered empty (false, 0, '') then a null string is returned
		$this->assertSame( '', $retrieved_margin_settings['coil_button_top_margin'] );
		$this->assertSame( $coil_button_settings['coil_button_right_margin'], $retrieved_margin_settings['coil_button_right_margin'] );
		// When invalid input is given a null string is returned
		$this->assertSame( '', $retrieved_margin_settings['coil_button_bottom_margin'] );
		$this->assertSame( $coil_button_settings['coil_button_left_margin'], $retrieved_margin_settings['coil_button_left_margin'] );
	}

	/**
	 * Testing if the final Coil button margins can be calculated correctly from the wp_options table.
	 * There are baseline values for the button margins which can be added to or subtracted from in the Coil buton settings tab.
	 *
	 * @return void
	 */
	public function test_if_the_coil_button_margins_can_be_calculated_correctly() :  void {
		// Set the Coil button to have custom margin sizes
		$coil_button_settings = [
			'coil_button_top_margin'    => '0',
			'coil_button_right_margin'  => '5',
			'coil_button_bottom_margin' => 'abc', // incorrect input
			'coil_button_left_margin'   => '', // no input
		];
		update_option( 'coil_button_settings_group', $coil_button_settings );

		$basline_margin_values = Admin\get_button_margin_key_defaults();

		$expected_margin_values = [
			'coil_button_top_margin'    => strval( intval( $coil_button_settings['coil_button_top_margin'] ) + intval( $basline_margin_values['coil_button_top_margin'] ) ),
			'coil_button_right_margin'  => strval( intval( $coil_button_settings['coil_button_right_margin'] ) + intval( $basline_margin_values['coil_button_right_margin'] ) ),
			'coil_button_bottom_margin' => strval( intval( $coil_button_settings['coil_button_bottom_margin'] ) + intval( $basline_margin_values['coil_button_bottom_margin'] ) ),
			'coil_button_left_margin'   => strval( intval( $coil_button_settings['coil_button_left_margin'] ) + intval( $basline_margin_values['coil_button_left_margin'] ) ),
		];

		$final_margin_value = [
			'coil_button_top_margin'    => Admin\get_coil_button_margins( 'coil_button_top_margin' ),
			'coil_button_right_margin'  => Admin\get_coil_button_margins( 'coil_button_right_margin' ),
			'coil_button_bottom_margin' => Admin\get_coil_button_margins( 'coil_button_bottom_margin' ),
			'coil_button_left_margin'   => Admin\get_coil_button_margins( 'coil_button_left_margin' ),
		];

		$this->assertSame( $expected_margin_values['coil_button_top_margin'], $final_margin_value['coil_button_top_margin'] );
		$this->assertSame( $expected_margin_values['coil_button_right_margin'], $final_margin_value['coil_button_right_margin'] );
		$this->assertSame( $expected_margin_values['coil_button_bottom_margin'], $final_margin_value['coil_button_bottom_margin'] );
		$this->assertSame( $expected_margin_values['coil_button_left_margin'], $final_margin_value['coil_button_left_margin'] );
	}

	/**
	 * Testing if the Coil button post-type visibility settings can be retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_coil_button_visibility_settings_can_be_retrieved_successfully() :  void {
		// Create a post
		$post_obj = self::factory()->post->create_and_get();

		// Set the Coil button to show on posts
		$coil_button_settings = [ 'post_button_visibility' => 'show' ];
		update_option( 'coil_button_settings_group', $coil_button_settings );

		$button_status = Admin\get_coil_button_status( $post_obj->ID );

		$this->assertSame( 'show-coil-button', $button_status );

		// Set the Coil button to be hidden on posts
		$coil_button_settings = [ 'post_button_visibility' => 'hide' ];
		update_option( 'coil_button_settings_group', $coil_button_settings );

		$button_status = Admin\get_coil_button_status( $post_obj->ID );

		$this->assertSame( '', $button_status );
	}
}
