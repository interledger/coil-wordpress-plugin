<?php
declare(strict_types=1);
/**
 * Coil admin screens and options.
 */

namespace Coil\Admin;

use Coil;
use Coil\Gating;
use const Coil\COIL__FILE__;
use const Coil\PLUGIN_VERSION;

/**
 * Maybe save the Coil metabox data on content save.
 *
 * @param int $post_id The ID of the post being saved.
 *
 * @return void
 */
function maybe_save_post_metabox( int $post_id ) : void {

	if ( ! current_user_can( 'edit_post', $post_id ) || empty( $_REQUEST['coil_metabox_nonce'] ) ) {
		return;
	}

	// Check the nonce.
	check_admin_referer( 'coil_metabox_nonce_action', 'coil_metabox_nonce' );

	$do_autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
	if ( $do_autosave || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	$post_monetization = sanitize_text_field( $_REQUEST['_coil_monetization_post_status'] ?? '' );

	if ( $post_monetization !== '' ) {
		switch ( $post_monetization ) {
			case 'monetized':
				$post_visibility = sanitize_text_field( $_REQUEST['_coil_visibility_post_status'] ?? get_visibility_default() );
				break;
			default:
				$post_visibility = get_visibility_default();
				break;
		}
		Gating\set_post_status( $post_id, '_coil_monetization_post_status', $post_monetization );
		Gating\set_post_status( $post_id, '_coil_visibility_post_status', $post_visibility );
	} else {
		delete_post_meta( $post_id, '_coil_monetization_post_status' );
		delete_post_meta( $post_id, '_coil_visibility_post_status' );
	}
}

/**
 * Fires after a term has been updated, but before the term cache has been cleaned.
 *
 * @param int    $term_id  Term ID.
 * @param int    $tt_id    Term taxonomy ID.
 * @param string $taxonomy Taxonomy slug.
 *
 * @return void
 */
function maybe_save_term_meta( int $term_id, int $tt_id, $taxonomy ) : void {

	$tax_obj = get_taxonomy( $taxonomy );
	if ( ! current_user_can( $tax_obj->cap->manage_terms ) || empty( $_REQUEST['term_gating_nonce'] ) ) {
		return;
	}

	// Check the nonce.
	check_admin_referer( 'coil_term_gating_nonce_action', 'term_gating_nonce' );

	$term_monetization = sanitize_text_field( $_REQUEST['_coil_monetization_term_status'] ?? '' );

	if ( $term_monetization !== '' ) {
		switch ( $term_monetization ) {
			case 'monetized':
				$term_visibity = sanitize_text_field( $_REQUEST['_coil_visibility_term_status'] ?? get_visibility_default() );
				break;
			default:
				$term_visibity = get_visibility_default();
				break;
		}
		Gating\set_term_status( $term_id, '_coil_monetization_term_status', $term_monetization );
		Gating\set_term_status( $term_id, '_coil_visibility_term_status', $term_visibity );
	} else {
		delete_term_coil_status_meta( $term_id );
	}

}

/**
 * Deletes any term meta when a term is deleted.
 *
 * @param int $term The term id.
 * @return void
 */
function delete_term_coil_status_meta( $term_id ) {

	if ( empty( $term_id ) ) {
		return;
	}
	delete_term_meta( $term_id, '_coil_monetization_term_status' );
	delete_term_meta( $term_id, '_coil_visibility_term_status' );
	// Deleted deprecated term meta
	delete_term_meta( $term_id, '_coil_monetizatize_term_status' );
}

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
		'settings' => '<a href="' . add_query_arg( [ 'page' => 'coil_settings' ], admin_url( 'admin.php' ) ) . '" aria-label="' . esc_attr__( 'Settings for Coil', 'coil-web-monetization' ) . '">' . esc_attr__( 'Settings', 'coil-web-monetization' ) . '</a>',
	];

	return array_merge( $action_links, $links );
}

/**
 * Add extra information to the meta section on the list on the plugins screen.
 *
 * @param string[] $metadata Plugin metadata.
 * @param string   $file     Path to this plugin's main file. Used to identify which row we're in.
 *
 * @return array $metadata Updated array of plugin meta.
 */
function add_plugin_meta_link( array $metadata, string $file ) : array {

	if ( $file !== 'coil-web-monetization/plugin.php' ) {
		return $metadata;
	}

	$row_meta = [
		'community' => '<a href="' . esc_url( 'https://wordpress.org/support/plugin/coil-web-monetization/' ) . '">' . esc_html__( 'Support forum', 'coil-web-monetization' ) . '</a>',
	];

	return array_merge( $metadata, $row_meta );
}

