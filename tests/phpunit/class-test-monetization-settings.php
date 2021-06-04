<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil\Admin;
use Coil\Gating;
use Coil\Settings;
use WP_UnitTestCase;

/**
 * Testing the custom monetization settings.
 */
class Test_Monetization_Settings extends WP_UnitTestCase {

	/**
	 * Testing if the content setings set posts to Monetized and Public by default.
	 *
	 * @return void
	 */
	public function test_if_content_settings_save_successfully() :  void {

		$gating_type;
		$post_obj = self::factory()->post->create_and_get();

		// Using the set post function
		$gating_type = 'no-gating';
		Gating\set_post_gating( $post_obj->ID, $gating_type );
		$this->assertSame( $gating_type, Gating\get_post_gating( $post_obj->ID ) );

		// Accessing the database directly
		$gating_type = 'gate-all';
		update_post_meta( $post_obj->ID, '_coil_monetize_post_status', $gating_type );
		if ( update_post_meta( $post_obj->ID, '_coil_monetize_post_status', $gating_type ) ) {
			add_post_meta( $post_obj->ID, '_coil_monetize_post_status', $gating_type );
		}
		$this->assertSame( $gating_type, Gating\get_post_gating( $post_obj->ID ) );
	}

	/**
	 * Testing if the padlock icon shows next to geted post titles by default.
	 *
	 * @return void
	 */
	public function test_if_default_padlock_display_is_enabled() :  void {

		$this->assertSame( true, Admin\get_visual_settings( 'coil_title_padlock' ) );
	}

	/**
	 * Testing if the padlock icon setting is retreived correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_padlock_display_setting_saves_successfully() :  void {

		$padlock_display = [ 'coil_title_padlock' => false ];
		update_option( 'coil_monetization_settings_group', $padlock_display );
		if ( ! update_option( 'coil_monetization_settings_group', $padlock_display ) ) {
			add_option( 'coil_monetization_settings_group', $padlock_display );
		}

		$this->assertSame( false, Admin\get_visual_settings( 'coil_title_padlock' ) );

		$padlock_display = [ 'coil_title_padlock' => true ];
		update_option( 'coil_monetization_settings_group', $padlock_display );
		if ( ! update_option( 'coil_monetization_settings_group', $padlock_display ) ) {
			add_option( 'coil_monetization_settings_group', $padlock_display );
		}

		$this->assertSame( true, Admin\get_visual_settings( 'coil_title_padlock' ) );
	}

	/**
	 * Testing if the donation bar footer shows by default.
	 *
	 * @return void
	 */
	public function test_if_default_donation_bar_display_is_enabled() :  void {

		$this->assertSame( true, Admin\get_visual_settings( 'coil_show_donation_bar' ) );
	}

	/**
	 * Testing if the donation bar display setting is retreived correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_donation_bar_display_setting_saves_successfully() :  void {

		$donation_bar_display = [ 'coil_show_donation_bar' => false ];
		update_option( 'coil_monetization_settings_group', $donation_bar_display );
		if ( ! update_option( 'coil_monetization_settings_group', $donation_bar_display ) ) {
			add_option( 'coil_monetization_settings_group', $donation_bar_display );
		}

		$this->assertSame( false, Admin\get_visual_settings( 'coil_show_donation_bar' ) );

		$donation_bar_display = [ 'coil_show_donation_bar' => true ];
		update_option( 'coil_monetization_settings_group', $donation_bar_display );
		if ( ! update_option( 'coil_monetization_settings_group', $donation_bar_display ) ) {
			add_option( 'coil_monetization_settings_group', $donation_bar_display );
		}

		$this->assertSame( true, Admin\get_visual_settings( 'coil_show_donation_bar' ) );
	}

	/**
	 * Testing if a user has donation bar and padlock display settings which they saved in the customizer that they are migrated successfully to the wp_options table
	 *
	 * @return void
	 */
	public function test_transfer_of_visual_settings_from_customizer() :  void {

		// Testing when both settings are set to false
		// Adding custom visual settings to the theme_mod
		set_theme_mod( 'coil_show_donation_bar', false );
		set_theme_mod( 'coil_title_padlock', false );

		// Transferrng settings to the wp_options table
		Settings\transfer_customizer_monetization_settings();

		// Creating an array of the visual settings that were retreived from the wp_options table.
		$visual_settings = [
			'coil_show_donation_bar' => Admin\get_visual_settings( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_visual_settings( 'coil_title_padlock' ),
		];

		// Checking that all visual settings that were retreived are correct
		$this->assertSame( false, $visual_settings['coil_show_donation_bar'] );
		$this->assertSame( false, $visual_settings['coil_title_padlock'] );

		// Checking that the theme_mod visual settings have been removed
		$this->assertFalse( get_theme_mod( 'coil_show_donation_bar' ) );
		$this->assertFalse( get_theme_mod( 'coil_title_padlock' ) );

		// Testing when both settings are set to true
		// Adding custom visual settings to the theme_mod
		set_theme_mod( 'coil_show_donation_bar', true );
		set_theme_mod( 'coil_title_padlock', true );

		// Transferrng settings to the wp_options table
		Settings\transfer_customizer_monetization_settings();

		// Creating an array of the visual settings that were retreived from the wp_options table.
		$visual_settings = [
			'coil_show_donation_bar' => Admin\get_visual_settings( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_visual_settings( 'coil_title_padlock' ),
		];

		// Checking that all visual settings that were retreived are correct
		$this->assertSame( true, $visual_settings['coil_show_donation_bar'] );
		$this->assertSame( true, $visual_settings['coil_title_padlock'] );

		// Checking that the theme_mod visual settings have been removed
		$this->assertFalse( get_theme_mod( 'coil_show_donation_bar' ) );
		$this->assertFalse( get_theme_mod( 'coil_title_padlock' ) );
		$this->assertFalse( get_option( 'coil_content_settings_posts_group' ) );

		// Testing when one setting is set to true and the other to false
		// Adding custom visual settings to the theme_mod
		set_theme_mod( 'coil_show_donation_bar', true );
		set_theme_mod( 'coil_title_padlock', false );

		// Transferrng settings to the wp_options table
		Settings\transfer_customizer_monetization_settings();

		// Creating an array of the visual settings that were retreived from the wp_options table.
		$visual_settings = [
			'coil_show_donation_bar' => Admin\get_visual_settings( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_visual_settings( 'coil_title_padlock' ),
		];

		// Checking that all visual settings that were retreived are correct
		$this->assertSame( true, $visual_settings['coil_show_donation_bar'] );
		$this->assertSame( false, $visual_settings['coil_title_padlock'] );

		// Checking that the theme_mod visual settings have been removed
		$this->assertFalse( get_theme_mod( 'coil_show_donation_bar' ) );
		$this->assertFalse( get_theme_mod( 'coil_title_padlock' ) );
		$this->assertFalse( get_option( 'coil_content_settings_posts_group' ) );
	}
}
