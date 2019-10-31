<?php
/**
 * Coil for WordPress - Load assets for our blocks.
 *
 * @author   Sébastien Dumont
 * @category Classes
 * @package  Coil/Classes/Block Assets
 * @license  GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coil block assets class.
 */
class Coil_Block_Assets {

	/**
	 * This class instance.
	 *
	 * @var Coil_Block_Assets
	 */
	private static $instance;

	/**
	 * Registers the plugin.
	 */
	public static function register() {
		if ( null === self::$instance ) {
			self::$instance = new Coil_Block_Assets();
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

		add_action( 'enqueue_block_assets', [ $this, 'block_assets' ] );
		add_action( 'init', [ $this, 'editor_assets' ], 9999 );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_scripts' ] );
		add_action( 'the_post', [ $this, 'frontend_scripts' ] );
	} // END __construct()

	/**
	 * Enqueue block assets for use within Gutenberg.
	 *
	 * @access public
	 */
	public function block_assets() {
		// Styles.
		wp_enqueue_style(
			$this->_slug . '-frontend',
			COIL_URL_PATH . '/dist/blocks.style.build' . COIL_ASSET_SUFFIX . '.css',
			[],
			COIL_VERSION
		);
	} // END block_assets()

	/**
	 * Enqueue block assets for use within Gutenberg.
	 *
	 * @access public
	 */
	public function editor_assets() {
		if ( ! is_admin() ) {
			return;
		}

		if ( ! $this->is_edit_or_new_admin_page() ) { // Load on allowed pages only.
			return;
		}

		// Styles.
		wp_register_style(
			$this->_slug . '-editor',
			COIL_URL_PATH . '/dist/blocks.editor.build' . COIL_ASSET_SUFFIX . '.css',
			[],
			COIL_VERSION
		);

		// Scripts.
		wp_register_script(
			$this->_slug . '-editor',
			COIL_URL_PATH . '/dist/blocks.build.js',
			[ 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-plugins', 'wp-components', 'wp-edit-post', 'wp-api', 'wp-editor', 'wp-hooks', 'wp-data' ],
			time(),
			false
		);
	} // END editor assets()

	/**
	 * Enqueue front-end assets for blocks.
	 *
	 * @access public
	 */
	public function frontend_scripts() {
		// Custom scripts are not allowed in AMP, so short-circuit.
		if ( Coil()->is_amp() ) {
			return;
		}

		// Define where the asset is loaded from.
		$dir = Coil()->asset_source( 'js' );

		// Define where the vendor asset is loaded from.
		$vendors_dir = Coil()->asset_source( 'js', 'vendors' );

		// Split Content block.
		/*if ( function_exists( 'has_block' ) && has_block( $this->_slug . '/split-content' ) ) {
			wp_enqueue_script(
				$this->_slug . '-split-content',
				$dir . $this->_slug . '-split-content' . COIL_ASSET_SUFFIX . '.js',
				array( 'jquery' ),
				COIL_VERSION,
				true
			);
		}*/
	} // END frontend_scripts()

	/**
	 * Checks if admin page is the 'edit' or 'new-post' screen.
	 *
	 * @access public
	 * @return bool true or false
	 */
	public function is_edit_or_new_admin_page() {
		global $pagenow;

		return ( is_admin() && ( $pagenow === 'post.php' || $pagenow === 'post-new.php' ) );
	} // END is_edit_or_new_admin_page()

} // END class

Coil_Block_Assets::register();