/**
 * Adds admin body class for the Coil settings screen.
 *
 * @param string $classes CSS classes.
 *
 * @return string $classes Updated CSS classes.
 */
function add_admin_body_class( string $classes ) : string {

	$screen = get_current_screen();
	if ( ! $screen ) {
		return $classes;
	}

	if ( $screen->id === 'toplevel_page_coil' ) {
		$classes = ' coil ';
	}

	return $classes;
}

/**
 * Load admin-only CSS/JS.
 *
 * @return void
 */
function load_admin_assets() : void {

	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$load_on_screens = [
		'toplevel_page_coil',
		'toplevel_page_coil_settings',
	];
	if ( ! in_array( $screen->id, $load_on_screens, true ) ) {
		return;
	}

	$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$admin_params = apply_filters(
		'coil_admin_js_params',
		[
			'ajax_url'                    => admin_url( 'admin-ajax.php' ),
			'site_logo_url'               => get_site_logo_src(),
			'coil_logo_url'               => [
				'light' => plugin_dir_url( dirname( __DIR__ ) ) . 'assets/images/coil-icn-black.svg',
				'dark'  => plugin_dir_url( dirname( __DIR__ ) ) . 'assets/images/coil-icn-white.svg',
			],
			'not_monetized_post_types'    => get_post_types_with_status( 'monetization', 'not-monetized' ),
			'exclusive_post_types'        => get_post_types_with_status( 'visibility', 'exclusive' ),
			'general_modal_msg'           => __( 'Removing monetization from {postTypes} will set them as public by default.', 'coil-web-monetization' ),
			'exclusive_modal_msg'         => __( 'Making {postTypes} exclusive will also set them as monetized by default.', 'coil-web-monetization' ),
			'invalid_payment_pointer_msg' => __( 'Please provide a valid payment pointer', 'coil-web-monetization' ),
			'invalid_blank_input_msg'     => __( 'Field cannot be blank', 'coil-web-monetization' ),
			'invalid_url_message'         => __( 'Please provide a valid URL', 'coil-web-monetization' ),
		]
	);

	wp_enqueue_style(
		'coil_admin',
		esc_url_raw( plugin_dir_url( dirname( __DIR__ ) ) . 'assets/css/admin/coil' . $suffix . '.css' ),
		[],
		PLUGIN_VERSION
	);

	wp_register_script(
		'coil_admin_notices',
		esc_url_raw( plugin_dir_url( dirname( __DIR__ ) ) . 'assets/js/admin/admin-notices' . $suffix . '.js' ),
		[ 'jquery' ],
		time(),
		true
	);

	wp_localize_script(
		'coil_admin_notices',
		'coilAdminParams',
		$admin_params
	);

	// Enqueue localized script.
	wp_enqueue_script( 'coil_admin_notices' );
}

/**
 * Adds a Coil Web Monetization section to the Customizer that redirects users from the customizer
 * (where these customization settings were found previously)
 * to the Coil Settings panel.
 *
 * @param \WP_Customize_Manager $wp_customize WordPress Customizer object.
 * @return void
 */
function add_redirect_customizer_section( $wp_customize ) : void {

	// Message and Monetization customization options relocation section.
	$redirect_section = 'coil_customizer_section';

	$wp_customize->add_section(
		$redirect_section,
		[
			'title'      => __( 'Coil Web Monetization', 'coil-web-monetization' ),
			'capability' => apply_filters( 'coil_settings_capability', 'manage_options' ),
		]
	);

	$redirect_setting_id = 'customization-redirect-id';

	$wp_customize->add_setting(
		$redirect_setting_id,
		[
			'capability' => apply_filters( 'coil_settings_capability', 'manage_options' ),
			'default'    => true,
		]
	);

	$description  = '<p>' . sprintf(
		__( 'Message customization and post visibility settings have moved to the ', 'coil-web-monetization' ) . '<a href="%s">' . __( 'Exclusive Content tab', 'coil-web-monetization' ) . '</a>' . '.',
		esc_url( admin_url( 'admin.php?page=coil_settings&tab=exclusive_settings', COIL__FILE__ ) )
	) . '</p>';
	$description .= '<p>' . sprintf(
		__( 'Monetization options have been moved to the ', 'coil-web-monetization' ) . '<a href="%s">' . __( 'General Settings tab', 'coil-web-monetization' ) . '</a>' . '.',
		esc_url( admin_url( 'admin.php?page=coil_settings&tab=general_settings', COIL__FILE__ ) )
	) . '</p>';

	$wp_customize->add_control(
		$redirect_setting_id,
		[
			'type'        => 'hidden',
			'section'     => $redirect_section,
			'label'       => __( 'Customization options have all been moved to the Coil settings panel.', 'coil-web-monetization' ),
			'description' => $description,
		]
	);
}

