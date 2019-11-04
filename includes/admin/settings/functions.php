<?php
declare(strict_types=1);

namespace Coil\Admin\Settings;

/**
 * Add action links to the list on the plugins screen.
 *
 * @param array $links An array of action links.
 *
 * @return array $links Updated array of action links.
 */
function add_plugin_action_links( array $links ) : array {
	if ( ! current_user_can( 'manage_options' ) ) {
		return $links;
	}

	$action_links = [
		'settings' => '<a href="' . add_query_arg( [ 'page' => 'coil' ], admin_url( 'admin.php' ) ) . '" aria-label="' . esc_attr__( 'Settings for Coil', 'coil-monetize-content' ) . '">' . esc_attr__( 'Settings', 'coil-monetize-content' ) . '</a>',
	];

	return array_merge( $action_links, $links );
}

/**
 * Add extra information to the meta section on the list on the plugins screen.
 *
 * @param array  $metadata An array of the plugin meta.
 * @param string $file     Path to this plugin's main file. Used to identify which row we're in.
 *
 * @return array $metadata Updated array of plugin meta.
 */
function add_plugin_meta_link( $metadata, $file ) : array {
	if ( $file !== 'coil-monetize-content/plugin.php' ) {
		return $metadata;
	}

	$row_meta = [
		'community' => '<a href="' . esc_url( 'https://wordpress.org/support/plugin/coil-monetize-content/' ) . '">' . esc_html__( 'Support forum', 'coil-monetize-content' ) . '</a>',
	];

	return array_merge( $metadata, $row_meta );
}

/**
 * Add Coil settings to the admin navigation menu.
 *
 * @return void
 */
function register_admin_menu() : void {
	add_menu_page(
		esc_attr__( 'Settings for Coil', 'coil-monetize-content' ),
		_x( 'Coil', 'admin menu name', 'coil-monetize-content' ),
		apply_filters( 'coil_settings_capability', 'manage_options' ),
		'coil',
		__NAMESPACE__ . '\render_coil_settings_screen'
	);
}

/**
 * Render the Coil setting screen.
 *
 * @return void
 */
function render_coil_settings_screen() : void {
	include_once( dirname( __FILE__ ) . '/html-settings.php' );
}

/**
 * Maybe save the Coil admin settings.
 *
 * @return void
 */
function maybe_save_coil_admin_settings() : void {

	if (
		! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ||
		empty( $_REQUEST['coil_for_wp_settings_nonce'] )
	) {
		return;
	}

	// Check the nonce.
	check_admin_referer( 'coil_for_wp_settings_action', 'coil_for_wp_settings_nonce' );

	$payment_pointer_id = ! empty( $_POST['coil_payout_pointer_id'] ) ? $_POST['coil_payout_pointer_id'] : '';
	$content_container  = ! empty( $_POST['coil_content_container'] ) ? $_POST['coil_content_container'] : '';

	$coil_options = [
		'coil_payout_pointer_id' => sanitize_text_field( $payment_pointer_id ),
		'coil_content_container' => sanitize_text_field( $content_container ),
	];

	foreach ( $coil_options as $key => $value ) {
		if ( ! empty( $value ) ) {
			update_option( $key, $value );
		} else {
			delete_option( $key );
		}
	}
}
