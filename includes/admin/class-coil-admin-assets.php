<?php
/**
 * Coil - Admin Assets.
 *
 * @author   Sébastien Dumont
 * @category Admin
 * @package  Coil/Admin
 * @license  GPL-2.0+
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Coil_Admin_Assets' ) ) {

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
			$suffix    = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

			if ( in_array( $screen_id, Coil_Admin::coil_get_admin_screens() ) ) {
				wp_register_style( COIL_SLUG . '_admin', COIL_URL_PATH . '/assets/css/admin/coil' . $suffix . '.css' );
				wp_enqueue_style( COIL_SLUG . '_admin' );
			}
		} // END admin_styles()

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

			if ( $screen_id == 'toplevel_page_coil' ) {
				$classes = ' coil ';
			}

			return $classes;
		} // END admin_body_class()

	} // END class

} // END if class exists

return new Coil_Admin_Assets();
