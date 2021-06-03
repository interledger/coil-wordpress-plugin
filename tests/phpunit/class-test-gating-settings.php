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
		$options = get_option( 'coil_monetization_settings_group', [] );
		$options[ 'coil_title_padlock' ] = true;
		update_option( 'coil_monetization_settings_group', $options );

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
		$options = get_option( 'coil_monetization_settings_group', [] );
		$options[ 'coil_title_padlock' ] = false;
		update_option( 'coil_monetization_settings_group', $options );

		foreach ( self::$basic_posts as $gating => $post_obj ) {
			$post_title = '';
			switch ($gating) {
				case 'no':
					$post_title = 'This is a post with no gating';
					$post_obj->post_title = $post_title;
				  	break;
				case 'no-gating':
					$post_title = 'This post is monetized and public';
					$post_obj->post_title = $post_title;
					break;
				case 'gate-all':
					$post_title = 'This is a post that is fully gated';
					$post_obj->post_title = $post_title;
					break;
				case 'gate-tagged-blocks':
					$post_title = 'This is a post that has partial gating';
					$post_obj->post_title = $post_title;
					$post_obj->post_title = Gating\maybe_add_padlock_to_title( $post_title, $post_obj->ID );
					break;
			  }
			  $post_obj->post_title = Gating\maybe_add_padlock_to_title( $post_title, $post_obj->ID );
			  $this->assertSame( $post_obj->post_title, $post_title );
		}
	}

	/**
	 * Check that excerpts are empty unless one as been set explicitly.
	 * 
	 * @return void
	 */
	public function test_excerpt_empty_by_default_when_post_is_gated() :  void {

		foreach ( self::$basic_posts as $gating => $post_obj ) {
			// Ensuring no explicit excerpt has been set
			$post_obj->post_excerpt = '';
			$post_obj->post_content = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.';
			$this->assertSame( '', $post_obj->post_excerpt );
			$post_obj->post_content = Gating\maybe_restrict_content( $post_obj->post_content );
			switch ($gating) {
				case 'no':
				case 'no-gating':
					$this->assertSame( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', $post_obj->post_content );
					break;
				case 'gate-all':
				case 'gate-tagged-blocks':
					$this->assertSame( '', $post_obj->post_content );
					break;
			  }
		}
	}
}
