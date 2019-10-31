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
			add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta'], 10, 3 );
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
					'settings' => '<a href="' . add_query_arg( [ 'page' => 'coil' ], admin_url( 'admin.php' ) ) . '" aria-label="' . sprintf( esc_attr__( 'Settings for %s', 'coil-monetize-content' ), 'Coil' ) . '">' . esc_attr__( 'Settings', 'coil-monetize-content' ) . '</a>',
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
			if ( $file == plugin_basename( COIL_PLUGIN_FILE ) ) {
				$metadata[ 1 ] = sprintf( __( 'By %s', 'coil-monetize-content' ), '<a href="' . esc_url( 'https://pragmatic.agency/' ) . '" aria-label="' . esc_attr__( 'View the agency site', 'coil-monetize-content' ) . '">Pragmatic</a> ' );
				$metadata[ 1 ] .= sprintf( __( 'and %s', 'coil-monetize-content' ), '<a href="' . esc_url( 'https://coil.com/' ) . '" aria-label="' . esc_attr__( 'View the Coil site', 'coil-monetize-content' ) . '">Coil</a>' );

				$row_meta = [
					'docs' => '<a href="' . apply_filters( 'coil_docs_url', esc_url( COIL_DOCUMENTATION_URL ) ) . '" aria-label="' . sprintf( esc_attr__( 'View %s documentation', 'coil-monetize-content' ), 'Coil' ) . '" target="_blank">' . esc_attr__( 'Documentation', 'coil-monetize-content' ) . '</a>',
					'community' => '<a href="' . esc_url( COIL_SUPPORT_URL ) . '" aria-label="' . esc_attr__( 'Get support from the community', 'coil-monetize-content' ). '" target="_blank">' . esc_attr__( 'Community Support', 'coil-monetize-content' ) . '</a>',
				];

				$metadata = array_merge( $metadata, $row_meta );
			}

			return $metadata;
		} // END plugin_row_meta()

	} // END class

} // END if class exists

return new Coil_Admin_Action_Links();
