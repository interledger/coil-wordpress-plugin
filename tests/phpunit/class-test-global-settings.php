<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil;
use Coil\Admin;
use WP_UnitTestCase;

/**
 * Testing the custom default global monetization settings.
 */
class Test_Global_Settings extends WP_UnitTestCase {

	/**
	 * Testing if the the default payment pointer is an empty string.
	 *
	 * @return void
	 */
	public function test_if_default_payment_pointer_is_an_empty_string() :  void {

		$default_payment_ponter_setting = Coil\get_payment_pointer();

		$this->assertSame( '', $default_payment_ponter_setting );
	}

	/**
	 * Testing if the payment pointer value can be retrieved successfully from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_payment_pointer_is_retrieved_successfully() :  void {

		$payment_pointer = [ 'coil_payment_pointer_id' => '$wallet.example.com/bob' ];
		update_option( 'coil_global_settings_group', $payment_pointer );

		$retrieved_payment_pointer = Coil\get_payment_pointer();

		$this->assertSame( $payment_pointer['coil_payment_pointer_id'], $retrieved_payment_pointer );
	}

	/**
	 * Testing if the default content container can be retrieved successfully from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_default_content_container_is_retrieved_successfully() :  void {

		$this->assertSame( '.content-area .entry-content', Admin\get_global_settings( 'coil_content_container' ) );
	}

	/**
	 * Testing if the content container can be retrieved successfully from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_content_container_is_retrieved_successfully() :  void {

		$content_container = [ 'coil_content_container' => '.content-area .entry-content, .post-story' ];
		update_option( 'coil_global_settings_group', $content_container );

		$retrieved_content_container = Admin\get_global_settings( 'coil_content_container' );

		$this->assertSame( $content_container['coil_content_container'], $retrieved_content_container );
	}
}
