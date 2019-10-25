<?php
/**
 * Coil for WordPress - Filters content based on monetization status.
 *
 * @author   Sébastien Dumont
 * @category Classes
 * @package  Coil/Classes/Gate Content
 * @license  GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coil gate content class.
 */
class Coil_Gate_Content {

	/**
	 * This class instance.
	 *
	 * @var Coil_Gate_Content
	 */
	private static $instance;

	/**
	 * Instance of the class.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new Coil_Gate_Content();
		}
	}

	/**
	 * The Constructor.
	 */
	public function __construct() {
		add_filter( 'the_content', array( $this, 'is_content_gated' ) );
	}

	/**
	 * Is the content gated?
	 *
	 * @access public
	 * @param  string $content
	 * @global object WP_Post - The post object.
	 * @return string $content
	 */
	public function is_content_gated ( $content ) {
		global $post;

		$monetize_status = get_post_meta( $post->ID, '_coil_monetize_post_status', true );

		if ( ! is_singular() && ! empty( $monetize_status ) ) {
			switch( $monetize_status ) {
				case 'gate-all': // Monetize all content.
					return '<p>🔒 ' . esc_html__( 'The contents of this article is for subscribers only!', 'coil-for-wp' ) . '</p>';
				break;

				case 'gate-tagged-blocks': // Monetized Specific Content
					$public_content = '<p>🔓 '. esc_html__( 'This article is monetized and some content is for subscribers only.', 'coil-for-wp' ) . '</p>';
					$public_content .= $content;

					return $public_content;
				break;

				case 'no': // Not monetized.
				case 'no-gate': // Just Monetized
				default:
					return $content;
				break;
			}
		}

		// If content is not modified then return as normal.
		// Can be filtered.
		return apply_filters( 'coil_returned_content', $content, $post, $monetize_status );
	} // END is_gated_content()

} // END class

Coil_Gate_Content::instance();
