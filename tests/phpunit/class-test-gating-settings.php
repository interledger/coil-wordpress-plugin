<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil\Gating;
use WP_UnitTestCase;
use WP_UnitTest_Factory;

/**
 * Payment pointer hierarchy tests.
 */
class Test_Gating_Settings extends WP_UnitTestCase {

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
	 * Check that post titles get padlock icon when setting enabled and posts are fully gated.
	 *
	 * @return void
	 */
	public function test_padlock_added_to_title_when_enabled() :  void {

		// Ensuring the padlock display setting has been enabled
		$options                       = get_option( 'coil_monetization_settings_group', [] );
		$options['coil_title_padlock'] = true;
		update_option( 'coil_monetization_settings_group', $options );

		foreach ( self::$basic_posts as $gating => $post_obj ) {
			$post_title           = 'Post Title';
			$post_obj->post_title = Gating\maybe_add_padlock_to_title( $post_title, $post_obj->ID );
			if ( $gating === 'gate-all' ) {
				$post_title = '🔒 ' . $post_title;
			}
			$this->assertSame( $post_title, $post_obj->post_title );
		}
	}

	/**
	 * Check that post titles do no get a padlock icon when option is disabled.
	 *
	 * @return void
	 */
	public function test_padlock_not_added_to_title_when_disabled() :  void {

		// Ensuring the padlock display setting has been disabled
		$options                       = get_option( 'coil_monetization_settings_group', [] );
		$options['coil_title_padlock'] = false;
		update_option( 'coil_monetization_settings_group', $options );

		foreach ( self::$basic_posts as $gating => $post_obj ) {
			$post_title           = 'Post Title';
			$post_obj->post_title = Gating\maybe_add_padlock_to_title( $post_title, $post_obj->ID );
			$this->assertSame( $post_title, $post_obj->post_title );
		}
	}

	/**
	 * Tests taxonomy gating.
	 *
	 * @return void
	 */
	public function test_taxonomy_term_gating() :  void {

		$category = [
			'No Monetization'      => 'no',
			'Monetized and Public' => 'no-gating',
			'Fully Gated'          => 'gate-all',
		];
		foreach ( $category as $category_name => $gating_type ) {
			wp_create_category( $category_name );
			Gating\set_term_gating( get_cat_ID( $category_name ), $gating_type );
			$post_obj = self::factory()->post->create_and_get();
			wp_set_post_categories( $post_obj->ID, get_cat_ID( $category_name ), false );
			$this->assertSame( $gating_type, Gating\get_taxonomy_term_gating( $post_obj->ID ) );
		}
	}

	/**
	 * Tests taxonomy gating.
	 *
	 * @return void
	 */
	public function test_taxonomy_term_gating() :  void {

		// add global defaults to the database
		delete_option( 'coil_monetization_settings_group' );
		// create array of expected values $global_gating =
		printf( Gating\get_global_posts_gating() );
		//$this->assertSame( $global_gating, Gating\et_global_posts_gating() );
	}
}
