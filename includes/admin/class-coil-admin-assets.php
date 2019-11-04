<?php
/**
 * Coil - Admin Assets.
 *
 * @author   Sébastien Dumont
 * @category Admin
 * @package  Coil/Admin
 * @license  GPL-2.0+
 */

class Coil_Admin_Assets {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ], 10 );

		// Adds admin body classes.
		add_filter( 'admin_body_class', [ $this, 'admin_body_class' ] );
	} // END __construct()

	/**
	 * Registers and enqueues Stylesheets.
	 *
	 * @access public
	 */
	public function admin_styles() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$screens = [
			'dashboard',
			'plugins',
			'toplevel_page_coil',
		];

		if ( ! in_array( $screen_id, $screens, true ) ) {
			return;
		}

		wp_enqueue_style(
			'coil_admin',
			esc_url_raw( plugin_dir_url( dirname( __DIR__ ) ) . 'assets/css/admin/coil' . $suffix . '.css' ),
			[],
			\Coil\PLUGIN_VERSION
		);
	}

	/**
	 * Adds admin body class for Coil page.
	 *
	 * @access public
	 * @param  string $classes
	 * @return string $classes
	 */
	public function admin_body_class( $classes ) {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( $screen_id === 'toplevel_page_coil' ) {
			$classes = ' coil ';
		}

		return $classes;
	}
}