/**
 * Gets the taxonomies and allows the output to be filtered.
 *
 * @return array Taxonomies or empty array
 */
function get_valid_taxonomies() : array {

	$all_taxonomies = get_taxonomies(
		[],
		'objects'
	);

	// Set up options to exclude certain taxonomies.
	$taxonomies_exclude = [
		'nav_menu',
		'link_category',
		'post_format',
	];

	$taxonomies_exclude = apply_filters( 'coil_settings_taxonomy_exclude', $taxonomies_exclude );

	// Store the available taxonomies using the above exclusion options.
	$taxonomy_options = [];
	foreach ( $all_taxonomies as $taxonomy ) {

		if ( ! empty( $taxonomies_exclude ) && in_array( $taxonomy->name, $taxonomies_exclude, true ) ) {
			continue;
		}
		if ( ! in_array( $taxonomy->name, $taxonomy_options, true ) ) {
			$taxonomy_options[] = $taxonomy->name;
		}
	}

	return $taxonomy_options;
}

/**
 * Get whatever settings are stored in the plugin as the default
 * content monetization settings (post, page, cpt etc).
 *
 * @return array Setting stored in options, or blank array.
 */
function get_general_settings() : array {

	$general_settings = get_option( 'coil_general_settings_group', [] );
	if ( ! empty( $general_settings ) ) {
		return $general_settings;
	}

	return [];
}

/**
 * Retrieve the payment pointer using a key from the general
 * settings group (serialized).
 *
 * @return string
 */
function get_payment_pointer_setting() {

	$settings = get_general_settings();

	return isset( $settings['coil_payment_pointer'] ) ? $settings['coil_payment_pointer'] : '';
}

/**
 * @return array Valid monetization states - Monetized or Not Monetized.
 */
function get_monetization_types() {

	$monetization_types = [
		'monetized'     => 'Monetized',
		'not-monetized' => 'Not Monetized',
	];

	return $monetization_types;
}

/**
 * Returns the single default 'monetized'.
 *
 * @return string
 */
function get_monetization_default() {
	return 'monetized';
}

/**
 * Retrieve the Exclusive Content settings.
 * @return array Setting stored in options.
 */
function get_exclusive_settings(): array {

	// Set up defaults.
	$defaults = [ 'coil_content_container' => '.content-area .entry-content' ];

	$exclusive_options = get_option( 'coil_exclusive_settings_group', [] );
	if ( empty( $exclusive_options ) || ! isset( $exclusive_options['coil_content_container'] ) ) {
		$exclusive_options['coil_content_container'] = $defaults['coil_content_container'];
	}

	return $exclusive_options;
}

/**
 * Retrieve the exclusive content toggle setting
 *
 * @return bool setting stored in options
 */
function is_exclusive_content_enabled() {
	$exclusive_options   = get_exclusive_settings();
	$exclusive_toggle_id = 'coil_exclusive_toggle';
	return isset( $exclusive_options[ $exclusive_toggle_id ] ) ? $exclusive_options[ $exclusive_toggle_id ] : get_exclusive_content_enabled_default();
}

/**
 * Provides the exclusive content toggle setting default
 *
 * @return bool The default is true
 */
function get_exclusive_content_enabled_default() {
	return true;
}

/**
 * Retrieve the paywall text field defaults
 * This includes the title, message, button text and button link
 *
 * @return array Text field default values
 */
function get_paywall_text_defaults() {

	// Set up defaults.
	return [
		'coil_paywall_title'       => __( 'Keep Reading with Coil', 'coil-web-monetization' ),
		'coil_paywall_message'     => __( 'We partnered with Coil to offer exclusive content. Access this and other great content with a Coil membership.', 'coil-web-monetization' ),
		'coil_paywall_button_text' => __( 'Become a Coil Member', 'coil-web-monetization' ),
		'coil_paywall_button_link' => __( 'https://coil.com/', 'coil-web-monetization' ),
	];
}

/**
 * Retrieve the paywall text fields.
 * If a value has been set then return it, otherwise return the default.
 * This includes the title, message, button text and button link
 *
 * @return string Text field value
 */
