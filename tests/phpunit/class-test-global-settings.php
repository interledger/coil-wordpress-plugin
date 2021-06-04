<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil\Admin;
use WP_UnitTestCase;

/**
 * Testing the custom global settings.
 */
class Test_Global_Settings extends WP_UnitTestCase {

	/**
	 * Testing if the the default payment pointer is an empty string.
	 *
	 * @return void
	 */
	public function test_if_default_payment_pointer_is_an_empty_string() :  void {

		$this->assertSame( '', Admin\get_global_settings( 'coil_payment_pointer_id' ) );
	}

	/**
	 * Testing if the payment pointer value has been saved successfully to the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_payment_pointer_saves_successfully() :  void {

		$payment_pointer = [ 'coil_payment_pointer_id' => '$wallet.example.com/bob' ];
		update_option( 'coil_global_settings_group', $payment_pointer );
		if ( ! update_option( 'coil_global_settings_group', $payment_pointer ) ) {
			add_option( 'coil_global_settings_group', $payment_pointer );
		}

		$this->assertSame( $payment_pointer['coil_payment_pointer_id'], Admin\get_global_settings( 'coil_payment_pointer_id' ) );
	}

	/**
	 * Testing if the default content container can be retreived successfully from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_default_content_container_is_retreived_successfully() :  void {

		$this->assertSame( '.content-area .entry-content', Admin\get_global_settings( 'coil_content_container' ) );
	}

	/**
	 * Testing if the content container value has been successfully saved to the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_content_container_saves_successfully() :  void {

		$content_container = [ 'coil_content_container' => '.content-area .entry-content, .post-story' ];
		update_option( 'coil_global_settings_group', $content_container );
		if ( ! update_option( 'coil_global_settings_group', $content_container ) ) {
			add_option( 'coil_global_settings_group', $content_container );
		}

		$this->assertSame( $content_container['coil_content_container'], Admin\get_global_settings( 'coil_content_container' ) );
	}
}
