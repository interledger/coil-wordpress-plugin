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
	 * Check that post titles get a padlock icon when the setting is enabled and posts are fully gated.
	 *
	 * @return void
	 */
	public function test_padlock_added_to_title_when_enabled_and_gated() :  void {

		// Ensuring the padlock display setting has been enabled
		$options                       = get_option( 'coil_exclusive_settings_group', [] );
		$options['coil_title_padlock'] = true;
		update_option( 'coil_exclusive_settings_group', $options );

		foreach ( self::$basic_posts as $gating => $post_obj ) {
			$post_title           = 'Post Title';
			$post_obj->post_title = Gating\maybe_add_padlock_to_title( $post_title, $post_obj->ID );
			if ( $gating === 'gate-all' ) {
				$post_title = '🔒 ' . $post_title;
			}

			$final_post_title = $post_obj->post_title;

			$this->assertSame( $post_title, $final_post_title );
		}
	}

	/**
	 * Check that post titles do no get a padlock icon when option is disabled.
	 *
	 * @return void
	 */
	public function test_padlock_not_added_to_title_when_disabled() :  void {

		// Ensuring the padlock display setting has been disabled
		$options                       = get_option( 'coil_appearance_settings_group', [] );
		$options['coil_title_padlock'] = false;
		update_option( 'coil_appearance_settings_group', $options );

		foreach ( self::$basic_posts as $gating => $post_obj ) {
			$post_title           = 'Post Title';
			$post_obj->post_title = Gating\maybe_add_padlock_to_title( $post_title, $post_obj->ID );

			$final_post_title = $post_obj->post_title;

			$this->assertSame( $post_title, $final_post_title );
		}
	}

	/**
	 * Tests taxonomy gating by creating a category and checking the correct gating value is returned.
	 *
	 * @return void
	 */
	public function test_setting_and_getting_category_taxonomy_term_gating() :  void {

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

			$taxonomy_gating = Gating\get_taxonomy_term_gating( $post_obj->ID );

			$this->assertSame( $gating_type, $taxonomy_gating );
		}
	}

	/**
	 * Tests taxonomy gating by creating a tag and checking the correct gating value is returned.
	 *
	 * @return void
	 */
	public function test_setting_and_getting_tag_taxonomy_term_gating() :  void {

		$tags = [
			'No Monetization'      => 'no',
			'Monetized and Public' => 'no-gating',
			'Fully Gated'          => 'gate-all',
		];

		foreach ( $tags as $tag_name => $gating_type ) {
			wp_create_tag( $tag_name );
			$tag    = get_term_by( 'name', $tag_name, 'post_tag' );
			$tag_id = $tag->term_id;
			Gating\set_term_gating( $tag_id, $gating_type );
			$post_obj = self::factory()->post->create_and_get();
			wp_set_post_tags( $post_obj->ID, $tag_name, false );

			$taxonomy_gating = Gating\get_taxonomy_term_gating( $post_obj->ID );

			$this->assertSame( $gating_type, $taxonomy_gating );
		}
	}

	/**
	 * Tests if the correct global default gating settings can be correctly retrieved.
	 *
	 * @return void
	 */
	public function test_retrieving_default_global_gating_settings() :  void {

		$gating_options = [
			[
				'post' => 'no',
				'page' => 'no',
			],
			[
				'post' => 'no-gating',
				'page' => 'no-gating',
			],
			[
				'post' => 'gate-all',
				'page' => 'gate-all',
			],
		];

		foreach ( $gating_options as $global_gating ) {
			update_option( 'coil_exclusive_settings_group', $global_gating );

			$global_default = Gating\get_global_posts_gating();

			$this->assertSame( $global_gating, $global_default );
		}
	}

	/**
	 * Tests if the correct gating value retrieved when a specific post is returned
	 * in cases that consider the combination of global, taxonomy or post monetization settings that have been used.
	 *
	 * @return void
	 */
	public function test_retrieving_content_gating() :  void {

		// Create a post
		$post_obj = self::factory()->post->create_and_get();

		// Set the global default monetization for posts 'gate-all' which will define the post's gating status at this point
		$gating_settings = [ 'post' => 'gate-all' ];
		update_option( 'coil_exclusive_settings_group', $gating_settings );

		$gating_status = Gating\get_content_gating( $post_obj->ID );

		$this->assertSame( 'gate-all', $gating_status );

		// Add a category to the post which has the monetization status 'no'.
		// This status will override the default and become the new monetization status of the post.
		$category_name = 'Monetization Disabled Category';
		wp_create_category( $category_name );
		Gating\set_term_gating( get_cat_ID( $category_name ), 'no' );
		wp_set_post_categories( $post_obj->ID, get_cat_ID( $category_name ), false );

		$gating_status = Gating\get_content_gating( $post_obj->ID );

		$this->assertSame( 'no', $gating_status );

		// Add a tag to the post which has the monetization status 'gate-all'.
		// This status will override the category's status because it has a stricter monetization status
		// and will become the new monetization status of the post.
		$tag_name = 'Fully Gated Tag';
		wp_create_tag( $tag_name );
		$tag    = get_term_by( 'name', $tag_name, 'post_tag' );
		$tag_id = $tag->term_id;
		Gating\set_term_gating( $tag_id, 'gate-all' );
		wp_set_post_tags( $post_obj->ID, $tag_name, false );

		$gating_status = Gating\get_content_gating( $post_obj->ID );

		$this->assertSame( 'gate-all', $gating_status );

		// Add a post-level monetization status with the value 'no-gating'.
		// This status will override the default and become the new monetization status of the post.
		Gating\set_post_gating( $post_obj->ID, 'no-gating' );

		$gating_status = Gating\get_content_gating( $post_obj->ID );

		$this->assertSame( 'no-gating', $gating_status );

		// Checking that changing the global default doesn't change the post's monetization status becasue it has been set at post-level
		// Set the global default monetization for posts 'no'.
		$gating_settings = [ 'post' => 'no' ];
		update_option( 'coil_exclusive_settings_group', $gating_settings );

		$gating_status = Gating\get_content_gating( $post_obj->ID );

		$this->assertNotSame( 'no', $gating_status );

		// Checking that changing the category's monetization status doesn't change the post's monetization status becasue it has been set at post-level
		// Set the global default monetization for posts 'gate-all'.
		$category_name = 'Fully Gated Category';
		wp_create_category( $category_name );
		Gating\set_term_gating( get_cat_ID( $category_name ), 'gate-all' );
		wp_set_post_categories( $post_obj->ID, get_cat_ID( $category_name ), false );

		$gating_status = Gating\get_content_gating( $post_obj->ID );

		$this->assertNotSame( 'gate-all', $gating_status );

	}
}
