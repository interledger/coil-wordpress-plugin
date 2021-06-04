<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil\Admin;
use Coil\Gating;
use Coil\Settings;
use WP_UnitTestCase;
use WP_UnitTest_Factory;

/**
 * Testing the custom monetization settings.
 */
class Test_Monetization_Settings extends WP_UnitTestCase {

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
	 * Test that gating provides the correct gating for basic posts.
	 *
	 * @return void
	 */
	public function test_that_basic_posts_have_correct_gating() :  void {

		foreach ( self::$basic_posts as $expected_gating_type => $post_obj ) {
			$this->assertSame( $expected_gating_type, Gating\get_post_gating( $post_obj->ID ) );
		}
	}

	/**
	 * Test that gating provides the correct gating for posts.
	 *
	 * @dataProvider get_post_meta_data_provider
	 *
	 * @param string $content_type         Type of gating to test. Either "basic", "taxonomy", "posttype".
	 * @param string $expected_gating_type See return values from Gating\get_post_gating().
	 *
	 * @return void
	 */
	public function test_that_posts_have_the_correct_gating( string $content_type, string $expected_gating_type ) :  void {

		$content_objs = "{$content_type}_posts";

		foreach ( self::$$content_objs as $expected_gating_type => $post_obj ) {
			$this->assertSame(
				$expected_gating_type,
				Gating\get_post_gating( $post_obj->ID ),
				"For {$content_type}, expected: {$expected_gating_type}/"
			);
		}
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
	 * Testing if the padlock icon setting is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_padlock_display_setting_saves_successfully() :  void {

		$padlock_display = [ 'coil_title_padlock' => false ];
		update_option( 'coil_monetization_settings_group', $padlock_display );

		$this->assertSame( false, Admin\get_visual_settings( 'coil_title_padlock' ) );

		$padlock_display = [ 'coil_title_padlock' => true ];
		update_option( 'coil_monetization_settings_group', $padlock_display );

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
	 * Testing if the donation bar display setting is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_donation_bar_display_setting_saves_successfully() :  void {

		$donation_bar_display = [ 'coil_show_donation_bar' => false ];
		update_option( 'coil_monetization_settings_group', $donation_bar_display );

		$this->assertSame( false, Admin\get_visual_settings( 'coil_show_donation_bar' ) );

		$donation_bar_display = [ 'coil_show_donation_bar' => true ];
		update_option( 'coil_monetization_settings_group', $donation_bar_display );

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

		// Creating an array of the visual settings that were retrieved from the wp_options table.
		$visual_settings = [
			'coil_show_donation_bar' => Admin\get_visual_settings( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_visual_settings( 'coil_title_padlock' ),
		];

		// Checking that all visual settings that were retrieved are correct
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

		// Creating an array of the visual settings that were retrieved from the wp_options table.
		$visual_settings = [
			'coil_show_donation_bar' => Admin\get_visual_settings( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_visual_settings( 'coil_title_padlock' ),
		];

		// Checking that all visual settings that were retrieved are correct
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

		// Creating an array of the visual settings that were retrieved from the wp_options table.
		$visual_settings = [
			'coil_show_donation_bar' => Admin\get_visual_settings( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_visual_settings( 'coil_title_padlock' ),
		];

		// Checking that all visual settings that were retrieved are correct
		$this->assertSame( true, $visual_settings['coil_show_donation_bar'] );
		$this->assertSame( false, $visual_settings['coil_title_padlock'] );

		// Checking that the theme_mod visual settings have been removed
		$this->assertFalse( get_theme_mod( 'coil_show_donation_bar' ) );
		$this->assertFalse( get_theme_mod( 'coil_title_padlock' ) );
		$this->assertFalse( get_option( 'coil_content_settings_posts_group' ) );
	}

	/**
	 * Testing if the content setings are retrieved correctly from the database.
	 *
	 * @return void
	 */
	public function test_if_content_settings_are_retrieved_successfully() :  void {

		foreach ( self::$basic_posts as $gating_type => $post_obj ) {
			// Accessing the database directly
			update_post_meta( $post_obj->ID, '_coil_monetize_post_status', $gating_type );
			if ( update_post_meta( $post_obj->ID, '_coil_monetize_post_status', $gating_type ) ) {
				add_post_meta( $post_obj->ID, '_coil_monetize_post_status', $gating_type );
			}
			$this->assertSame( $gating_type, Gating\get_post_gating( $post_obj->ID ) );
		}
	}

	/**
	 * Get data for testing post gating and titles.
	 *
	 * @link https://phpunit.readthedocs.io/en/8.4/writing-tests-for-phpunit.html#data-providers
	 *
	 * @return array
	 */
	public function get_post_meta_data_provider() : array {

		return [
			[
				'basic',
				'default',
			],
			[
				'basic',
				'no',
			],
			[
				'basic',
				'no-gating',
			],
			[
				'basic',
				'gate-all',
			],
			[
				'basic',
				'gate-tagged-blocks',
			],
		];
	}
}