function get_paywall_text_settings_or_default( $field_id ) {
	$text_fields = [ 'coil_paywall_title', 'coil_paywall_message', 'coil_paywall_button_text', 'coil_paywall_button_link' ];
	if ( in_array( $field_id, $text_fields, true ) ) {
		$value = get_paywall_appearance_setting( $field_id ) === '' ? get_paywall_appearance_setting( $field_id, true ) : get_paywall_appearance_setting( $field_id );
		return $value;
	}
	return '';
}

/**
 * Retrieve the paywall appearance settings
 * using a key from coil_exclusive_settings_group (serialized).
 *
 * @param string $field_id The named key in the wp_options serialized array.
 * @param bool $default set to either return the text field default if the text field item is empty or to return an empty string.
 * @return string
 */
function get_paywall_appearance_setting( $field_id, $use_text_default = false ) {

	$exclusive_options = get_exclusive_settings();

	$text_fields    = [ 'coil_paywall_title', 'coil_paywall_message', 'coil_paywall_button_text', 'coil_paywall_button_link' ];
	$paywall_styles = [ 'coil_message_color_theme', 'coil_message_branding', 'coil_message_font' ];

	// Text inputs can be empty strings, in which the placeholder text will display or the default text will be returned.
	if ( in_array( $field_id, $text_fields, true ) ) {

		// The default is returned as a placeholder or as a coil_js_ui_messages field when no custom input has been provided
		if ( $use_text_default && empty( $exclusive_options[ $field_id ] ) ) {
			$text_defaults = get_paywall_text_defaults();
			return $text_defaults[ $field_id ];
		} else {
			return ( ! empty( $exclusive_options[ $field_id ] ) ) ? $exclusive_options[ $field_id ] : '';
		}
	} elseif ( in_array( $field_id, $paywall_styles, true ) ) {
		if ( isset( $exclusive_options[ $field_id ] ) ) {
			$setting_value = $exclusive_options[ $field_id ];
		} else {
			$style_defaults = get_paywall_appearance_defaults();
			$setting_value  = $style_defaults[ $field_id ];
		}
		return $setting_value;

	}
	return null;
}

/**
 * @return array Valid colour theme states
 */
function get_theme_color_types() {

	$theme_colors = [ 'light', 'dark' ];

	return $theme_colors;
}

/**
 * @return array Valid branding options.
 */
function get_paywall_branding_options() {

	$branding_choices = [ 'site_logo', 'coil_logo', 'no_logo' ];

	return $branding_choices;
}

/**
 * @return string Extracts the src attribute of the website logo, if there is one.
 */
function get_site_logo_src() {
	$site_logo_src = false;
	if ( function_exists( 'get_custom_logo' ) ) {
		$site_logo   = get_custom_logo();
		$pattern     = '/(src=")[^"]*(")/i';
		$matches     = [];
		$match_found = preg_match( $pattern, $site_logo, $matches );
		if ( $match_found ) {
			$site_logo_src = str_replace( 'src="', '', $matches[0] );
			$site_logo_src = str_replace( '"', '', $site_logo_src );
		}
	}
	return $site_logo_src;
}


/**
 * @return array Valid padlock positions.
 */
function get_padlock_title_icon_position_options() {

	$positions = [ 'before', 'after' ];

	return $positions;
}

/**
 * @return array Valid padlock icon styles.
 */
function get_padlock_title_icon_style_options() {

	$icon_styles = [ 'lock', 'coil_icon', 'bonus', 'exclusive' ];

	return $icon_styles;
}
/**
 * Returns the paywall appearance settings for all fields that are not text based.
 * This includes the color theme, branding choice, and font style.
 *
 * @return array
 */
function get_paywall_appearance_defaults(): array {
	$paywall_appearance_defaults = [
		'coil_message_color_theme' => 'light',
		'coil_message_branding'    => 'coil_logo',
		'coil_message_font'        => false,
	];

	return $paywall_appearance_defaults;
}

/**
 * Retrieve the exclusive post appearance settings
 * using a key from coil_exclusive_settings_group (serialized).
 *
 * @param string $field_id The named key in the wp_options serialized array.
 * @return string
 */
function get_exlusive_post_setting( $field_id ) {

	$padloack_settings = [ 'coil_padlock_icon_position', 'coil_padlock_icon_style' ];

	$exclusive_options  = get_exclusive_settings();
	$exclusive_defaults = get_exclusive_post_defaults();

	if ( $field_id === 'coil_title_padlock' ) {
		$setting_value = isset( $exclusive_options[ $field_id ] ) ? $exclusive_options[ $field_id ] : false;
		return $setting_value;
	} elseif ( in_array( $field_id, $padloack_settings, true ) ) {
		$setting_value = isset( $exclusive_options[ $field_id ] ) ? $exclusive_options[ $field_id ] : $exclusive_defaults[ $field_id ];
		return $setting_value;
	}
	return false;
}

