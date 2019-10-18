<?php
/**
 * Coil - Admin.
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

if ( ! class_exists( 'Coil_Admin' ) ) {

	class Coil_Admin {

		/**
		 * Constructor
		 *
		 * @access public
		 */
		public function __construct() {
			// Include classes.
			self::includes();

			// Add admin page.
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_init', array( $this, 'save_settings' ) );
		} // END __construct()

		/**
		 * Include any classes we need within admin.
		 *
		 * @access public
		 */
		public function includes() {
			include( dirname( __FILE__ ) . '/class-coil-admin-action-links.php' ); // Action Links
			include( dirname( __FILE__ ) . '/class-coil-admin-assets.php' );       // Admin Assets
			include( dirname( __FILE__ ) . '/class-coil-admin-notices.php' );      // Plugin Notices
		} // END includes()

		/**
		 * Add Coil to the menu.
		 *
		 * @access public
		 */
		public function admin_menu() {
			$title = sprintf( esc_attr__( 'Settings for %s', 'coil-for-wp' ), 'Coil' );

			add_menu_page(
				$title,
				'Coil',
				apply_filters( 'coil_screen_capability', 'manage_options' ),
				'coil',
				array( $this, 'coil_page' ),
				COIL_URL_PATH . '/assets/images/coil-favicon-16.png'
			);
		} // END admin_menu()

		/**
		 * Coil Page
		 *
		 * @access public
		 */
		public function coil_page() {
			include_once( dirname( __FILE__ ) . '/views/html-settings.php' );
		} // END coil_page()

		/**
		 * Saves the coil settings when requested by someone with permission.
		 *
		 * @access public
		 */
		public function save_settings() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Sorry but you do not have permission for this action.', 'coil-for-wp' ) );
			}

			// Add nonce for security and authentication.
			$nonce_name = isset( $_POST['coil_for_wp_settings_nonce'] ) ? $_POST['coil_for_wp_settings_nonce'] : '';

			if ( wp_verify_nonce( $nonce_name, 'coil_for_wp_settings_action' ) ) {
				$current_pointer = get_option( 'coil_payout_pointer_id' );
				$new_pointer     = ! empty( $_POST['coil_payout_pointer_id'] ) ? sanitize_text_field( $_POST['coil_payout_pointer_id'] ) : '';

				// Only save the pointer ID if one is not set or does not match the current pointer...
				if ( ! empty( $new_pointer ) && $current_pointer != $new_pointer ) {
					update_option( 'coil_payout_pointer_id', $new_pointer );
				}
				// ... otherwise delete the option.
				else {
					delete_option( 'coil_payout_pointer_id' );
				}
			}
		} // END save_settings()

		/**
		 * These are the only screens Coil will focus 
		 * on displaying notices or enqueue scripts/styles.
		 *
		 * @access public
		 * @static
		 * @return array
		 */
		public static function coil_get_admin_screens() {
			return array(
				'dashboard',
				'plugins',
				'toplevel_page_coil'
			);
		} // END coil_get_admin_screens()

		/**
		 * Seconds to words.
		 *
		 * Forked from: https://github.com/thatplugincompany/login-designer/blob/master/includes/admin/class-login-designer-feedback.php
		 *
		 * @access public
		 * @static
		 * @param  string $seconds Seconds in time.
		 * @return string
		 */
		public static function coil_seconds_to_words( $seconds ) {
			// Get the years.
			$years = ( intval( $seconds ) / YEAR_IN_SECONDS ) % 100;
			if ( $years > 1 ) {
				/* translators: Number of years */
				return sprintf( __( '%s years', 'coil-for-wp' ), $years );
			} elseif ( $years > 0 ) {
				return __( 'a year', 'coil-for-wp' );
			}

			// Get the weeks.
			$weeks = ( intval( $seconds ) / WEEK_IN_SECONDS ) % 52;
			if ( $weeks > 1 ) {
				/* translators: Number of weeks */
				return sprintf( __( '%s weeks', 'coil-for-wp' ), $weeks );
			} elseif ( $weeks > 0 ) {
				return __( 'a week', 'coil-for-wp' );
			}

			// Get the days.
			$days = ( intval( $seconds ) / DAY_IN_SECONDS ) % 7;
			if ( $days > 1 ) {
				/* translators: Number of days */
				return sprintf( __( '%s days', 'coil-for-wp' ), $days );
			} elseif ( $days > 0 ) {
				return __( 'a day', 'coil-for-wp' );
			}

			// Get the hours.
			$hours = ( intval( $seconds ) / HOUR_IN_SECONDS ) % 24;
			if ( $hours > 1 ) {
				/* translators: Number of hours */
				return sprintf( __( '%s hours', 'coil-for-wp' ), $hours );
			} elseif ( $hours > 0 ) {
				return __( 'an hour', 'coil-for-wp' );
			}

			// Get the minutes.
			$minutes = ( intval( $seconds ) / MINUTE_IN_SECONDS ) % 60;
			if ( $minutes > 1 ) {
				/* translators: Number of minutes */
				return sprintf( __( '%s minutes', 'coil-for-wp' ), $minutes );
			} elseif ( $minutes > 0 ) {
				return __( 'a minute', 'coil-for-wp' );
			}

			// Get the seconds.
			$seconds = intval( $seconds ) % 60;
			if ( $seconds > 1 ) {
				/* translators: Number of seconds */
				return sprintf( __( '%s seconds', 'coil-for-wp' ), $seconds );
			} elseif ( $seconds > 0 ) {
				return __( 'a second', 'coil-for-wp' );
			}
		} // END coil_seconds_to_words()

	} // END class

} // END if class exists

return new Coil_Admin();
