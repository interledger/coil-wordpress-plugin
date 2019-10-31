<?php
/**
 * Coil for WordPress - Compatibility
 *
 * Provides functions to check the version of WordPress
 * and if the Gutenberg plugin is installed.
 *
 * @author   Sébastien Dumont
 * @category Classes
 * @package  Coil/Classes/Compatibility
 * @license  GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coil compatibility class.
 */
class Coil_Compatibility {

	/**
	 * The Constructor.
	 */
	public function __construct() {
		add_filter( 'initialize-monetization-js', [ $this, 'pass_post_excerpt_js' ], 10, 2 );
	}

	/**
	 * Checks if the Gutenberg plugin is installed.
	 *
	 * @access public
	 * @return bool true|false
	 */
	public static function is_gutenberg_installed() {
		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
			return true;
		}
	} // END is_gutenberg_installed()

	/**
	 * Identifies if the post is being edited via Gutenberg.
	 *
	 * @access public
	 * @param
	 * @return bool true|false
	 */
	public static function is_post_using_gutenberg( $post ) {
		// If user loaded with the Gutenberg editor (plugin version) then don't register the meta box.
		if ( function_exists( 'use_block_editor_for_post' ) && use_block_editor_for_post( $post ) ) {
			return true;
		}

		return false;
	} // END is_post_using_gutenberg()

	/**
	 * Checks if the version of WordPress is equal to 5.3 or greater.
	 *
	 * @access public
	 * @global string $wp_version - The version of WordPress.
	 * @return bool true|false
	 */
	public static function is_wp_version_gte_5_3() {
		global $wp_version;

		if ( version_compare( str_replace( '-', '.', preg_replace( "/[a-zA-Z\/]/", "", $wp_version ) ), '5.3.0', '>=' ) ) {
			return true;
		}

		return false;
	} // END is_wp_version_gte_5_3()

	/**
	 * Checks if the version of WordPress is equal to 5.2.5 or less.
	 *
	 * @access public
	 * @global string $wp_version - The version of WordPress.
	 * @return bool true|false
	 */
	public static function is_wp_version_lte_5_2() {
		global $wp_version;

		if ( version_compare( preg_replace( "/[a-zA-Z\/]/", "", $wp_version ), '5.2.5', '<' ) ) {
			return true;
		}

		return false;
	} // END is_wp_version_lte_5_2()

	/**
	 * This adds the post excerpt if WordPress is higher than version 4.9.
	 *
	 * @access public
	 * @param  array  $params - The JavaScript parameters before filtering.
	 * @param  int    $post_ID - The post ID number.
	 * @global string $wp_version - The version of WordPress.
	 * @return array  $params - The JavaScript parameters after filtering.
	 */
	public function pass_post_excerpt_js( $params, $post_ID ) {
		global $wp_version;

		if ( version_compare( $wp_version, '4.9', '>' ) ) {
			$params[ 'post_excerpt' ] = get_the_excerpt( $post_ID );
		}

		return $params;
	} // END pass_post_excerpt_js()

} // END class.

return new Coil_Compatibility();