/**
 * Returns the padlock default as true.
 *
 * @return array
 */
function get_exclusive_post_defaults(): array {
	$exclusive_post_defaults = [
		'coil_title_padlock'         => true,
		'coil_padlock_icon_position' => 'before',
		'coil_padlock_icon_style'    => 'lock',
	];

	return $exclusive_post_defaults;
}

/**
 * @return array Valid visibility states - Public or Exclusive.
 */
function get_visibility_types() : array {

	$visibility_types = [
		'public'    => 'Keep public',
		'exclusive' => 'Make exclusive',
	];

	return $visibility_types;
}

/**
 * Returns the visibility default which is 'public' for all post types.
 *
 * @return string
 */
function get_visibility_default() {

	return 'public';
}

/**
* Returns the default excerpt display for all post types. The default is to not display.
*
* @return boolean
*/
function get_excerpt_display_default() {

	return false;
}

/**
 * Retrieve the CSS selector setting settings.
 * @return string Setting stored in options.
 */
function get_css_selector() {

	$exclusive_options = get_exclusive_settings();
	if ( empty( $exclusive_options ['coil_content_container'] ) ) {
		return '.content-area .entry-content';
	} else {
		return $exclusive_options ['coil_content_container'];
	}
}

/**
 * Retrieve the Coil button settings.
 * @return array Setting stored in options.
 */
function get_coil_button_settings() : array {

	$coil_button_settings = get_option( 'coil_button_settings_group', [] );
	return $coil_button_settings;
}

/**
 * Retrieve the Coil button toggle setting
 *
 * @return bool setting stored in options
 */
function is_coil_button_enabled() {
	$coil_button_options = get_coil_button_settings();
	$exclusive_toggle_id = 'coil_button_toggle';
	return isset( $coil_button_options[ $exclusive_toggle_id ] ) ? $coil_button_options[ $exclusive_toggle_id ] : false;
}

/**
 * Retrieve the Coil button settings.
 * Note: This does not return the final button margins since they need to be calculated relative the baseline using the get_coil_button_margins function.
 * @param string $field_name
 * @return string Setting stored in options.
 */
function get_coil_button_setting( $field_id, $use_text_default = false ) {

	$coil_button_settings = get_coil_button_settings();
	$value                = false;
	$text_fields          = [ 'coil_button_text', 'coil_button_link', 'coil_members_button_text' ];
	$margin_keys          = array_keys( get_button_margin_key_defaults() );
	$default_settings     = get_coil_button_defaults();
	// Text inputs can be empty strings, in which the placeholder text will display or the default text will be returned.
	if ( in_array( $field_id, $text_fields, true ) ) {
		if ( $use_text_default && empty( $coil_button_settings[ $field_id ] ) ) {
			$value = $default_settings[ $field_id ];
		} else {
			$value = ( ! empty( $coil_button_settings[ $field_id ] ) ) ? $coil_button_settings[ $field_id ] : '';
		}
	} elseif ( in_array( $field_id, $margin_keys, true ) ) {
		if ( ! empty( $coil_button_settings[ $field_id ] ) ) {
			$filtered_int = filter_var( $coil_button_settings[ $field_id ], FILTER_SANITIZE_NUMBER_INT );
			$value        = ( $filtered_int !== false ) ? $filtered_int : '';
		} else {
			$value = '';
		}
	} elseif ( in_array( $field_id, array_keys( $default_settings ), true ) ) {
		$value = isset( $coil_button_settings[ $field_id ] ) ? $coil_button_settings[ $field_id ] : $default_settings[ $field_id ];
	}

	return $value;
}

/**
 * Calculates the final Coil button margin values.
 * The button has baseline margin values that can be added to or subtracted from in the Coil buton settings tab.
 * @param string $field_name
 * @return string Final button margin value.
 */
function get_coil_button_margins( $field_id ) {
	$value                   = get_coil_button_setting( $field_id );
	$margin_keys             = array_keys( get_button_margin_key_defaults() );
	$magin_baseline_settings = get_coil_button_defaults();
	if ( in_array( $field_id, $margin_keys, true ) ) {
		if ( empty( $value ) ) {
			$value = $magin_baseline_settings[ $field_id ];
		} else {
			$value = strval( intval( $value ) + intval( $magin_baseline_settings[ $field_id ] ) );
		}
	}

	return $value;
}

