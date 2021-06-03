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
 * Testing the custom message settings.
 */
class Test_Messaging_Settings extends WP_UnitTestCase {

	/**
	 * Basic posts for testing with.
	 *
	 * These have:
	 *
	 * - No post type-specific gating.
	 * - No taxonomy-specific gating.
	 *
	 * @var \WP_Post[] Standard post objects.
	 */
	protected static $basic_posts = [];

	/**
	 * Create fake data before tests run.
	 *
	 * @param WP_UnitTest_Factory $factory Helper that creates fake data.
	 *
	 * @return void
	 */
	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) : void {

		self::$basic_posts = [
			'no'                 => $factory->post->create_and_get(),
			'no-gating'          => $factory->post->create_and_get(),
			'gate-all'           => $factory->post->create_and_get(),
			'gate-tagged-blocks' => $factory->post->create_and_get(),
		];

		foreach ( self::$basic_posts as $gating_type => $post_obj ) {
			Gating\set_post_gating( $post_obj->ID, $gating_type );
		}
	}

	/**
	 * Delete fake data after tests run.
	 *
	 * @return void
	 */
	public static function wpTearDownAfterClass() : void {

		foreach ( self::$basic_posts as $_ => $post_obj ) {
			wp_delete_post( $post_obj->ID, true );
		}

		self::$basic_posts = [];
	}

	/**
	 * Check that message defaults can be retreived successfully.
	 *
	 * @return void
	 */
	public function test_retreiving_message_defaults() :  void {

		// Ensuring no custom messages are present in the database
		update_option( 'coil_monetization_settings_group', null );

        // Creating an array of the message defaults that were retreived
        $message = [
            'unable_to_verify'        => Admin\get_messaging_setting_or_default( 'coil_unable_to_verify_message' ),
            'voluntary_donation'      => Admin\get_messaging_setting_or_default( 'coil_voluntary_donation_message' ),
            'loading_content'         => Admin\get_messaging_setting_or_default( 'coil_verifying_status_message' ),
            'fully_gated'             => Admin\get_messaging_setting_or_default( 'coil_fully_gated_content_message' ),
            'partial_gating'          => Admin\get_messaging_setting_or_default( 'coil_partially_gated_content_message' ),
            'learn_more_button_text'  => Admin\get_messaging_setting_or_default( 'coil_learn_more_button_text' ),
            'learn_more_button_link'  => Admin\get_messaging_setting_or_default( 'coil_learn_more_button_link' ),
        ];

        // Checking that all defaults are correct
        $this->assertSame( 'You need a valid Coil account to see this content.', $message['unable_to_verify'] );
        $this->assertSame( 'This site is monetized using Coil. If you enjoy the content, consider supporting us by signing up for a Coil Membership. Here\'s how…', $message['voluntary_donation'] );
        $this->assertSame( 'Verifying Web Monetization status. Please wait...', $message['loading_content'] );
        $this->assertSame( 'Unlock exclusive content with Coil. Need a Coil account?', $message['fully_gated'] );
        $this->assertSame( 'To keep reading, join Coil and install the browser extension. Visit coil.com for more information.', $message['partial_gating'] );
        $this->assertSame( 'Get Coil to access', $message['learn_more_button_text'] );
        $this->assertSame( 'https://coil.com/', $message['learn_more_button_link'] );
	}
}

