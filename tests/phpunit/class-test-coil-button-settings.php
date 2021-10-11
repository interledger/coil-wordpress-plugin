<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil\Admin;
use Coil\Settings;
use WP_UnitTestCase;

/**
 * Testing the Coil button settings.
 */
class Test_Coil_Button_Settings extends WP_UnitTestCase {

	/**
	 * Testing if the donation bar footer shows by default.
	 *
	 * @return void
	 */
	public function test_if_default_donation_bar_display_is_enabled() :  void {

		// Database defaults must first be setup
		Settings\maybe_update_database();
		$default_donation_bar_display = Admin\get_coil_button_setting( 'coil_show_donation_bar' );

		$this->assertSame( true, $default_donation_bar_display );
	}

    	/**
	 * Testing if the donation bar display setting is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_donation_bar_display_setting_is_retrieved_successfully() :  void {

		$donation_bar_display = [ 'coil_show_donation_bar' => false ];
		update_option( 'coil_button_settings_group', $donation_bar_display );

		$donation_bar_settings = Admin\get_coil_button_setting( 'coil_show_donation_bar' );

		$this->assertSame( false, $donation_bar_settings );

		$donation_bar_display = [ 'coil_show_donation_bar' => true ];
		update_option( 'coil_button_settings_group', $donation_bar_display );

		$donation_bar_settings = Admin\get_coil_button_setting( 'coil_show_donation_bar' );

		$this->assertSame( true, $donation_bar_settings );
	}
}