/**
 * Return the Coil button visibility status
 * based on the global defaults.
 *
 * @param integer $post_id
 * @return string Coil button status.
 */
function get_coil_button_status( $object_id ) {
	$coil_button_class    = '';
	$post_id              = (int) $object_id;
	$post                 = get_post( $post_id );
	$coil_button_settings = get_coil_button_settings();

	if ( ! empty( $coil_button_settings ) && ! empty( $post ) && isset( $coil_button_settings[ $post->post_type . '_button_visibility' ] ) ) {
		$status = $coil_button_settings[ $post->post_type . '_button_visibility' ];
		if ( $status === 'show' ) {
			$coil_button_class = 'show-coil-button';
		}
	}

	return $coil_button_class;
}

/**
 * Retrieve the Coil button settings defaults
 *
 * @return array Default values
 */
function get_coil_button_defaults() {
	// Set up defaults.
	$settings        = [
		'coil_button_toggle'          => true,
		'coil_button_member_display'  => true,
		'coil_button_text'            => __( 'Support us with Coil', 'coil-web-monetization' ),
		'coil_button_link'            => __( 'https://coil.com/', 'coil-web-monetization' ),
		'coil_members_button_text'    => __( 'Thanks for your support!', 'coil-web-monetization' ),
		'coil_button_color_theme'     => 'dark',
		'coil_button_size'            => 'large',
		'coil_button_position'        => 'bottom-right',
		'post_type_button_visibility' => 'show', // a generic default for all post-types
	];
	$margin_defaults = get_button_margin_key_defaults();
	return array_merge( $settings, $margin_defaults );
}

/**
 * @return array Default margins for the Coil button.
 */
function get_button_margin_key_defaults() {
	$horizontal_margin_baseline = '32';
	$vertical_margin_baseline   = '0';
	return [
		'coil_button_top_margin'    => $vertical_margin_baseline,
		'coil_button_right_margin'  => $horizontal_margin_baseline,
		'coil_button_bottom_margin' => $vertical_margin_baseline,
		'coil_button_left_margin'   => $horizontal_margin_baseline,
	];
}

/**
 * @return array Valid button sizes.
 */
function get_button_size_options() {

	$sizes = [ 'large', 'small' ];

	return $sizes;
}

/**
 * @return array Valid button positions.
 */
function get_button_position_options() {

	$position_options = [
		'bottom-right' => 'Bottom - Right',
		'bottom-left'  => 'Bottom - Left',
		'top-right'    => 'Top - Right',
		'top-left'     => 'Top - Left',
	];

	return $position_options;
}

/**
 * Create an array of the supported post types names.
 *
 * @return array
 */
function get_post_type_names() {
	$post_types = Coil\get_supported_post_types( 'objects' );
	$type_names = [];
	foreach ( $post_types as $post_type ) {
		array_push( $type_names, $post_type->name );
	}
	return $type_names;
}

/**
 * Create an array that names all post types with a particular default status.
 *
 * @param string $status_type Either monetization or visibility status type
 * @param string $status The Coil status being filtered for. E.g. 'exclusive'
 * @return array
 */
