<?php
/**
 * Test.
 */
namespace Coil\Tests;

use Coil\Gating;
use WP_UnitTestCase;
use WP_UnitTest_Factory;

/**
 * Gating tests.
 */
class Test_Coil_Status_Settings extends WP_UnitTestCase {

	/**
	 * Basic posts for testing with.
	 *
	 * @var \WP_Post[] Standard post objects.
	 */
	protected static $basic_posts        = [];
	protected static $monetization_types = [
		[
			'monetization' => 'not-monetized',
			'name'         => 'Disabled',
		],
		[
			'monetization' => 'monetized',
			'name'         => 'Enabled',
		],
		[
			'monetization' => 'default',
			'name'         => 'Default',
		],
	];
	protected static $visibility_types   = [
		[
			'visibility' => 'public',
			'name'       => 'Public',
		],
		[
			'visibility' => 'exclusive',
			'name'       => 'Exclusive',
		],
		[
			'visibility' => 'default',
			'name'       => 'Default',
		],
	];

	/**
	 * Create fake data before tests run.
	 *
	 * @param WP_UnitTest_Factory $factory Helper that creates fake data.
	 *
	 * @return void
	 */
	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) : void {

		self::$basic_posts = [
			[
				'post'         => $factory->post->create_and_get(),
				'title'        => 'Disabled',
				'monetization' => 'not-monetized',
				'visibility'   => 'public',
			],
			[
				'post'         => $factory->post->create_and_get(),
				'title'        => 'Enabled & Public',
				'monetization' => 'monetized',
				'visibility'   => 'public',
			],
			[
				'post'         => $factory->post->create_and_get(),
				'title'        => 'Enabled & Exclusive',
				'monetization' => 'monetized',
				'visibility'   => 'exclusive',
			],
			[
				'post'         => $factory->post->create_and_get(),
				'title'        => 'Split',
				'monetization' => 'monetized',
				'visibility'   => 'gate-tagged-blocks',
			],
			[
				'post'         => $factory->post->create_and_get(),
				'title'        => 'Default',
				'monetization' => 'default',
				'visibility'   => 'default',
			],
		];

		foreach ( self::$basic_posts as $post_array ) {
			Gating\set_post_status( $post_array['post']->ID, '_coil_monetization_post_status', $post_array['monetization'] );
			Gating\set_post_status( $post_array['post']->ID, '_coil_visibility_post_status', $post_array['visibility'] );
		}
	}

	/**
	 * Delete fake data after tests run.
	 *
	 * @return void
	 */
	public static function wpTearDownAfterClass() : void {

		foreach ( self::$basic_posts as $_ => $post_array ) {
			wp_delete_post( $post_array['post']->ID, true );
		}

		self::$basic_posts = [];
	}

	/**
	 * Check that the post status was correctly set and can be correctly retrieved.
	 *
	 * @return void
	 */
	public function test_set_and_get_post_status() :  void {

		// Post status was set up in the wpSetUpBeforeClass function.

		foreach ( self::$basic_posts as $post_array ) {
			$retrieved_monetization_status = Gating\get_post_status( $post_array['post']->ID, 'monetization' );
			$retrieved_visibility_status   = Gating\get_post_status( $post_array['post']->ID, 'visibility' );

			$this->assertSame( $post_array['monetization'], $retrieved_monetization_status );
			$this->assertSame( $post_array['visibility'], $retrieved_visibility_status );
		}
	}

	// TODO: Update to reflect new padlock SVGs
	// /**
	//  * Check that post titles get a padlock icon when the setting is enabled and posts are fully gated.
	//  *
	//  * @return void
	//  */
	// public function test_padlock_added_to_title_when_enabled() :  void {

	// 	// Ensuring the padlock display setting has been enabled
	// 	$settings                       = get_option( 'coil_exclusive_settings_group', [] );
	// 	$settings['coil_title_padlock'] = true;
	// 	update_option( 'coil_exclusive_settings_group', $settings );

	// 	foreach ( self::$basic_posts as $post_array ) {
	// 		$post_title                     = $post_array['title'];
	// 		$post_array['post']->post_title = Gating\maybe_add_padlock_to_title( $post_title, $post_array['post']->ID );
	// 		if ( $post_array['visibility'] === 'exclusive' ) {
	// 			$post_title = '🔒 ' . $post_title;
	// 		}

	// 		$final_post_title = $post_array['post']->post_title;

	// 		$this->assertSame( $post_title, $final_post_title );
	// 	}
	// }

	/**
	 * Check that post titles do no get a padlock icon when option is disabled.
	 *
	 * @return void
	 */
	public function test_padlock_not_added_to_title_when_disabled() :  void {

		// Ensuring the padlock display setting has been disabled
		$settings                       = get_option( 'coil_exclusive_settings_group', [] );
		$settings['coil_title_padlock'] = false;
		update_option( 'coil_exclusive_settings_group', $settings );

		foreach ( self::$basic_posts as $post_array ) {
			$post_title                     = $post_array['title'];
			$post_array['post']->post_title = Gating\maybe_add_padlock_to_title( $post_title, $post_array['post']->ID );

			$final_post_title = $post_array['post']->post_title;

			$this->assertSame( $post_title, $final_post_title );
		}
	}

	// TODO: Update to reflect new padlock SVGs
	// /**
	//  * Check that post titles can have padlock set to the end of the title instead of the beginning.
	//  *
	//  * @return void
	//  */
	// public function test_padlock_location_can_be_changed() :  void {

	// 	// Set the padlock to display after the title
	// 	$settings                               = get_option( 'coil_exclusive_settings_group', [] );
	// 	$settings['coil_padlock_icon_position'] = 'after';
	// 	$settings['coil_title_padlock']         = true;
	// 	update_option( 'coil_exclusive_settings_group', $settings );

	// 	foreach ( self::$basic_posts as $post_array ) {
	// 		$post_title                     = $post_array['title'];
	// 		$post_array['post']->post_title = Gating\maybe_add_padlock_to_title( $post_title, $post_array['post']->ID );

	// 		if ( $post_array['visibility'] === 'exclusive' ) {
	// 			$post_title = $post_title . ' 🔒';
	// 		}

	// 		$final_post_title = $post_array['post']->post_title;

	// 		$this->assertSame( $post_title, $final_post_title );
	// 	}
	// }

	/**
	 * Tests taxonomy monetization meta field by creating a category and checking the correct monetization value is returned.
	 *
	 * @return void
	 */
	public function test_setting_and_getting_category_taxonomy_term_monetization() :  void {

		foreach ( self::$monetization_types as $category_info ) {
			wp_create_category( $category_info['name'] );
			Gating\set_term_status( get_cat_ID( $category_info['name'] ), '_coil_monetization_term_status', $category_info['monetization'] );
			$post_obj = self::factory()->post->create_and_get();
			wp_set_post_categories( $post_obj->ID, get_cat_ID( $category_info['name'] ), false );

			$taxonomy_monetization = Gating\get_taxonomy_term_status( $post_obj->ID, '_coil_monetization_term_status' );

			$this->assertSame( $category_info['monetization'], $taxonomy_monetization );
		}
	}

	/**
	 * Tests taxonomy visibility meta field by creating a category and checking the correct visibility value is returned.
	 *
	 * @return void
	 */
	public function test_setting_and_getting_category_taxonomy_term_visibility() :  void {

		foreach ( self::$visibility_types as $category_info ) {
			wp_create_category( $category_info['name'] );
			Gating\set_term_status( get_cat_ID( $category_info['name'] ), '_coil_visibility_term_status', $category_info['visibility'] );
			$post_obj = self::factory()->post->create_and_get();
			wp_set_post_categories( $post_obj->ID, get_cat_ID( $category_info['name'] ), false );

			$taxonomy_visibility = Gating\get_taxonomy_term_status( $post_obj->ID, '_coil_visibility_term_status' );

			$this->assertSame( $category_info['visibility'], $taxonomy_visibility );
		}
	}

	/**
	 * Tests taxonomy monetization meta field by creating a tag and checking the correct monetization value is returned.
	 *
	 * @return void
	 */
	public function test_setting_and_getting_tag_taxonomy_term_monetization() :  void {

		foreach ( self::$monetization_types as $tag_info ) {
			wp_create_tag( $tag_info['name'] );
			$tag    = get_term_by( 'name', $tag_info['name'], 'post_tag' );
			$tag_id = $tag->term_id;
			Gating\set_term_status( $tag_id, '_coil_monetization_term_status', $tag_info['monetization'] );
			$post_obj = self::factory()->post->create_and_get();
			wp_set_post_tags( $post_obj->ID, $tag_info['name'], false );

			$taxonomy_monetization = Gating\get_taxonomy_term_status( $post_obj->ID, '_coil_monetization_term_status' );

			$this->assertSame( $tag_info['monetization'], $taxonomy_monetization );
		}

	}

	/**
	 * Tests taxonomy visibility meta field by creating a tag and checking the correct visibility value is returned.
	 *
	 * @return void
	 */
	public function test_setting_and_getting_tag_taxonomy_term_visibility() :  void {

		foreach ( self::$visibility_types as $tag_info ) {
			wp_create_tag( $tag_info['name'] );
			$tag    = get_term_by( 'name', $tag_info['name'], 'post_tag' );
			$tag_id = $tag->term_id;
			Gating\set_term_status( $tag_id, '_coil_visibility_term_status', $tag_info['visibility'] );
			$post_obj = self::factory()->post->create_and_get();
			wp_set_post_tags( $post_obj->ID, $tag_info['name'], false );

			$taxonomy_visibility = Gating\get_taxonomy_term_status( $post_obj->ID, '_coil_visibility_term_status' );

			$this->assertSame( $tag_info['visibility'], $taxonomy_visibility );
		}

	}

	/**
	 * Tests if the correct monetization and visibility values are retrieved when a specific post is returned
	 * in cases that consider the combination of global, taxonomy or post monetization settings that have been used.
	 *
	 * @return void
	 */
	public function test_retrieving_content_status() :  void {

		// Create a post
		$post_obj = self::factory()->post->create_and_get();

		// Set the global default monetization and visibility for posts to monetized and exclusive which will define the post's status at this point
		$monetization_settings = [ 'post_monetization' => 'monetized' ];
		update_option( 'coil_general_settings_group', $monetization_settings );
		$visibility_settings = [ 'post_visibility' => 'exclusive' ];
		update_option( 'coil_exclusive_settings_group', $visibility_settings );

		$monetization_status = Gating\get_content_status( $post_obj->ID, 'monetization' );
		$visibility_status   = Gating\get_content_status( $post_obj->ID, 'visibility' );

		$this->assertSame( 'monetized', $monetization_status );
		$this->assertSame( 'exclusive', $visibility_status );

		// Add a category to the post which has monetization disabled.
		// This status will override the default and become the new status of the post.
		$category_name = 'Monetization Disabled Category';
		wp_create_category( $category_name );
		Gating\set_term_status( get_cat_ID( $category_name ), '_coil_monetization_term_status', 'not-monetized' );
		Gating\set_term_status( get_cat_ID( $category_name ), '_coil_visibility_term_status', 'public' );
		wp_set_post_categories( $post_obj->ID, get_cat_ID( $category_name ), false );

		$monetization_status = Gating\get_content_status( $post_obj->ID, 'monetization' );
		$visibility_status   = Gating\get_content_status( $post_obj->ID, 'visibility' );

		$this->assertSame( 'not-monetized', $monetization_status );
		$this->assertSame( 'public', $visibility_status );

		// Add a tag to the post which is monetized and exclusive'.
		// This status will override the category's status because it is stricter
		// and will become the new status of the post.
		$tag_name = 'Exclusive Tag';
		wp_create_tag( $tag_name );
		$tag    = get_term_by( 'name', $tag_name, 'post_tag' );
		$tag_id = $tag->term_id;
		Gating\set_term_status( $tag_id, '_coil_monetization_term_status', 'monetized' );
		Gating\set_term_status( $tag_id, '_coil_visibility_term_status', 'exclusive' );
		wp_set_post_tags( $post_obj->ID, $tag_name, false );

		$monetization_status = Gating\get_content_status( $post_obj->ID, 'monetization' );
		$visibility_status   = Gating\get_content_status( $post_obj->ID, 'visibility' );

		$this->assertSame( 'monetized', $monetization_status );
		$this->assertSame( 'exclusive', $visibility_status );

		// Add a post-level status with monetization enabled and public visibility.
		// This status has the highest priority and will become the new status of the post.
		Gating\set_post_status( $post_obj->ID, '_coil_monetization_post_status', 'monetized' );
		Gating\set_post_status( $post_obj->ID, '_coil_visibility_post_status', 'public' );

		$monetization_status = Gating\get_content_status( $post_obj->ID, 'monetization' );
		$visibility_status   = Gating\get_content_status( $post_obj->ID, 'visibility' );

		$this->assertSame( 'monetized', $monetization_status );
		$this->assertSame( 'public', $visibility_status );

		// Checking that changing the global defaults doesn't change the post's status.
		// Set the global default monetization for posts 'no'.
		$monetization_settings = [ 'post_monetization' => 'not-monetized' ];
		update_option( 'coil_general_settings_group', $monetization_settings );
		$visibility_settings = [ 'post_visibility' => 'public' ];
		update_option( 'coil_exclusive_settings_group', $visibility_settings );

		$monetization_status = Gating\get_content_status( $post_obj->ID, 'monetization' );
		$visibility_status   = Gating\get_content_status( $post_obj->ID, 'visibility' );

		$this->assertSame( 'monetized', $monetization_status );
		$this->assertSame( 'public', $visibility_status );

		// Checking that changing the category's status doesn't change the post's status.
		// Set the global default for posts to be exclusive.
		$category_name = 'Exclusive Category';
		wp_create_category( $category_name );
		Gating\set_term_status( get_cat_ID( $category_name ), '_coil_monetization_term_status', 'monetized' );
		Gating\set_term_status( get_cat_ID( $category_name ), '_coil_visibility_term_status', 'exclusive' );
		wp_set_post_categories( $post_obj->ID, get_cat_ID( $category_name ), false );

		$monetization_status = Gating\get_content_status( $post_obj->ID, 'monetization' );
		$visibility_status   = Gating\get_content_status( $post_obj->ID, 'visibility' );

		$this->assertSame( 'monetized', $monetization_status );
		$this->assertSame( 'public', $visibility_status );
	}
}
