<?php
/**
 * Coil - Installation related functions and actions.
 *
 * @author   Sébastien Dumont
 * @category Classes
 * @package  Coil/Classes/Install
 * @license  GPL-2.0+
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Coil_Install' ) ) {

	class Coil_Install {

		/**
		 * Constructor.
		 *
		 * @access public
		 */
		public function __construct() {
			// Checks version of Coil and install/update if needed.
			add_action( 'init', array( $this, 'check_version' ), 5 );

			// Redirect to settings page once activated.
			add_action( 'activated_plugin', array( $this, 'redirect_settings' ) );
		} // END __construct()

		/**
		 * Check plugin version and run the updater if necessary.
		 *
		 * This check is done on all requests and runs if the versions do not match.
		 *
		 * @access public
		 * @static
		 */
		public static function check_version() {
			if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'coil_version' ), COIL_VERSION, '<' ) && current_user_can( 'install_plugins' ) ) {
				self::install();
				do_action( 'coil_updated' );
			}
		} // END check_version()

		/**
		 * Install Coil.
		 *
		 * @access public
		 * @static
		 */
		public static function install() {
			if ( ! is_blog_installed() ) {
				return;
			}

			// Check if we are not already running this routine.
			if ( 'yes' === get_transient( 'coil_installing' ) ) {
				return;
			}

			// If we made it till here nothing is running yet, lets set the transient now for five minutes.
			set_transient( 'coil_installing', 'yes', MINUTE_IN_SECONDS * 5 );
			if ( ! defined( 'COIL_INSTALLING' ) ) {
				define( 'COIL_INSTALLING', true );
			}

			// Set activation date.
			self::set_install_date();

			// Update plugin version.
			self::update_version();

			delete_transient( 'coil_installing' );

			do_action( 'coil_installed' );
		} // END install()

		/**
		 * Update plugin version to current.
		 *
		 * @access private
		 * @static
		 */
		private static function update_version() {
			update_option( 'coil_version', COIL_VERSION );
		} // END update_version()

		/**
		 * Set the time the plugin was installed.
		 *
		 * @access public
		 * @static
		 */
		public static function set_install_date() {
			add_site_option( 'coil_install_date', time() );
		} // END set_install_date()

		/**
		 * Redirects to the settings page upon plugin activation.
		 *
		 * @access public
		 * @static
		 * @param  string $plugin The activate plugin name.
		 */
		public static function redirect_settings( $plugin ) {
			// Prevent redirect if plugin name does not match.
			if ( $plugin !== plugin_basename( COIL_PLUGIN_FILE ) ) {
				return;
			}

			$settings = add_query_arg( array( 
				'page' => 'coil'
			), admin_url( 'admin.php' ) );

			/**
			 * Should Coil be installed via WP-CLI,
			 * display a link to the Settings page.
			 */
			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				WP_CLI::log(
					WP_CLI::colorize(
						'%y' . sprintf( '🎉 %1$s %2$s', __( 'Get started with %3$s here:', 'coil-for-wp' ), $settings, esc_html__( 'Coil', 'coil-for-wp' ) ) . '%n'
					)
				);
				return;
			}

			// If activated on a Multisite, don't redirect.
			if ( is_multisite() ) {
				return;
			}

			wp_safe_redirect( $settings );
			exit;
		} // END redirect_settings()

	} // END class.

} // END if class exists.

return new Coil_Install();