function get_post_types_with_status( $status_type, $status ) {
	$settings = [];
	if ( $status_type === 'monetization' ) {
		$settings = get_general_settings();
	} elseif ( $status_type === 'visibility' ) {
		$settings = get_exclusive_settings();
	}

	$possible_post_types   = get_post_type_names();
	$applicable_post_types = [];
	foreach ( $settings as $key => $value ) {
		$id = str_replace( '_' . $status_type, '', $key );
		if ( in_array( $id, $possible_post_types, true ) && $value === $status ) {
			array_push( $applicable_post_types, $id );
		}
	}
	return $applicable_post_types;
}
/**
 * Returns an array of the padlock icon styles, which we use across the options panel
 * @return void
*/
function get_padlock_icon_styles() {
	$icon_styles = [
		'lock'      => '<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M0 24.68H24V0.68H0V24.68Z" fill="url(#pattern0)"/><defs><pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1"><use xlink:href="#image0_1587_1290" transform="scale(0.015625)"/></pattern><image id="image0_1587_1290" width="64" height="64" xlink:href="data:image/png;base64,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"/></defs></svg>',
		'coil_icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 5C0 2.23858 2.23858 0 5 0H19C21.7614 0 24 2.23858 24 5V19C24 21.7614 21.7614 24 19 24H5C2.23858 24 0 21.7614 0 19V5Z" fill="#0B1621"/><path d="M15.7587 14.8242C16.0793 14.8242 16.4975 14.9868 16.7903 15.6781C16.8321 15.773 16.86 15.8815 16.86 15.9763C16.86 17.1149 14.1973 18.1451 12.6498 18.2264C12.5383 18.2264 12.4128 18.24 12.3013 18.24C10.0011 18.24 7.86809 17.0472 6.76675 15.1224C6.25094 14.2142 6 13.2112 6 12.1946C6 11.0153 6.34852 9.83601 7.03163 8.8194C7.56139 8.00611 8.59302 6.93528 10.4053 6.35243C10.8654 6.20332 11.674 6 12.5941 6C13.4863 6 14.4761 6.18977 15.3404 6.82684C16.5533 7.70791 16.8042 8.6703 16.8042 9.29382C16.8042 9.56492 16.7624 9.76824 16.7206 9.89023C16.4418 10.8797 15.5077 11.6388 14.4203 11.7879C14.1694 11.815 13.9603 11.8421 13.7651 11.8421C12.7753 11.8421 12.4407 11.4355 12.4407 10.9746C12.4407 10.3511 13.0402 9.61914 13.4723 9.61914C13.5281 9.61914 13.5839 9.63269 13.6257 9.6598C13.7372 9.72757 13.8766 9.75468 14.0021 9.75468C14.0439 9.75468 14.0718 9.75468 14.1136 9.74113C14.4622 9.70046 14.6434 9.47003 14.6434 9.18538C14.6434 8.65674 14.0021 7.93834 12.608 7.93834C12.1619 7.93834 11.6461 8.00611 11.0606 8.18232C9.83376 8.54831 9.15065 9.40226 8.80213 9.9309C8.34208 10.6086 8.11902 11.3948 8.11902 12.1674C8.11902 12.8316 8.28632 13.4958 8.6209 14.0922C9.34583 15.3528 10.7678 16.139 12.3013 16.139C12.385 16.139 12.4547 16.139 12.5383 16.139C14.5319 16.0306 14.9919 15.0546 15.438 14.8648C15.5077 14.8513 15.6332 14.8242 15.7587 14.8242Z" fill="white"/></svg>',
		'bonus'     => '<svg width="44" height="16" viewBox="0 0 44 16" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="0.5" y="0.5" width="43" height="15" rx="2.5" fill="black"/><path d="M8.19102 12C9.69492 12 10.5982 11.2432 10.5982 10.0029C10.5982 9.08496 9.96348 8.40625 9.01133 8.30859V8.22559C9.70469 8.1084 10.2467 7.44922 10.2467 6.71191C10.2467 5.62793 9.45078 4.9541 8.12754 4.9541H5.29551V12H8.19102ZM6.38926 5.88184H7.87363C8.68418 5.88184 9.15781 6.2627 9.15781 6.91211C9.15781 7.58105 8.65488 7.9375 7.68809 7.9375H6.38926V5.88184ZM6.38926 11.0723V8.80176H7.90781C8.93809 8.80176 9.48008 9.1875 9.48008 9.9248C9.48008 10.6719 8.95762 11.0723 7.97129 11.0723H6.38926ZM14.8074 4.7832C12.7859 4.7832 11.5164 6.19922 11.5164 8.47461C11.5164 10.7451 12.7566 12.1709 14.8074 12.1709C16.8436 12.1709 18.0936 10.7402 18.0936 8.47461C18.0936 6.2041 16.8338 4.7832 14.8074 4.7832ZM14.8074 5.80371C16.1404 5.80371 16.9754 6.83887 16.9754 8.47461C16.9754 10.1006 16.1453 11.1504 14.8074 11.1504C13.45 11.1504 12.6346 10.1006 12.6346 8.47461C12.6346 6.83887 13.4744 5.80371 14.8074 5.80371ZM20.4424 12V6.99512H20.5205L24.1387 12H25.125V4.9541H24.0605V9.95898H23.9824L20.3643 4.9541H19.3779V12H20.4424ZM27.7814 4.9541H26.6877V9.57324C26.6877 11.1016 27.7766 12.1709 29.5393 12.1709C31.3117 12.1709 32.3957 11.1016 32.3957 9.57324V4.9541H31.302V9.47559C31.302 10.4619 30.6623 11.1455 29.5393 11.1455C28.4211 11.1455 27.7814 10.4619 27.7814 9.47559V4.9541ZM33.6264 10.1055C33.7045 11.3652 34.7543 12.1709 36.3168 12.1709C37.9867 12.1709 39.0316 11.3262 39.0316 9.97852C39.0316 8.91895 38.4359 8.33301 36.9906 7.99609L36.2143 7.80566C35.2963 7.59082 34.9252 7.30273 34.9252 6.7998C34.9252 6.16504 35.5014 5.75 36.3656 5.75C37.1859 5.75 37.7523 6.15527 37.8549 6.80469H38.9193C38.8559 5.61816 37.8109 4.7832 36.3803 4.7832C34.8422 4.7832 33.8168 5.61816 33.8168 6.87305C33.8168 7.9082 34.3979 8.52344 35.6723 8.82129L36.5805 9.04102C37.5131 9.26074 37.9232 9.58301 37.9232 10.1201C37.9232 10.7451 37.2787 11.1992 36.3998 11.1992C35.4574 11.1992 34.8031 10.7744 34.7104 10.1055H33.6264Z" fill="white"/><rect x="0.5" y="0.5" width="43" height="15" rx="2.5" stroke="#383838"/></svg>',
		'exclusive' => '<svg width="62" height="16" viewBox="0 0 62 16" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="0.5" y="0.5" width="61" height="15" rx="2.5" fill="black"/><path d="M9.45625 11.0039H6.07734V8.88965H9.27559V7.94238H6.07734V5.9502H9.45625V4.9541H4.98359V12H9.45625V11.0039ZM10.3891 12H11.6L13.3529 9.39258H13.4359L15.1645 12H16.4437L14.0951 8.48438L16.4877 4.9541H15.2572L13.5141 7.61035H13.4311L11.7172 4.9541H10.4135L12.7426 8.45508L10.3891 12ZM20.2379 12.1709C21.8102 12.1709 22.9674 11.2432 23.1627 9.85156H22.0738C21.8785 10.6475 21.1705 11.1504 20.2379 11.1504C18.9684 11.1504 18.1773 10.1201 18.1773 8.47949C18.1773 6.83398 18.9684 5.80371 20.233 5.80371C21.1607 5.80371 21.8687 6.36035 22.0738 7.22461H23.1627C22.9869 5.78906 21.7857 4.7832 20.233 4.7832C18.275 4.7832 17.0592 6.19434 17.0592 8.47949C17.0592 10.7598 18.2799 12.1709 20.2379 12.1709ZM28.7977 10.9941H25.4969V4.9541H24.4031V12H28.7977V10.9941ZM30.8633 4.9541H29.7695V9.57324C29.7695 11.1016 30.8584 12.1709 32.6211 12.1709C34.3936 12.1709 35.4775 11.1016 35.4775 9.57324V4.9541H34.3838V9.47559C34.3838 10.4619 33.7441 11.1455 32.6211 11.1455C31.5029 11.1455 30.8633 10.4619 30.8633 9.47559V4.9541ZM36.7082 10.1055C36.7863 11.3652 37.8361 12.1709 39.3986 12.1709C41.0686 12.1709 42.1135 11.3262 42.1135 9.97852C42.1135 8.91895 41.5178 8.33301 40.0725 7.99609L39.2961 7.80566C38.3781 7.59082 38.007 7.30273 38.007 6.7998C38.007 6.16504 38.5832 5.75 39.4475 5.75C40.2678 5.75 40.8342 6.15527 40.9367 6.80469H42.0012C41.9377 5.61816 40.8928 4.7832 39.4621 4.7832C37.924 4.7832 36.8986 5.61816 36.8986 6.87305C36.8986 7.9082 37.4797 8.52344 38.7541 8.82129L39.6623 9.04102C40.5949 9.26074 41.0051 9.58301 41.0051 10.1201C41.0051 10.7451 40.3605 11.1992 39.4816 11.1992C38.5393 11.1992 37.885 10.7744 37.7922 10.1055H36.7082ZM44.4477 12V4.9541H43.3539V12H44.4477ZM49.1939 12L51.6988 4.9541H50.5367L48.6471 10.6328H48.5641L46.6646 4.9541H45.4732L47.9928 12H49.1939ZM57.202 11.0039H53.823V8.88965H57.0213V7.94238H53.823V5.9502H57.202V4.9541H52.7293V12H57.202V11.0039Z" fill="white"/><rect x="0.5" y="0.5" width="61" height="15" rx="2.5" stroke="#383838"/></svg>',
	];

	return $icon_styles;
}
