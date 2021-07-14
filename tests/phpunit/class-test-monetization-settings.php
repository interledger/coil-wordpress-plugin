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
			$retrieved_gating = Gating\get_post_gating( $post_obj->ID );

			$this->assertSame( $expected_gating_type, $retrieved_gating );
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

		$padlock_setting = Admin\get_appearance_settings( 'coil_title_padlock' );

		$this->assertSame( true, $padlock_setting );
	}

	/**
	 * Testing if the padlock icon setting is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_padlock_display_setting_is_retrieved_successfully() :  void {

		$padlock_display = [ 'coil_title_padlock' => false ];
		update_option( 'coil_appearance_settings_group', $padlock_display );

		$padlock_setting = Admin\get_appearance_settings( 'coil_title_padlock' );

		$this->assertSame( false, $padlock_setting );

		$padlock_display = [ 'coil_title_padlock' => true ];
		update_option( 'coil_appearance_settings_group', $padlock_display );

		$padlock_setting = Admin\get_appearance_settings( 'coil_title_padlock' );

		$this->assertSame( true, $padlock_setting );
	}

	/**
	 * Testing if the Coil Promotion Bar shows by default.
	 *
	 * @return void
	 */
	public function test_if_default_promotion_bar_display_is_enabled() :  void {

		$default_appearance_settings = Admin\get_appearance_settings( 'coil_show_donation_bar' );

		$this->assertSame( true, $default_appearance_settings );
	}

	/**
	 * Testing if the Coil Promotion Bar display setting is retrieved correctly from the wp_options table.
	 *
	 * @return void
	 */
	public function test_if_the_promotion_bar_display_setting_is_retrieved_successfully() :  void {

		$promotion_bar_display = [ 'coil_show_donation_bar' => false ];
		update_option( 'coil_appearance_settings_group', $promotion_bar_display );

		$promotion_bar_settings = Admin\get_appearance_settings( 'coil_show_donation_bar' );

		$this->assertSame( false, $promotion_bar_settings );

		$promotion_bar_display = [ 'coil_show_donation_bar' => true ];
		update_option( 'coil_appearance_settings_group', $promotion_bar_display );

		$promotion_bar_settings = Admin\get_appearance_settings( 'coil_show_donation_bar' );

		$this->assertSame( true, $promotion_bar_settings );
	}

	/**
	 * Testing if a user has the Coil Promotion Bar and padlock display settings which they saved in the customizer that they are migrated successfully to the wp_options table
	 * In the case where both settings had been set in the customizer and no settings had been added to the settings panel.
	 *
	 * @return void
	 */
	public function test_transfer_of_appearance_settings_from_customizer_when_settings_disabled() :  void {

		// Testing when both settings are set to false
		// Adding custom appearance settings to the theme_mod
		set_theme_mod( 'coil_show_donation_bar', false );
		set_theme_mod( 'coil_title_padlock', false );

		// Transferrng settings to the wp_options table
		Settings\transfer_customizer_appearance_settings();

		// Creating an array of the appearance settings that were retrieved from the wp_options table.
		$appearance_settings = [
			'coil_show_donation_bar' => Admin\get_appearance_settings( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_appearance_settings( 'coil_title_padlock' ),
		];

		// Checking that all appearance settings that were retrieved are correct
		$this->assertSame( false, $appearance_settings['coil_show_donation_bar'] );
		$this->assertSame( false, $appearance_settings['coil_title_padlock'] );

		// Checking that the theme_mod appearance settings have been removed
		$this->assertFalse( get_theme_mod( 'coil_show_donation_bar' ) );
		$this->assertFalse( get_theme_mod( 'coil_title_padlock' ) );
	}

	/**
	 * Testing if a user has the Coil Promotion Bar and padlock display settings which they saved in the customizer that they are migrated successfully to the wp_options table
	 * In the case where both settings had been set in the customizer and no settings had been added to the settings panel.
	 *
	 * @return void
	 */
	public function test_transfer_of_appearance_settings_from_customizer_when_settings_enabled() :  void {

		// Adding custom appearance settings to the theme_mod
		set_theme_mod( 'coil_show_donation_bar', true );
		set_theme_mod( 'coil_title_padlock', true );

		// Transferrng settings to the wp_options table
		Settings\transfer_customizer_appearance_settings();

		// Creating an array of the appearance settings that were retrieved from the wp_options table.
		$appearance_settings = [
			'coil_show_donation_bar' => Admin\get_appearance_settings( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_appearance_settings( 'coil_title_padlock' ),
		];

		// Checking that all appearance settings that were retrieved are correct
		$this->assertSame( true, $appearance_settings['coil_show_donation_bar'] );
		$this->assertSame( true, $appearance_settings['coil_title_padlock'] );

		// Checking that the theme_mod appearance settings have been removed
		$this->assertFalse( get_theme_mod( 'coil_show_donation_bar' ) );
		$this->assertFalse( get_theme_mod( 'coil_title_padlock' ) );
	}

	/**
	 * Testing if a user has the Coil Promotion Bar and padlock display settings which they saved in the customizer that they are migrated successfully to the wp_options table
	 * In the case where both settings had been set in the customizer and no settings had been added to the settings panel.
	 *
	 * @return void
	 */
	public function test_transfer_of_appearance_settings_from_customizer_when_settings_are_mixed() :  void {

		// Testing when one setting is set to true and the other to false
		// Adding custom appearance settings to the theme_mod
		set_theme_mod( 'coil_show_donation_bar', true );
		set_theme_mod( 'coil_title_padlock', false );

		// Transferrng settings to the wp_options table
		Settings\transfer_customizer_appearance_settings();

		// Creating an array of the appearance settings that were retrieved from the wp_options table.
		$appearance_settings = [
			'coil_show_donation_bar' => Admin\get_appearance_settings( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_appearance_settings( 'coil_title_padlock' ),
		];

		// Checking that all appearance settings that were retrieved are correct
		$this->assertSame( true, $appearance_settings['coil_show_donation_bar'] );
		$this->assertSame( false, $appearance_settings['coil_title_padlock'] );

		// Checking that the theme_mod appearance settings have been removed
		$this->assertFalse( get_theme_mod( 'coil_show_donation_bar' ) );
		$this->assertFalse( get_theme_mod( 'coil_title_padlock' ) );
	}

	/**
	 * Testing if a user has the Coil Promotion Bar and padlock display settings which they saved in the customizer that they are migrated successfully to the wp_options table
	 * In cases where some settings are saved to the customizer and others have been saved to the settings panel.
	 *
	 * @return void
	 */
	public function test_transfer_of_appearance_settings_from_customizer_where_settings_have_been_saved_in_both_locations() :  void {

		// Adding custom appearance settings to the theme_mod
		set_theme_mod( 'coil_show_donation_bar', false );
		$options                       = get_option( 'coil_appearance_settings_group', [] );
		$options['coil_title_padlock'] = false;
		update_option( 'coil_appearance_settings_group', $options );

		// Transferrng settings to the wp_options table
		Settings\transfer_customizer_appearance_settings();

		// Creating an array of the appearance settings that were retrieved from the wp_options table.
		$appearance_settings = [
			'coil_show_donation_bar' => Admin\get_appearance_settings( 'coil_show_donation_bar' ),
			'coil_title_padlock'     => Admin\get_appearance_settings( 'coil_title_padlock' ),
		];

		// Checking that all appearance settings that were retrieved are correct
		$this->assertSame( false, $appearance_settings['coil_show_donation_bar'] );
		$this->assertSame( false, $appearance_settings['coil_title_padlock'] );

		// Checking that the theme_mod appearance settings have been removed
		$this->assertFalse( get_theme_mod( 'coil_show_donation_bar' ) );
		$this->assertFalse( get_theme_mod( 'coil_title_padlock' ) );
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

			$content_gating = Gating\get_post_gating( $post_obj->ID );

			$this->assertSame( $gating_type, $content_gating );
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
