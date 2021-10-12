<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil;
use WP_UnitTestCase;

/**
 * Testing the monetization settings.
 * This includes monetization defaults as well as the payment pointer.
 */
class Test_General_Settings extends WP_UnitTestCase {

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

		$payment_pointer = [ 'coil_payment_pointer' => '$wallet.example.com/bob' ];
		update_option( 'coil_general_settings_group', $payment_pointer );

		$retrieved_payment_pointer = Coil\get_payment_pointer();

		$this->assertSame( $payment_pointer['coil_payment_pointer'], $retrieved_payment_pointer );
	}

}
