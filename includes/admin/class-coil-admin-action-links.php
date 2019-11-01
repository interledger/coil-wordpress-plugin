<?php
/**
 * Coil - Admin Action Links.
 *
 * Adds links to Coil on the plugins page.
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

if ( ! class_exists( 'Coil_Admin_Action_Links' ) ) {

	class Coil_Admin_Action_Links {

		/**
		 * Constructor
		 *
		 * @access public
		 */
		public function __construct() {
			add_filter( 'plugin_action_links_' . plugin_basename( COIL_PLUGIN_FILE ), [ $this, 'plugin_action_links' ] );
			add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 3 );
		} // END __construct()

		/**
		 * Plugin action links.
		 *
		 * @access public
		 * @param  array $links An array of plugin links.
		 * @return array $links
		 */
		public function plugin_action_links( $links ) {
			if ( current_user_can( 'manage_options' ) ) {
				$action_links = [
					'settings' => '<a href="' . add_query_arg( [ 'page' => 'coil' ], admin_url( 'admin.php' ) ) . '" aria-label="' . esc_attr__( 'Settings for Coil', 'coil-monetize-content' ) . '">' . esc_attr__( 'Settings', 'coil-monetize-content' ) . '</a>',
				];

				return array_merge( $action_links, $links );
			}

			return $links;
		} // END plugin_action_links()

		/**
		 * Plugin row meta links
		 *
		 * @access public
		 * @param  array  $metadata An array of the plugin's metadata.
		 * @param  string $file     Path to the plugin file.
		 * @param  array  $data     Plugin Information
		 * @return array  $metadata
		 */
		public function plugin_row_meta( $metadata, $file, $data ) {
			if ( $file === plugin_basename( COIL_PLUGIN_FILE ) ) {
				$row_meta = [
					'community' => '<a href="' . esc_url( 'https://wordpress.org/support/plugin/coil-monetize-content/' ) . '">' . esc_html__( 'Support forum', 'coil-monetize-content' ) . '</a>',
				];

				$metadata = array_merge( $metadata, $row_meta );
			}

			return $metadata;
		} // END plugin_row_meta()

	} // END class

} // END if class exists

return new Coil_Admin_Action_Links();
