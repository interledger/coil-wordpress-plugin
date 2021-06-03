<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil\Gating;
use Coil\Admin;
use WP_UnitTestCase;
use WP_UnitTest_Factory;

/**
 * Payment pointer hierarchy tests.
 */
class Test_Payment_Pointer_Hierarchy extends WP_UnitTestCase {

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
	 * Check that post titles get padlock icon when setting enabled and posts are fully gated.
	 * 
	 * @return void
	 */
	public function test_padlock_added_to_title() :  void {
		
		foreach ( self::$basic_posts as $gating => $post_obj ) {
			$post_title = '';
			switch ($gating) {
				case 'no':
					$post_title = 'This is a post with no gating';
					$post_obj->post_title = $post_title;
					$post_obj->post_title = Gating\maybe_add_padlock_to_title( $post_title, $post_obj->ID );
				  	break;
				case 'no-gating':
					$post_title = 'This post is monetized and public';
					$post_obj->post_title = $post_title;
					$post_obj->post_title = Gating\maybe_add_padlock_to_title( $post_title, $post_obj->ID );
					break;
				case 'gate-all':
					$post_title = 'This is a post that is fully gated';
					$post_obj->post_title = $post_title;
					$post_obj->post_title = Gating\maybe_add_padlock_to_title( $post_title, $post_obj->ID );
					$post_title = '🔒 ' . $post_title;
					break;
				case 'gate-tagged-blocks':
					$post_title = 'This is a post that has partial gating';
					$post_obj->post_title = $post_title;
					$post_obj->post_title = Gating\maybe_add_padlock_to_title( $post_title, $post_obj->ID );
					break;
			  }
			$this->assertSame( $post_obj->post_title, $post_title );
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
