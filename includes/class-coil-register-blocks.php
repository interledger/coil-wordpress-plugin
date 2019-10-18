<?php
/**
 * Coil for WordPress - Register Blocks
 *
 * @author   Sébastien Dumont
 * @category Classes
 * @package  Coil/Classes/Register Blocks
 * @license  GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coil register blocks class.
 */
class Coil_Register_Blocks {

	/**
	 * This class instance.
	 *
	 * @var Coil_Register_Blocks
	 */
	private static $instance;

	/**
	 * Registers the blocks.
	 */
	public static function register() {
		if ( null === self::$instance ) {
			self::$instance = new Coil_Register_Blocks();
		}
	}

	/**
	 * The Block Slug.
	 *
	 * @var string $_slug
	 */
	private $_slug;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->_slug = 'coil';

		add_action( 'init', array( $this, 'register_blocks' ), 99 );
	} // END __construct()

	/**
	 * Registers Coil Blocks.
	 *
	 * @access public
	 */
	public function register_blocks() {
		// Return early if this function does not exist.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Shortcut for the slug.
		$slug = $this->_slug;

		/**
		 * Register Gutenberg block on server-side.
		 *
		 * Register the block on server-side to ensure that the block
		 * scripts and styles for both frontend and backend are
		 * enqueued when the editor loads.
		 *
		 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
		 */
		register_block_type(
			$slug . '/split-content', array(
				'editor_script' => $slug . '-editor',
				'editor_style'  => $slug . '-editor',
				'style'         => $slug . '-frontend',
			)
		);
	}

} // END class

Coil_Register_Blocks::register();
