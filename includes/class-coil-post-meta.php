<?php
/**
 * Coil for WordPress - Post Meta.
 *
 * Registers the post meta.
 *
 * @author   Sébastien Dumont
 * @category Classes
 * @package  Coil/Classes/Post Meta
 * @license  GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coil post meta class.
 */
class Coil_Post_Meta {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'rest_api_init', [ $this, 'register_meta' ] );
	}

	/**
	 * Register meta.
	 */
	public function register_meta() {
		register_meta( 'post', '_coil_monetize_post_status', [
			'show_in_rest'  => true,
			'single'        => true,
			'type'          => 'string',
			'auth_callback' => [ $this, 'auth_callback' ],
		] );
	} // END register_meta()

	/**
	 * Determine if the current user can edit posts.
	 *
	 * @return bool True when can edit posts, else false.
	 */
	public function auth_callback() {
		return current_user_can( 'edit_posts' );
	} // END auth_callback()

} // END class

return new Coil_Post_Meta();
