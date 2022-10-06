<?php
declare(strict_types=1);
/**
 * Coil settings.
 * Creates and renders the Coil settings panel
 */

namespace Coil\Settings;

use Coil;
use Coil\Admin;
use Coil\Gating;
use Coil\Rendering;
use const Coil\COIL__FILE__;

/* ------------------------------------------------------------------------ *
 * Menu Registration
 * ------------------------------------------------------------------------ */

/**
 * Add Coil settings to the admin navigation menu.
 *
 * @return void
 */
function register_admin_menu() : void {

	add_menu_page(
		esc_html__( 'Coil', 'coil-web-monetization' ),
		esc_html( _x( 'Coil', 'admin menu name', 'coil-web-monetization' ) ),
		apply_filters( 'coil_settings_capability', 'manage_options' ),
		'coil_settings',
		__NAMESPACE__ . '\render_coil_settings_screen',
		'data:image/svg+xml;base64,' . base64_encode( '<svg height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m10 18c4.4183 0 8-3.5817 8-8 0-4.41828-3.5817-8-8-8-4.41828 0-8 3.58172-8 8 0 4.4183 3.58172 8 8 8zm3.1274-5.5636c-.1986-.4734-.4822-.5848-.6997-.5848-.0778 0-.1556.0156-.2045.0253-.0046.0009-.0089.0018-.0129.0026-.0785.0337-.1576.1036-.2553.19-.2791.2466-.7099.6274-1.7113.6824h-.1607c-1.03998 0-2.00434-.5383-2.49598-1.4014-.22691-.4084-.34036-.8632-.34036-1.318 0-.529.15127-1.06731.46327-1.53137.23636-.36197.69963-.94669 1.53163-1.19728.39709-.12066.74691-.16706 1.04944-.16706.9455 0 1.3804.4919 1.3804.85387 0 .1949-.1229.35268-.3593.38053-.0284.00928-.0473.00928-.0756.00928-.0851 0-.1797-.01856-.2553-.06497-.0284-.01856-.0662-.02784-.104-.02784-.2931 0-.6996.50118-.6996.92812 0 .31556.2269.594.8981.594.1121 0 .2309-.0133.3679-.02864.0249-.00279.0504-.00564.0765-.00849.7375-.10209 1.3709-.62184 1.56-1.29937.0284-.08353.0567-.22275.0567-.40837 0-.42694-.1702-1.08591-.9927-1.68919-.5862-.43621-1.2575-.56615-1.8625-.56615-.62404 0-1.1724.13922-1.4844.24131-1.22909.39909-1.92872 1.13231-2.288 1.68919-.46327.69609-.69963 1.50355-.69963 2.31103 0 .6961.17018 1.3829.52 2.0047.74691 1.318 2.19345 2.1347 3.75343 2.1347.0378 0 .078-.0023.1182-.0046s.0804-.0047.1182-.0047c1.0494-.0557 2.8552-.761 2.8552-1.5406 0-.065-.0189-.1393-.0472-.2042z" fill-rule="evenodd" fill="black"/></svg>' )
	);
}

/* ------------------------------------------------------------------------ *
 * Setting Registration
 * ------------------------------------------------------------------------ */

/**
 * Initialize the theme options page by registering the Sections,
 * Fields, and Settings.
 *
 * @return void
 */
function register_admin_content_settings() {

	// Tab 1 - Welcome
	register_setting(
		'coil_welcome_settings_group',
		'coil_welcome_settings_group',
		false
	);

	// ==== Welcome Note, Payment Pointer and Guide
	add_settings_section(
		'coil_welcome_section',
		false,
		__NAMESPACE__ . '\coil_settings_welcome_render_callback',
		'coil_welcome_section'
	);

	// Tab 2 - General Settings
	register_setting(
		'coil_general_settings_group',
		'coil_general_settings_group',
		__NAMESPACE__ . '\coil_general_settings_group_validation'
	);

	// ==== Payment Pointer
	add_settings_section(
		'coil_payment_pointer_section',
		false,
		__NAMESPACE__ . '\coil_settings_payment_pointer_render_callback',
		'coil_payment_pointer_section'
	);

	// ==== Global Monetization Defaults
	add_settings_section(
		'coil_monetization_section',
		false,
		__NAMESPACE__ . '\coil_settings_monetization_render_callback',
		'coil_monetization_section'
	);

	// Tab 3 - Exclusive Content
	register_setting(
		'coil_exclusive_settings_group',
		'coil_exclusive_settings_group',
		__NAMESPACE__ . '\coil_exclusive_settings_group_validation'
	);

	// ==== Enable / Disable
	add_settings_section(
		'coil_enable_exclusive_section',
		false,
		__NAMESPACE__ . '\coil_settings_enable_exclusive_toggle_render_callback',
		'coil_enable_exclusive_section'
	);

	// ==== CSS Selectors
	add_settings_section(
		'coil_css_selector_section',
		false,
		__NAMESPACE__ . '\coil_settings_css_selector_render_callback',
		'coil_css_selector_section'
	);

	// ==== Global Visibility Defaults
	add_settings_section(
		'coil_post_visibility_section',
		false,
		__NAMESPACE__ . '\coil_settings_post_visibility_render_callback',
		'coil_post_visibility_section'
	);

	// ==== Excerpt Visibility Defaults
	add_settings_section(
		'coil_excerpt_display_section',
		false,
		__NAMESPACE__ . '\coil_settings_excerpt_display_render_callback',
		'coil_excerpt_display_section'
	);

	// ==== Paywall Appearance
	add_settings_section(
		'coil_paywall_settings',
		false,
		__NAMESPACE__ . '\coil_settings_paywall_render_callback',
		'coil_paywall_section'
	);

	// ==== Exclusive Icon Appearance
	add_settings_section(
		'coil_exclusive_icon_section',
		false,
		__NAMESPACE__ . '\coil_settings_exclusive_icon_render_callback',
		'coil_exclusive_icon_section'
	);

	// Tab 4 - Streaming Support Widget
	register_setting(
		'streaming_widget_settings_group',
		'streaming_widget_settings_group',
		__NAMESPACE__ . '\streaming_widget_settings_group_validation'
	);

	// ==== Enable / Disable
	add_settings_section(
		'coil_enable_streaming_widget_section',
		false,
		__NAMESPACE__ . '\coil_settings_enable_streaming_widget_toggle_render_callback',
		'coil_enable_streaming_widget_section'
	);

	// ==== Streaming Support Widget Visibility
	add_settings_section(
		'streaming_widget_visibility_section',
		false,
		__NAMESPACE__ . '\coil_settings_streaming_widget_visibility_render_callback',
		'streaming_widget_visibility_section'
	);

	// ==== Streaming Support Widget Settings
	add_settings_section(
		'streaming_widget_settings_section',
		false,
		__NAMESPACE__ . '\coil_settings_streaming_widget_settings_render_callback',
		'streaming_widget_settings_section'
	);
}

/* ------------------------------------------------------------------------ *
 * Section Validation
 * ------------------------------------------------------------------------ */

/**
 * Validates the payment pointer and
 * the radio button options, that set the global monetization defaults,
 * to be properly validated
 *
 * @param array $general_settings The posted radio options from the General Settings section
 * @return array
 */
function coil_general_settings_group_validation( $general_settings ) : array {

	if ( ! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ) {
		return [];
	}

	$final_settings        = [];
	$general_settings_keys = array_keys( $general_settings );

	$post_monetization_default = Admin\get_monetization_default();
	// A list of valid monetization types (monetized or not-monetized)
	$valid_options = array_keys( Admin\get_monetization_types() );
	// Retrieves the exclusive settings to get the post type visibility defaults
	$exclusive_settings = Admin\get_exclusive_settings();

	// Validate the payment pointer
	if ( in_array( 'coil_payment_pointer', $general_settings_keys, true ) ) {
		$general_settings['coil_payment_pointer'] = sanitize_text_field( $general_settings['coil_payment_pointer'] );
		// check if url starts with https://, or $
		$correct_format = preg_match( '/^(https:\/\/.)|^[\$]./', $general_settings['coil_payment_pointer'] );
		// If the payment pointer is invalid it won't be saved.
		if ( $correct_format === 1 ) {
			$final_settings['coil_payment_pointer'] = $general_settings['coil_payment_pointer'];
		}
	}

	// Validate the monetization defaults
	$post_type_options = Coil\get_supported_post_types( 'objects' );
	foreach ( $post_type_options as $post_type ) {
		// Sets the keys for the post visibility and post monetization settings
		$monetization_setting_key = $post_type->name . '_monetization';
		$visibility_setting_key   = $post_type->name . '_visibility';

		// The default value is monetized
		$final_settings[ $monetization_setting_key ] = isset( $general_settings[ $monetization_setting_key ] ) && in_array( $general_settings[ $monetization_setting_key ], $valid_options, true ) ? sanitize_key( $general_settings[ $monetization_setting_key ] ) : $post_monetization_default;

		// Ensures that a post cannot default to be Not Monetized and Exclusive
		if ( $final_settings[ $monetization_setting_key ] === 'not-monetized' && isset( $exclusive_settings[ $visibility_setting_key ] ) && $exclusive_settings[ $visibility_setting_key ] === 'exclusive' ) {
			$exclusive_settings [ $visibility_setting_key ] = Admin\get_visibility_default();
			update_option( 'coil_exclusive_settings_group', $exclusive_settings );
		}
	}

	return $final_settings;
}

/**
 * Validates the post type default visibility settings.
 * Validates text inputs (the paywall title, message, button text and link and the CSS selector).
 * Validates paywall appearance settings.
 * Validates exclusive icon appearance settings
 * Validates the excerpt visibility setings per post type.
 *
 * @param array $exclusive_settings The posted text input fields.
 * @return array
 */
function coil_exclusive_settings_group_validation( $exclusive_settings ) : array {

	if ( ! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ) {
		return [];
	}

	$paywall_defaults        = Admin\get_paywall_appearance_defaults();
	$exclusive_post_defaults = Admin\get_exclusive_icon_defaults();
	$final_settings          = [];

	// Posts default to being publicly visible
	$post_visibility_default = Admin\get_visibility_default();
	// Monetization defaults are needed to check that the 'exclusive' and 'not-monetized' defaults are never set globally on one post type
	$post_monetization_settings = Admin\get_general_settings();
	// Valid visibility options are public or exclusive
	$valid_options = array_keys( Admin\get_visibility_types() );
	// A list of valid post types
	$post_type_options = Coil\get_supported_post_types( 'objects' );

	// Loops through each post type to validate post visibility defaults and excerpt display settings
	foreach ( $post_type_options as $post_type ) {
		// Validates default post visibility settings
		// Sets the keys for the post visibility and post monetization settings
		$monetization_setting_key = $post_type->name . '_monetization';
		$visibility_setting_key   = $post_type->name . '_visibility';

		// The default value is public
		$final_settings[ $visibility_setting_key ] = isset( $exclusive_settings[ $visibility_setting_key ] ) && in_array( $exclusive_settings[ $visibility_setting_key ], $valid_options, true ) ? sanitize_key( $exclusive_settings[ $visibility_setting_key ] ) : $post_visibility_default;

		// Ensures that a post cannot default to be Not Monetized and Exclusive
		if ( $final_settings[ $visibility_setting_key ] === 'exclusive' && isset( $post_monetization_settings[ $monetization_setting_key ] ) && $post_monetization_settings[ $monetization_setting_key ] === 'not-monetized' ) {
			$post_monetization_settings [ $monetization_setting_key ] = 'monetized';
			update_option( 'coil_general_settings_group', $post_monetization_settings );
		}

		// Validates excerpt display settings
		$excerpt_setting_key                    = $post_type->name . '_excerpt';
		$final_settings[ $excerpt_setting_key ] = isset( $exclusive_settings[ $excerpt_setting_key ] ) && ( $exclusive_settings[ $excerpt_setting_key ] === 'on' || $exclusive_settings[ $excerpt_setting_key ] === true ) ? true : false;
	}

	// Validates all text input fields
	$text_fields = [
		'coil_content_container',
		'coil_paywall_title',
		'coil_paywall_message',
		'coil_paywall_button_text',
		'coil_paywall_button_link',
	];

	foreach ( $text_fields as $field_name ) {
		if ( $field_name === 'coil_paywall_button_link' ) {
			// Check the URL has something dot something to be valid. Only save it if valid.
			if ( isset( $exclusive_settings[ $field_name ] ) && preg_match( '/.+\..+/', $exclusive_settings[ $field_name ] ) === 1 ) {
				$final_settings[ $field_name ] = esc_url_raw( $exclusive_settings[ $field_name ] );
			} else {
				$final_settings[ $field_name ] = '';
			}
		} else {
			// If no CSS selector is set then the default value must be used
			if ( $field_name === 'coil_content_container' && ( ! isset( $exclusive_settings[ $field_name ] ) || sanitize_text_field( $exclusive_settings[ $field_name ] ) === '' ) ) {
				$final_settings[ $field_name ] = Admin\get_css_selector_default();
			} elseif ( ( $field_name === 'coil_paywall_title' || $field_name === 'coil_paywall_message' ) && isset( $exclusive_settings[ $field_name ] ) && ctype_space( $exclusive_settings[ $field_name ] ) ) {
				// Allows the option of saving whitespace in the title or message as a way of eliminating it from the paywall.
				$final_settings[ $field_name ] = ' ';
			} else {
				$final_settings[ $field_name ] = ( isset( $exclusive_settings[ $field_name ] ) ) ? sanitize_text_field( $exclusive_settings[ $field_name ] ) : '';
			}
		}
	}

	// Theme validation, branding, icon position, and icon style validation
	$additional_fields = [
		[
			'field_name'    => 'coil_message_color_theme',
			'valid_choices' => Admin\get_theme_color_types(),
			'default'       => $paywall_defaults['coil_message_color_theme'],
		],
		[
			'field_name'    => 'coil_message_branding',
			'valid_choices' => Admin\get_paywall_branding_options(),
			'default'       => $paywall_defaults['coil_message_branding'],
		],
		[
			'field_name'    => 'coil_padlock_icon_position',
			'valid_choices' => Admin\get_padlock_title_icon_position_options(),
			'default'       => $exclusive_post_defaults['coil_padlock_icon_position'],
		],
		[
			'field_name'    => 'coil_padlock_icon_style',
			'valid_choices' => Admin\get_padlock_title_icon_style_options(),
			'default'       => $exclusive_post_defaults['coil_padlock_icon_style'],
		],
		[
			'field_name'    => 'coil_padlock_icon_size',
			'valid_choices' => Admin\get_padlock_title_icon_size_options(),
			'default'       => $exclusive_post_defaults['coil_padlock_icon_size'],
		],
	];

	foreach ( $additional_fields as $field_item ) {
		$field_name                    = $field_item['field_name'];
		$valid_choices                 = $field_item['valid_choices'];
		$default                       = $field_item['default'];
		$final_settings[ $field_name ] = isset( $exclusive_settings[ $field_name ] ) && in_array( $exclusive_settings[ $field_name ], $valid_choices, true ) ? sanitize_key( $exclusive_settings[ $field_name ] ) : $default;
	}

	// Validates all checkbox input fields
	$checkbox_fields = [
		'coil_message_font',
		'coil_title_padlock',
		'coil_exclusive_toggle',
	];

	foreach ( $checkbox_fields as $field_name ) {
		$final_settings[ $field_name ] = isset( $exclusive_settings[ $field_name ] ) && ( $exclusive_settings[ $field_name ] === 'on' || $exclusive_settings[ $field_name ] === true ) ? true : false;
	}
	return $final_settings;
}

/**
 * Validates the streaming support widget settings.
 *
 * @param array $streaming_widget_settings
 * @return array
 */
function streaming_widget_settings_group_validation( $streaming_widget_settings ): array {
	$final_settings = [];
	$defaults       = Admin\get_streaming_widget_defaults();

	// Validates all text input fields
	$text_fields = [
		'streaming_widget_text',
		'streaming_widget_link',
		'members_streaming_widget_text',
	];

	foreach ( $text_fields as $field_name ) {

		if ( $field_name === 'streaming_widget_link' ) {
			// Check the URL has something dot something to be valid. Only save it if valid.
			if ( isset( $streaming_widget_settings[ $field_name ] ) && preg_match( '/.+\..+/', $streaming_widget_settings[ $field_name ] ) === 1 ) {
				$final_settings[ $field_name ] = esc_url_raw( $streaming_widget_settings[ $field_name ] );
			} else {
				$final_settings[ $field_name ] = '';
			}
		} elseif ( ( $field_name === 'streaming_widget_text' || $field_name === 'members_streaming_widget_text' ) && isset( $streaming_widget_settings[ $field_name ] ) && ctype_space( $streaming_widget_settings[ $field_name ] ) ) {
			// Allows the option of saving whitespace in the streaming support widget text as a way of eliminating it to only show the icon.
			$final_settings[ $field_name ] = ' ';
		} else {
			$final_settings[ $field_name ] = ( isset( $streaming_widget_settings[ $field_name ] ) ) ? sanitize_text_field( $streaming_widget_settings[ $field_name ] ) : '';
		}
	}

	$checkbox_fields = [ 'streaming_widget_toggle', 'streaming_widget_member_display' ];

	foreach ( $checkbox_fields as $field_name ) {
		$final_settings[ $field_name ] = isset( $streaming_widget_settings[ $field_name ] ) && ( $streaming_widget_settings[ $field_name ] === 'on' || $streaming_widget_settings[ $field_name ] === true ) ? true : false;
	}

	// Validates streaming support widget margins
	$margin_fields = Admin\get_streaming_widget_margin_key_defaults();

	foreach ( $margin_fields as $field_name => $default ) {

		if ( isset( $streaming_widget_settings[ $field_name ] ) ) {
			$number                        = filter_var( $streaming_widget_settings[ $field_name ], FILTER_SANITIZE_NUMBER_INT );
			$final_settings[ $field_name ] = $number !== false ? $number : $default;
		} else {
			$final_settings[ $field_name ] = '';
		}
	}

	// A list of valid post types
	$post_type_options = Coil\get_supported_post_types( 'objects' );
	foreach ( $post_type_options as $post_type ) {
		// Validates streaming support widget visibility settings
		$widget_visibility_setting_key = $post_type->name . '_streaming_widget_visibility';
		$valid_options                 = [ 'show', 'hide' ];

		// The default value is to show
		$final_settings[ $widget_visibility_setting_key ] = isset( $streaming_widget_settings[ $widget_visibility_setting_key ] ) && in_array( $streaming_widget_settings[ $widget_visibility_setting_key ], $valid_options, true ) ? sanitize_key( $streaming_widget_settings[ $widget_visibility_setting_key ] ) : $defaults['post_type_widget_visibility'];
	}

	// Validates the streaming support widget's color theme, size, and position.
	$additional_fields = [
		[
			'field_name'    => 'streaming_widget_color_theme',
			'valid_choices' => Admin\get_theme_color_types(),
		],
		[
			'field_name'    => 'streaming_widget_size',
			'valid_choices' => Admin\get_streaming_widget_size_options(),
		],
		[
			'field_name'    => 'streaming_widget_position',
			'valid_choices' => array_keys( Admin\get_streaming_widget_position_options() ),
		],
	];

	foreach ( $additional_fields as $field_item ) {
		$field_name                    = $field_item['field_name'];
		$valid_choices                 = $field_item['valid_choices'];
		$final_settings[ $field_name ] = isset( $streaming_widget_settings[ $field_name ] ) && in_array( $streaming_widget_settings[ $field_name ], $valid_choices, true ) ? sanitize_key( $streaming_widget_settings[ $field_name ] ) : $defaults[ $field_name ];
	}

	return $final_settings;
}

/* ------------------------------------------------------------------------ *
 * Settings Rendering
 * ------------------------------------------------------------------------ */

/**
 * Renders the output of the welcome tab.
 * This contains the payment pointer and also acts as a guide for the other tabs.
 *
 * @return void
 */
function coil_settings_welcome_render_callback() {
	?>
	<div class="tab-styling">
		<?php

		Rendering\render_settings_section_heading(
			__( 'About the Coil Plugin', 'coil-web-monetization' )
		);

		// Monetization Section
		Rendering\render_welcome_section(
			'',
			__( 'The Coil WordPress Plugin lets you enable Web Monetization on your website. With Web Monetization, you automatically receive streaming payments whenever Coil members visit your site.', 'coil-web-monetization' )
		);

		// Exclusive Content Section
		Rendering\render_welcome_section(
			__( 'Exclusive Content', 'coil-web-monetization' ),
			__( 'Offer exclusive content to Coil members as a perk for them supporting you.', 'coil-web-monetization' ),
			'exclusive_settings',
			__( 'Enable Exclusive Content', 'coil-web-monetization' )
		);

		// Streaming Support Widget Section
		Rendering\render_welcome_section(
			__( 'Streaming Support Widget', 'coil-web-monetization' ),
			__( 'Show that you accept support from Coil members by displaying a widget on certain pages of your site. If you choose to show the widget to active Coil members, the widget\'s text changes to thank them for their support.', 'coil-web-monetization' ),
			'streaming_widget',
			__( 'Add Widget', 'coil-web-monetization' )
		);

		?>
	</div>
	<?php
}

/**
 * Renders the output of the help links sidebar tab.
 *
 * @return void
 */
function coil_settings_sidebar_render_callback() {

	?>
	<div class="settings-sidebar">
		<div class="settings-sidebar-body">
			<header>
				<svg width="22px" height="22px" viewBox="0 0 22 22" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
					<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<g id="help-wheel" fill="#EE8249" fill-rule="nonzero">
							<path d="M11,21.5 C9.073,21.5 7.186,20.97 5.545,19.969 C5.545,19.969 5.531,19.96 5.524,19.956 C4.112,19.09 2.908,17.885 2.042,16.473 C2.038,16.467 2.031,16.455 2.031,16.455 C1.03,14.813 0.5,12.927 0.5,11 C0.5,9.073 1.03,7.187 2.031,5.545 C2.031,5.545 2.042,5.528 2.047,5.52 C3.978,2.377 7.325,0.5 11,0.5 C14.677,0.5 18.024,2.378 19.955,5.523 C19.96,5.53 19.969,5.545 19.969,5.545 C20.97,7.186 21.5,9.073 21.5,11 C21.5,12.927 20.97,14.814 19.969,16.455 C19.969,16.455 19.962,16.466 19.959,16.471 C19.092,17.886 17.886,19.093 16.47,19.96 C16.465,19.963 16.455,19.97 16.455,19.97 C14.814,20.97 12.927,21.5 11,21.5 Z M7.123,19.12 C8.328,19.697 9.658,20 11,20 C12.342,20 13.672,19.697 14.877,19.12 L11.25,15.493 C11.164,15.498 11.081,15.5 11,15.5 C10.919,15.5 10.837,15.498 10.751,15.492 L7.123,19.12 Z M3.668,16.211 C4.254,17.034 4.966,17.746 5.789,18.333 L9.063,15.06 C8.136,14.616 7.385,13.865 6.941,12.938 L3.668,16.211 Z M16.211,18.332 C17.035,17.746 17.746,17.034 18.333,16.21 L15.059,12.937 C14.615,13.863 13.864,14.614 12.938,15.058 L16.211,18.332 Z M19.12,14.877 C19.697,13.672 20,12.342 20,11 C20,9.658 19.697,8.328 19.12,7.123 L15.493,10.75 C15.498,10.836 15.5,10.919 15.5,11 C15.5,11.082 15.498,11.165 15.492,11.251 L19.12,14.877 Z M2.88,7.123 C2.303,8.328 2,9.658 2,11 C2,12.342 2.303,13.672 2.88,14.877 L6.508,11.25 C6.503,11.164 6.5,11.081 6.5,11 C6.5,10.919 6.503,10.836 6.508,10.75 L2.88,7.123 Z M11,8 C9.346,8 8,9.346 8,11 C8,12.654 9.346,14 11,14 C12.654,14 14,12.654 14,11 C14,9.346 12.654,8 11,8 Z M6.941,9.063 C7.385,8.137 8.136,7.385 9.062,6.942 L5.789,3.668 C4.965,4.254 4.254,4.966 3.668,5.789 L6.941,9.063 Z M12.938,6.941 C13.864,7.385 14.615,8.136 15.059,9.062 L18.332,5.789 C17.746,4.966 17.034,4.254 16.211,3.668 L12.938,6.941 Z M10.75,6.508 C10.836,6.503 10.919,6.5 11,6.5 C11.081,6.5 11.164,6.503 11.25,6.508 L14.878,2.881 C13.672,2.303 12.342,2 11,2 C9.658,2 8.328,2.303 7.122,2.88 L10.75,6.508 Z" id="Shape"></path>
						</g>
					</g>
				</svg>

				<h3><?php esc_html_e( 'Useful links &amp; how to guides', 'coil-web-monetization' ); ?></h3>
			</header>
			<section>
				<ul>
					<?php
					printf(
						'<li><a target="_blank" href="%1$s">%2$s</a></li>',
						esc_url( 'https://help.coil.com/docs/monetize/content/wp-overview/' ),
						esc_html__( 'How to configure the Coil plugin', 'coil-web-monetization' )
					);

					printf(
						'<li><a target="_blank" href="%1$s">%2$s</a></li>',
						esc_url( 'https://help.coil.com/docs/general-info/intro-to-coil/' ),
						esc_html__( 'About Coil and Web Monetization', 'coil-web-monetization' )
					);

					printf(
						'<li><a target="_blank" href="%1$s">%2$s</a></li>',
						esc_url( 'https://webmonetization.org/docs/ilp-wallets' ),
						esc_html__( 'Digital wallets and payment pointers', 'coil-web-monetization' )
					);

					printf(
						'<li><a target="_blank" href="%1$s">%2$s</a></li>',
						esc_url( 'https://help.coil.com/docs/monetize/get-creator-account/' ),
						esc_html__( 'Get a free Coil creator account', 'coil-web-monetization' )
					);
					?>
				</ul>
			</section>
		</div>
	</div>
	<?php
}

/**
 * Renders the output of the payment pointer input field.
 *
 * @return void
 */
function coil_settings_payment_pointer_render_callback() {
	?>
	<div class="tab-styling">
		<?php

		Rendering\render_settings_section_heading(
			__( 'Payment Pointer', 'coil-web-monetization' ),
			__( 'Enter your digital wallet\'s payment pointer to receive payments', 'coil-web-monetization' )
		);

		// Render the payment pointer input field
		$payment_pointer_id = 'coil_payment_pointer';
		Rendering\render_text_input_field(
			$payment_pointer_id,
			'coil_general_settings_group[' . $payment_pointer_id . ']',
			Admin\get_payment_pointer_setting( $payment_pointer_id ),
			'$wallet.example.com/alice'
		);

		printf(
			'<p class="%s">%s (<a href="%s" target="%s" >%s</a>)</p>',
			esc_attr( 'description' ),
			esc_html__( 'Don\'t have a digital wallet or know your payment pointer?', 'coil-web-monetization' ),
			esc_url( 'https://webmonetization.org/docs/ilp-wallets' ),
			esc_attr( '_blank' ),
			esc_html__( 'Learn more', 'coil-web-monetization' )
		);

		?>
	</div>
	<?php
}

/**
 * Renders the output of the global monetization default settings
 * showing radio buttons based on the post types available in WordPress.
 *
 * @return void
 */
function coil_settings_monetization_render_callback() {
	?>
	<div class="tab-styling">
		<?php
		Rendering\render_settings_section_heading(
			__( 'Monetization Settings', 'coil-web-monetization' ),
			__( 'Manage the default monetization for your post types. You\'ll receive streaming payments whenever Coil members visit monetized posts.', 'coil-web-monetization' )
		);

		// Using a function to generate the table with the global monetization radio button options.
		Rendering\render_generic_post_type_table(
			'coil_general_settings_group',
			Admin\get_monetization_types(),
			'radio',
			'monetization',
			Admin\get_general_settings()
		);

		printf(
			'<p class="%s">%s</p>',
			esc_attr( 'description' ),
			esc_html__( 'You can override these settings in the Category, Tag, Page, and Post menus.', 'coil-web-monetization' )
		);

		?>
	</div>
	<?php
}

/**
 * Renders the output of the paywall appearance settings.
 * This includes custom messages, color theme, branding, and font options.
 *
 * @return void
 */
function coil_settings_enable_exclusive_toggle_render_callback() {
	?>
	<div class="tab-styling">
		<?php
		Rendering\render_settings_section_heading(
			__( 'Exclusive Content', 'coil-web-monetization' )
		);

		printf(
			'<p>%s (<a href="%s" target="%s" >%s</a>)</p>',
			esc_html__( 'Exclusive content is only available to Coil members.', 'coil-web-monetization' ),
			esc_url( 'https://help.coil.com/docs/monetize/content/wp-exclusive-content' ),
			esc_attr( '_blank' ),
			esc_html__( 'Learn more', 'coil-web-monetization' )
		);

		printf(
			'<p>%s (<a href="%s" target="%s" >%s</a>)</p>',
			esc_html__( 'Coil members must use the Coil extension or supported mobile browser to unlock exclusive content.', 'coil-web-monetization' ),
			esc_url( 'https://help.coil.com/docs/membership/coil-extension/' ),
			esc_attr( '_blank' ),
			esc_html__( 'Learn more', 'coil-web-monetization' )
		);

		printf(
			'<p>%s</p>',
			esc_html__( 'Disabling exclusive content will make all content publicly available.', 'coil-web-monetization' )
		);

		$exclusive_toggle_id = 'coil_exclusive_toggle';
		Rendering\render_toggle(
			$exclusive_toggle_id,
			'coil_exclusive_settings_group[' . $exclusive_toggle_id . ']',
			Admin\is_exclusive_content_enabled()
		);
		?>
	</div>
	<?php
}

/**
 * Renders the output of the paywall appearance settings.
 * This includes custom messages, color theme, branding, and font options.
 *
 * @return void
 */
function coil_settings_paywall_render_callback() {
	?>
	<div class="tab-styling exclusive-content-section">
		<?php
		Rendering\render_settings_section_heading(
			__( 'Paywall Appearance', 'coil-web-monetization' ),
			__( 'When a post is set to Exclusive, this paywall replaces the post\'s content for users without an active Coil membership.', 'coil-web-monetization' )
		);
		?>
		<div class="coil-row">
			<div class="coil-column-7">
				<?php
				// Renders the textfield for each paywall text field input.
				$text_fields = [
					[
						'id'   => 'coil_paywall_title',
						'type' => 'text',
					],
					[
						'id'   => 'coil_paywall_message',
						'type' => 'textarea',
					],
					[
						'id'   => 'coil_paywall_button_text',
						'type' => 'text',
					],
					[
						'id'   => 'coil_paywall_button_link',
						'type' => 'text',
					],
				];

				foreach ( $text_fields as $field_name => $field_props ) {
					coil_paywall_appearance_text_field_settings_render_callback( $field_props['id'], $field_props['type'] );
				}

				// Renders the color theme radio buttons
				Rendering\render_input_field_heading(
					__( 'Color Theme', 'coil-web-monetization' )
				);
				paywall_theme_render_callback();

				// Renders the branding selection box
				Rendering\render_input_field_heading(
					__( 'Branding', 'coil-web-monetization' )
				);
				paywall_branding_render_callback();

				// Renders the font checkbox
				paywall_font_render_callback();
				?>
			</div>
			<div class="coil-column-5">
				<?php
				Rendering\render_input_field_heading(
					__( 'Preview', 'coil-web-monetization' )
				);
				?>
				<div class=" coil-preview">
					<div class="coil-paywall-container" data-theme="<?php echo esc_attr( Admin\get_paywall_appearance_setting( 'coil_message_color_theme' ) ); ?>">
						<?php printf( '<img class="%s %s" src="%s" />', 'coil-paywall-image', Admin\get_paywall_appearance_setting( 'coil_message_branding', true ), get_paywall_theme_logo() ); ?>
						<?php printf( '<h3 class="%s">%s</h3>', 'coil-paywall-heading', Admin\get_paywall_appearance_setting( 'coil_paywall_title', true ) ); ?>
						<?php printf( '<p class="%s">%s</p>', 'coil-paywall-body', Admin\get_paywall_appearance_setting( 'coil_paywall_message', true ) ); ?>
						<?php printf( '<a class="%s">%s</a>', 'coil-paywall-cta', Admin\get_paywall_appearance_setting( 'coil_paywall_button_text', true ) ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Returns the path for which ever image is being used for the preview
 *
 * @return void
 */
function get_paywall_theme_logo() {
	$logo_setting = Admin\get_paywall_appearance_setting( 'coil_message_branding', true );

	$coil_logo_type = ( Admin\get_paywall_appearance_setting( 'coil_message_color_theme' ) === 'light' ? 'black' : 'white' );

	switch ( $logo_setting ) {
		case 'coil_logo':
			$logo_url = plugin_dir_url( COIL__FILE__ ) . 'assets/images/coil-icn-' . $coil_logo_type . '.svg';
			break;
		case 'site_logo':
			$logo_url = Admin\get_site_logo_src();
			break;
		case 'no_logo':
		default:
			$logo_url = false;
			break;
	}

	return $logo_url;
}

/**
 * Renders the output of the paywall theme radio button settings.
 *
 * @return void
 */
function paywall_theme_render_callback() {

	// Set the theme color
	$message_color_theme = Admin\get_paywall_appearance_setting( 'coil_message_color_theme' );
	$theme_name          = 'coil_exclusive_settings_group[coil_message_color_theme]';

	echo '<div class="coil-radio-group">';

	Rendering\render_radio_button_field(
		'light_color_theme',
		$theme_name,
		'light',
		__( 'Light theme', 'coil-web-monetization' ),
		$message_color_theme,
		true
	);

	Rendering\render_radio_button_field(
		'dark_color_theme',
		$theme_name,
		'dark',
		__( 'Dark theme', 'coil-web-monetization' ),
		$message_color_theme
	);

	echo '</div>';
}

/**
 * Renders the output of the branding selection box settings.
 *
 * @return void
 */
function paywall_branding_render_callback() {

	// Defaults to the Coil logo
	$message_branding_value = Admin\get_paywall_appearance_setting( 'coil_message_branding' );

	printf(
		'<select name="%s" id="%s">',
		esc_attr( 'coil_exclusive_settings_group[coil_message_branding]' ),
		esc_attr( 'coil_branding' )
	);

	printf(
		'<option value="%s" %s>%s</option>',
		esc_attr( 'coil_logo' ),
		( ! empty( $message_branding_value ) && $message_branding_value === 'coil_logo' || empty( $message_branding_value ) ? 'selected="selected"' : false ),
		esc_attr( 'Show Coil logo' )
	);

	printf(
		'<option value="%s" %s>%s</option>',
		esc_attr( 'site_logo' ),
		( ! empty( $message_branding_value ) && $message_branding_value === 'site_logo' ? 'selected="selected"' : false ),
		esc_attr( 'Show website logo' )
	);

	printf(
		'<option value="%s" %s>%s</option>',
		esc_attr( 'no_logo' ),
		( ! empty( $message_branding_value ) && $message_branding_value === 'no_logo' ? 'selected="selected"' : false ),
		esc_attr( 'Show no logo' )
	);

	echo '</select>';

	printf(
		'<p class="%s" style="%s">%s<a href="%s">%s</a>.</p>',
		esc_attr( 'description set-site-logo-description' ),
		esc_attr( 'display: none' ),
		esc_html__( 'You can change your site logo in the ', 'coil-web-monetization' ),
		esc_url( admin_url( 'customize.php?return=%2Fwp-admin%2Fthemes.php' ) ),
		esc_html__( 'Appearance Settings', 'coil-web-monetization' )
	);
}

/**
 * Renders the output of the font option checkbox
 * The default is unchecked
 * @return void
 */
function paywall_font_render_callback() {

	$font_input_id = 'coil_message_font';

	Rendering\render_checkbox_field(
		$font_input_id,
		'coil_exclusive_settings_group[' . $font_input_id . ']',
		__( 'Use theme font styles', 'coil-web-monetization' ),
		Admin\get_paywall_appearance_setting( $font_input_id )
	);
}

/**
 * Renders the output of the exclusive icon appearance settings.
 * This includes choosing whether to tdisplay the padloc, where to display it and which icon to use.
 *
 * @return void
 */
function coil_settings_exclusive_icon_render_callback() {

	?>
	<div class="tab-styling exclusive-content-section">
		<?php
		Rendering\render_settings_section_heading(
			__( 'Exclusive Icon Appearance', 'coil-web-monetization' ),
			__( 'Customize the appearance of exclusive posts on archive pages.', 'coil-web-monetization' )
		);
		?>
		<div class="coil-row">
			<div class="coil-column-7">
				<?php
				// Renders the padlock display checkbox
				$padlock_input_id = 'coil_title_padlock';
				Rendering\render_checkbox_that_toggles_content(
					$padlock_input_id,
					'coil_exclusive_settings_group[' . $padlock_input_id . ']',
					__( 'Show icon next to exclusive post titles', 'coil-web-monetization' ),
					Admin\get_exlusive_icon_setting( $padlock_input_id )
				);

				// Renders the icon position radio buttons
				Rendering\render_input_field_heading(
					__( 'Icon Position', 'coil-web-monetization' ),
					'coil_icon_position_label'
				);
				coil_padlock_icon_position_selection_render_callback();

				// Renders the icon style radio buttons
				Rendering\render_input_field_heading(
					__( 'Icon Style', 'coil-web-monetization' ),
					'coil_icon_style_label'
				);
				coil_padlock_icon_style_selection_render_callback();

				$padlock_icon_styles  = Admin\get_padlock_icon_styles();
				$padlock_icon         = Admin\get_exlusive_icon_setting( 'coil_padlock_icon_style', true );
				$padlock_icon_enabled = Admin\get_exlusive_icon_setting( 'coil_title_padlock' );

				// Renders the icon size radio buttons
				Rendering\render_input_field_heading(
					__( 'Icon Size', 'coil-web-monetization' ),
					'coil_icon_size_label'
				);
				coil_padlock_icon_size_selection_render_callback();
				?>
			</div>
			<div class="coil-column-5 <?php echo esc_attr( $padlock_icon_enabled ? '' : 'hidden' ); ?>">
				<?php
				Rendering\render_input_field_heading(
					__( 'Preview', 'coil-web-monetization' )
				);
				?>
				<div class="coil-preview short">
					<div class="coil-title-preview-container" data-padlock-icon-position="<?php echo esc_attr( Admin\get_exlusive_icon_setting( 'coil_padlock_icon_position' ) ); ?>">
						<div class="coil-title-preview-row coil-title-padlock-row">
							<span class="coil-padlock-icon">
								<?php echo $padlock_icon_styles[ $padlock_icon ]; ?>
							</span>
							<div class="coil-title-preview-bar title"></div>
						</div>
						<div class="coil-title-preview-row">
							<div class="coil-title-preview-bar sub-title coil-column-6"></div>
						</div>
						<div class="coil-title-preview-row">
							<div class="coil-title-preview-bar body coil-column-12"></div>
						</div>
						<div class="coil-title-preview-row">
							<div class="coil-title-preview-bar body coil-column-12"></div>
						</div>
						<div class="coil-title-preview-row">
							<div class="coil-title-preview-bar body coil-column-12"></div>
						</div>
						<div class="coil-title-preview-row">
							<div class="coil-title-preview-bar body coil-column-10"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Renders the output of the padlock position radio button settings.
 *
 * @return void
 */
function coil_padlock_icon_position_selection_render_callback() {

	// Set the icon position
	$padlock_icon_position = Admin\get_exlusive_icon_setting( 'coil_padlock_icon_position' );
	$name                  = 'coil_exclusive_settings_group[coil_padlock_icon_position]';

	echo '<div class="coil-radio-group">';

	Rendering\render_radio_button_field(
		'padlock_icon_position_before',
		$name,
		'before',
		__( 'Before title', 'coil-web-monetization' ),
		$padlock_icon_position,
		true
	);

	Rendering\render_radio_button_field(
		'padlock_icon_position_after',
		$name,
		'after',
		__( 'After title', 'coil-web-monetization' ),
		$padlock_icon_position
	);

	echo '</div>';
}

/**
 * Renders the output of the padlock position radio button settings.
 *
 * @return void
 */
function coil_padlock_icon_style_selection_render_callback() {

	// Set the icon style
	$padlock_icon_style = Admin\get_exlusive_icon_setting( 'coil_padlock_icon_style' );

	$padlock_icon_styles = Admin\get_padlock_icon_styles();

	echo '<div class="coil-radio-group">';
	foreach ( $padlock_icon_styles as $value => $svg ) {
		echo sprintf(
			'<label for="%1$s"><input type="radio" name="%2$s" id="%1$s" value="%3$s" %4$s /> %5$s</label>',
			esc_attr( 'coil_padlock_icon_style_' . $value ),
			esc_attr( 'coil_exclusive_settings_group[coil_padlock_icon_style]' ),
			esc_attr( $value ),
			( ! empty( $padlock_icon_style ) && $padlock_icon_style === $value || empty( $padlock_icon_style ) && 'lock' === $value ? 'checked="checked"' : false ),
			$svg
		);
	}
	echo '</div>';
}

/**
 * Renders the output of the padlock position radio button settings.
 *
 * @return void
 */
function coil_padlock_icon_size_selection_render_callback() {

	// Defaults to the small size
	$coil_padlock_icon_size = Admin\get_exlusive_icon_setting( 'coil_padlock_icon_size' );
	$name                   = 'coil_exclusive_settings_group[coil_padlock_icon_size]';

	echo '<div class="coil-radio-group">';

	Rendering\render_radio_button_field(
		'padlock_icon_size_small',
		$name,
		esc_attr( 'small' ),
		__( 'Small', 'coil-web-monetization' ),
		$coil_padlock_icon_size,
		true
	);

	Rendering\render_radio_button_field(
		'padlock_icon_size_medium',
		$name,
		esc_attr( 'medium' ),
		__( 'Medium', 'coil-web-monetization' ),
		$coil_padlock_icon_size
	);

	Rendering\render_radio_button_field(
		'padlock_icon_size_large',
		$name,
		esc_attr( 'large' ),
		__( 'Large', 'coil-web-monetization' ),
		$coil_padlock_icon_size
	);

	echo '</div>';
}

/**
 * Renders the output of the global post type visibility default settings
 * showing radio buttons based on the post types available in WordPress.
 * @return void
 */
function coil_settings_post_visibility_render_callback() {
	?>
	<div class="tab-styling exclusive-content-section">
		<?php
		Rendering\render_settings_section_heading(
			__( 'Visibility Settings', 'coil-web-monetization' ),
			__( 'Manage the default visibility for your post types. Content within exclusive post types is only visible to Coil members. Everyone else will see the paywall.', 'coil-web-monetization' )
		);
		printf(
			'<p>%1$s<a href="%2$s">%3$s</a>%4$s</p>',
			esc_html__( 'A post type can only be marked as exclusive if it is also set to Monetized under ', 'coil-web-monetization' ),
			esc_url( admin_url( 'admin.php?page=coil_settings&tab=general_settings', COIL__FILE__ ) ),
			esc_html__( 'General Settings', 'coil-web-monetization' ),
			'.'
		);

		// Using a function to generate the table with the global visibility radio button options.
		Rendering\render_generic_post_type_table(
			'coil_exclusive_settings_group',
			Admin\get_visibility_types(),
			'radio',
			'visibility',
			Admin\get_exclusive_settings()
		);

		printf(
			'<p class="%s">%s</p>',
			esc_attr( 'description' ),
			esc_html__( 'You can override these settings in the Category, Tag, Page, and Post menus.', 'coil-web-monetization' )
		);
		?>
	</div>
	<?php
}

/**
 * Renders the output of the excerpt settings showing checkbox
 * inputs based on the post types available in WordPress.
 *
 * @return void
 */
function coil_settings_excerpt_display_render_callback() {

	?>
	<div class="tab-styling exclusive-content-section">
		<?php
		Rendering\render_settings_section_heading(
			__( 'Excerpt Settings', 'coil-web-monetization' ),
			__( 'Select whether to show a short summary for any pages, posts, or other content types you choose to make exclusive. Whether excerpts are supported and where excerpts appear (for example, under page titles, on archive pages, etc.) depends on your theme.', 'coil-web-monetization' )
		);

		// Using a function to generate the table with the post type excerpt checkboxes.
		Rendering\render_generic_post_type_table(
			'coil_exclusive_settings_group',
			[ 'Display Excerpt' ],
			'checkbox',
			'excerpt',
			Admin\get_exclusive_settings()
		);
		?>
	</div>
	<?php
}

/**
 * Render the CSS selector settings input field.
 *
 * @return void
 */
function coil_settings_css_selector_render_callback() {

	?>
	<div class="tab-styling exclusive-content-section">
		<?php
		Rendering\render_settings_section_heading(
			__( 'CSS Selector', 'coil-web-monetization' )
		);

		printf(
			'<p>%s (<a href="%s" target="_blank">%s</a>)</p>',
			/* translators: 1) HTML link open tag, 2) HTML link close tag, 3) HTML link open tag, 4) HTML link close tag. */
				esc_html__( 'This plugin uses CSS selectors to control exclusive content. Many themes use the plugin\'s default selectors. If your exclusive content is being incorrectly shown or hidden, there\'s a strong possibility your theme is using different selectors. Enter your theme\'s CSS selectors here.', 'coil-web-monetization' ),
			esc_url( 'https://help.coil.com/docs/monetize/content/wp-exclusive-content/#adjust-the-css-selectors-used-by-the-plugin-as-needed' ),
			esc_html__( 'Learn more', 'coil-web-monetization' )
		);

		$exclusive_settings = Admin\get_exclusive_settings();

		printf(
			'<input class="%s" type="%s" name="%s" id="%s" value="%s" placeholder="%s"/>',
			esc_attr( 'wide-input' ),
			esc_attr( 'text' ),
			esc_attr( 'coil_exclusive_settings_group[coil_content_container]' ),
			esc_attr( 'coil_content_container' ),
			esc_attr( $exclusive_settings['coil_content_container'] ),
			esc_attr( Admin\get_css_selector_default() )
		);

		printf(
			'<p><button class="%s" id="%s">%s</button></p>',
			esc_attr( 'button btn-primary' ),
			esc_attr( 'css_selector_button' ),
			esc_attr( 'Get CSS Selector Automatically' )
		);
		?>
	</div>
	<?php
}

/**
 * Renders the output of the content messaging customization setting
 * @return void
 */
function coil_paywall_appearance_text_field_settings_render_callback( $field_name, $field_type = 'text' ) {
	$defaults = Admin\get_paywall_text_defaults();

	switch ( $field_name ) {
		case 'coil_paywall_title':
			$heading = __( 'Title', 'coil-web-monetization' );
			break;
		case 'coil_paywall_message':
			$heading = __( 'Message', 'coil-web-monetization' );
			break;
		case 'coil_paywall_button_text':
			$heading = __( 'Button Text', 'coil-web-monetization' );
			break;
		case 'coil_paywall_button_link':
			$heading = __( 'Button Link', 'coil-web-monetization' );
			break;
		default:
			$heading = '';
			break;
	}

	if ( 'text' === $field_type ) {
		// Render the text input fields
		Rendering\render_text_input_field(
			$field_name,
			'coil_exclusive_settings_group[' . $field_name . ']',
			Admin\get_paywall_appearance_setting( $field_name ),
			$defaults[ $field_name ],
			$heading,
			''
		);
	} else {
		// Print <textarea> field for the paywall message
		if ( '' !== $heading ) {
			Rendering\render_input_field_heading( $heading );
		}

		printf(
			'<textarea class="%s" name="%s" id="%s" placeholder="%s">%s</textarea>',
			esc_attr( 'wide-input' ),
			esc_attr( 'coil_exclusive_settings_group[' . $field_name . ']' ),
			esc_attr( $field_name ),
			esc_attr( $defaults[ $field_name ] ),
			esc_attr( Admin\get_paywall_appearance_setting( $field_name ) )
		);
	}
}

/**
 * Renders the output of the enable streaming support widget toggle
 * @return void
 */
function coil_settings_enable_streaming_widget_toggle_render_callback() {
	?>
	<div class="tab-styling">
		<?php
		Rendering\render_settings_section_heading(
			__( 'Streaming Support Widget', 'coil-web-monetization' )
		);

		printf(
			'<p>%s</p>',
			esc_html__( 'Encourage your audience to support you through Coil by adding a Coil-branded widget to selected post types. You\'ll receive streaming payments for each second a Coil member spends on your web monetized content.', 'coil-web-monetization' )
		);

		printf(
			'<p>%s</p><ul><li>%s</li><li>%s</li><li>%s</li></ul>',
			esc_html__( 'When enabled, the widget will not appear:', 'coil-web-monetization' ),
			esc_html__( 'On post types set to Not Monetized.', 'coil-web-monetization' ),
			esc_html__( 'When a visitor dismisses the widget. The widget reappears after the cookie expires (2 weeks) or sooner if the visitor clears their cache.', 'coil-web-monetization' ),
			esc_html__( 'When a visitor is not a Coil member and a post is set to Exclusive. Non-members will see your paywall. Coil members will see your content and the widget unless you\'ve chosen to hide the widget from members.', 'coil-web-monetization' )
		);

		$streaming_widget_toggle_id = 'streaming_widget_toggle';
		Rendering\render_toggle(
			$streaming_widget_toggle_id,
			'streaming_widget_settings_group[' . $streaming_widget_toggle_id . ']',
			Admin\is_streaming_widget_enabled()
		);
		?>
	</div>
	<?php
}

/**
 * Renders the streaming support widget visibility settings
 * @return void
 */
function coil_settings_streaming_widget_visibility_render_callback() {
	?>
	<div class="tab-styling streaming-widget-section">
		<?php
		Rendering\render_settings_section_heading(
			__( 'Visibility', 'coil-web-monetization' ),
			__( 'Select whether to show or hide the widget on specific post types.', 'coil-web-monetization' )
		);

		// Using a function to generate the table with the post type excerpt checkboxes.
		$columns = [
			'show' => 'Show',
			'hide' => 'Hide',
		];
		Rendering\render_generic_post_type_table(
			'streaming_widget_settings_group',
			$columns,
			'radio',
			'streaming_widget_visibility',
			Admin\get_streaming_widget_settings()
		);
		?>
	</div>
	<?php
}

/**
 * Renders the streaming support widget customization settings
 * @return void
 */
function coil_settings_streaming_widget_settings_render_callback() {
	?>
	<div class="tab-styling streaming-widget-section">
		<div class="coil-row">
			<div class="coil-column-7">
				<?php
				$defaults = Admin\get_streaming_widget_defaults();

				Rendering\render_settings_section_heading(
					__( 'Widget Settings', 'coil-web-monetization' )
				);

				// Render the streaming support widget text input field
				$streaming_widget_text_id = 'streaming_widget_text';
				Rendering\render_text_input_field(
					$streaming_widget_text_id,
					'streaming_widget_settings_group[' . $streaming_widget_text_id . ']',
					Admin\get_streaming_widget_setting( $streaming_widget_text_id ),
					$defaults[ $streaming_widget_text_id ],
					__( 'Text', 'coil-web-monetization' )
				);

				// Render the streaming support widget link input field
				$streaming_widget_link_id = 'streaming_widget_link';
				Rendering\render_text_input_field(
					$streaming_widget_link_id,
					'streaming_widget_settings_group[' . $streaming_widget_link_id . ']',
					Admin\get_streaming_widget_setting( $streaming_widget_link_id ),
					$defaults[ $streaming_widget_link_id ],
					__( 'Link', 'coil-web-monetization' )
				);

				// Render the streaming support widget member's display checkbox
				echo '<br>';
				$streaming_widget_member_display_id = 'streaming_widget_member_display';
				Rendering\render_checkbox_that_toggles_content(
					$streaming_widget_member_display_id,
					'streaming_widget_settings_group[' . $streaming_widget_member_display_id . ']',
					__( 'Show widget to Coil members', 'coil-web-monetization' ),
					Admin\get_streaming_widget_setting( $streaming_widget_member_display_id )
				);

				// Render the streaming support widget's member text input field
				$streaming_widget_member_text_id = 'members_streaming_widget_text';
				Rendering\render_text_input_field(
					$streaming_widget_member_text_id,
					'streaming_widget_settings_group[' . $streaming_widget_member_text_id . ']',
					Admin\get_streaming_widget_setting( $streaming_widget_member_text_id ),
					$defaults[ $streaming_widget_member_text_id ],
					__( 'Message for Coil members', 'coil-web-monetization' )
				);

				Rendering\render_input_field_heading(
					__( 'Color Theme', 'coil-web-monetization' )
				);
				streaming_widget_theme_render_callback();

				Rendering\render_input_field_heading(
					__( 'Widget Size', 'coil-web-monetization' )
				);
				streaming_widget_size_render_callback();

				Rendering\render_input_field_heading(
					__( 'Position on Screen', 'coil-web-monetization' )
				);
				streaming_widget_position_dropdown();

				Rendering\render_input_field_heading(
					__( 'Margin', 'coil-web-monetization' )
				);
				render_streaming_widget_margin_settings();
				?>
			</div>
			<div class="coil-column-5">
				<?php
				Rendering\render_input_field_heading(
					__( 'Preview', 'coil-web-monetization' )
				);

				$streaming_widget_position       = Admin\get_streaming_widget_setting( 'streaming_widget_position' );
				$streaming_widget_size           = Admin\get_streaming_widget_setting( 'streaming_widget_size' );
				$streaming_widget_theme          = Admin\get_streaming_widget_setting( 'streaming_widget_color_theme' );
				$streaming_widget_member_display = Admin\get_streaming_widget_setting( 'streaming_widget_member_display' );

				$coil_logo_type = ( $streaming_widget_theme === 'light' ? 'black' : 'white' );
				?>

				<div class="coil-preview coil-non-members stacked">
					<p><?php _e( 'Without Coil Membership', 'coil-web-monetization' ); ?></p>
					<div class="streaming-widget" data-theme="<?php echo esc_attr( $streaming_widget_theme ); ?>" data-position="<?php echo esc_attr( $streaming_widget_position ); ?>" data-size="<?php echo esc_attr( $streaming_widget_size ); ?>">
						<div>
							<?php printf( '<img class="%s" src="%s" />', 'streaming-widget-image', plugin_dir_url( COIL__FILE__ ) . 'assets/images/coil-icn-' . $coil_logo_type . '.svg' ); ?>
							<div><?php echo Admin\get_streaming_widget_setting( 'streaming_widget_text', true ); ?></div>
						</div>
					</div>
				</div>
				<div class="coil-preview coil-members stacked <?php echo $streaming_widget_member_display === true ? '' : 'hide'; ?>">
					<p><?php _e( 'With Coil Membership', 'coil-web-monetization' ); ?></p>
					<div class="streaming-widget" data-theme="<?php echo esc_attr( $streaming_widget_theme ); ?>" data-position="<?php echo esc_attr( $streaming_widget_position ); ?>" data-size="<?php echo esc_attr( $streaming_widget_size ); ?>">
						<div>
							<?php printf( '<img class="%s" src="%s" />', 'streaming-widget-image', plugin_dir_url( COIL__FILE__ ) . 'assets/images/coil-icn-' . $coil_logo_type . '-streaming.svg' ); ?>
							<div><?php echo Admin\get_streaming_widget_setting( 'members_streaming_widget_text', true ); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Renders the output of the streaming support widget theme radio button settings.
 *
 * @return void
 */
function streaming_widget_theme_render_callback() {

	// Set the theme color settingcoil-preview
	$widget_color_theme = Admin\get_streaming_widget_setting( 'streaming_widget_color_theme' );

	echo '<div class="coil-radio-group">';

	Rendering\render_radio_button_field(
		'dark_color_theme',
		'streaming_widget_settings_group[streaming_widget_color_theme]',
		'dark',
		__( 'Dark', 'coil-web-monetization' ),
		$widget_color_theme,
		true
	);

	Rendering\render_radio_button_field(
		'light_color_theme',
		'streaming_widget_settings_group[streaming_widget_color_theme]',
		'light',
		__( 'Light', 'coil-web-monetization' ),
		$widget_color_theme
	);

	echo '</div>';
}

/**
 * Renders the output of the streaming support widget size radio button settings.
 *
 * @return void
 */
function streaming_widget_size_render_callback() {

	// Set the theme color setting coil-preview
	$widget_size = Admin\get_streaming_widget_setting( 'streaming_widget_size' );

	echo '<div class="coil-radio-group">';

	Rendering\render_radio_button_field(
		'large_size',
		'streaming_widget_settings_group[streaming_widget_size]',
		'large',
		__( 'Large', 'coil-web-monetization' ),
		$widget_size,
		true
	);

	Rendering\render_radio_button_field(
		'small_size',
		'streaming_widget_settings_group[streaming_widget_size]',
		'small',
		__( 'Small', 'coil-web-monetization' ),
		$widget_size
	);

	echo '</div>';
}

/**
 * Renders the output of the streaming support widget position dropdown.
 *
 * @return void
 */
function streaming_widget_position_dropdown() {
	$position = Admin\get_streaming_widget_setting( 'streaming_widget_position' );
	echo sprintf(
		'<select name="%s" id="%s">',
		'streaming_widget_settings_group[streaming_widget_position]',
		'position_dropdown'
	);

	$position_options = Admin\get_streaming_widget_position_options();

	foreach ( $position_options as $setting_key => $setting_value ) {
		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $setting_key ),
			selected( $setting_key, $position ),
			$setting_value
		);
	}
	echo '?></select>';
}

/**
 * Renders the output of the streaming support widget margin table.
 *
 * @return void
 */
function render_streaming_widget_margin_settings() {
	echo '<div class="coil-margin-input-group">';

	$margins = [
		'streaming_widget_top_margin'    => Admin\get_streaming_widget_setting( 'streaming_widget_top_margin' ) !== false ? Admin\get_streaming_widget_setting( 'streaming_widget_top_margin' ) : '',
		'streaming_widget_bottom_margin' => Admin\get_streaming_widget_setting( 'streaming_widget_bottom_margin' ) !== false ? Admin\get_streaming_widget_setting( 'streaming_widget_bottom_margin' ) : '',
		'streaming_widget_right_margin'  => Admin\get_streaming_widget_setting( 'streaming_widget_right_margin' ) !== false ? Admin\get_streaming_widget_setting( 'streaming_widget_right_margin' ) : '',
		'streaming_widget_left_margin'   => Admin\get_streaming_widget_setting( 'streaming_widget_left_margin' ) !== false ? Admin\get_streaming_widget_setting( 'streaming_widget_left_margin' ) : '',
	];

	$placeholders = Admin\get_streaming_widget_margin_key_defaults();

	$desciptions = [
		'streaming_widget_top_margin'    => esc_html__( 'TOP', 'coil-web-monetization' ),
		'streaming_widget_bottom_margin' => esc_html__( 'BOTTOM', 'coil-web-monetization' ),
		'streaming_widget_right_margin'  => esc_html__( 'RIGHT', 'coil-web-monetization' ),
		'streaming_widget_left_margin'   => esc_html__( 'LEFT', 'coil-web-monetization' ),

	];

	foreach ( $margins as $id => $setting ) {
		$location = str_replace( 'streaming_widget_', '', $id );
		$location = str_replace( '_margin', '', $location );
		printf(
			'<div class="%s" style="%s">',
			esc_attr( 'margin-' . $location ),
			esc_attr( 'display:none' )
		);
		// Render the streaming support widget margin text input fields
		if ( $setting !== '' ) {

			$setting = strval( $setting ) . 'px';
		}
		Rendering\render_text_input_field(
			$id,
			'streaming_widget_settings_group[' . $id . ']',
			esc_attr( $setting ),
			esc_attr( $placeholders[ $id ] . 'px' )
		);

		printf(
			'<p class="%s">%s</p>',
			esc_attr( 'description' ),
			esc_attr( $desciptions[ $id ] )
		);
		echo '</div>';
	}
	echo '</div>';
}

/**
 * Creates dismissable welcome notice on coil admin screen
 * @return void
 */
function admin_welcome_notice() {

	global $current_user;

	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	if ( $screen->id !== 'toplevel_page_coil_settings' ) {
		return;
	}

	$payment_pointer_id = Admin\get_payment_pointer_setting();

	if ( $payment_pointer_id ) {
		return;
	}

	$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'welcome';

	if ( $active_tab !== 'welcome' ) {
		return;
	}
	?>

	<div class="notice is-dismissible coil-welcome-notice">
		<div class="coil-welcome-notice__content">
			<h3><?php esc_html_e( 'Welcome to Coil Web Monetization for WordPress', 'coil-web-monetization' ); ?></h3>
			<?php
			printf(
				'<p>%s (<a href="%s" target="%s" >%s</a>)</p>',
				esc_html__( 'Before you begin, add your payment pointer.', 'coil-web-monetization' ),
				esc_url( 'https://webmonetization.org/docs/ilp-wallets' ),
				esc_attr( '_blank' ),
				esc_html__( 'Learn more', 'coil-web-monetization' )
			);
			?>
			<p>
				<?php
				echo sprintf(
					'<a class="%1$s" href="%2$s">%3$s</a>',
					'button button-primary',
					esc_url( '?page=coil_settings&tab=general_settings' ),
					esc_html__( 'Add Payment Pointer', 'coil-web-monetization' )
				);
				?>
			</p>
			<p>
				<?php
				printf(
					'<p>%s</p>',
					esc_html__( 'Please ensure the CSS selector is correct for your site\'s theme.', 'coil-web-monetization' )
				);
				?>
			</p>
			<p>
				<?php
				echo sprintf(
					'<a class="%1$s" href="%2$s">%3$s</a>',
					'button button-primary',
					esc_url( '?page=coil_settings&tab=exclusive_settings' ),
					esc_html__( 'Check CSS Selector', 'coil-web-monetization' )
				);
				?>
			</p>
		</div>
	</div>
	<?php
}

/**
 * Admin notice to ensure payment pointer has been set
 * @return void
 */
function admin_no_payment_pointer_notice() {

	// Only nag admins that can manage coil settings
	if ( ! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	if ( $screen->id !== 'toplevel_page_coil_settings' ) {
		return;
	}

	$payment_pointer_id = Admin\get_payment_pointer_setting();

	if ( $payment_pointer_id ) {
		return;
	}

	$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';

	if ( $active_tab !== 'general_settings' ) {
		return;
	}
	?>

	<div style="display: none;" class="notice coil-no-payment-pointer-notice">
		<img width="48" height="48" class="coil-no-payment-pointer-notice__icon" src="<?php echo esc_url( plugins_url( 'assets/images/web-mon-icon.svg', COIL__FILE__ ) ); ?>" alt="<?php esc_attr_e( 'Coil', 'coil-web-monetization' ); ?>" />
		<div class="coil-no-payment-pointer-notice__content">
			<h3><?php esc_html_e( 'Warning', 'coil-web-monetization' ); ?></h3>
			<p><?php esc_html_e( 'A payment pointer is required to receive payments and to hide exclusive content from visitors who don\'t have an active Coil membership.', 'coil-web-monetization' ); ?></p>
		</div>
	</div>
	<?php
}

/**
 * Render the Coil submenu setting screen to display options to gate posts
 * and taxonomy content types.
 *
 * @return void
 */
function render_coil_settings_screen() : void {

	?>
	<div class="wrap coil plugin-header">
		<div class="plugin-branding">
			<svg id="coil-icn-32" float="left" width="40" height="40" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M16 32C24.8366 32 32 24.8366 32 16C32 7.16344 24.8366 0 16 0C7.16344 0 0 7.16344 0 16C0 24.8366 7.16344 32 16 32ZM22.2293 20.7672C21.8378 19.841 21.2786 19.623 20.8498 19.623C20.6964 19.623 20.5429 19.6534 20.4465 19.6725C20.4375 19.6743 20.429 19.676 20.421 19.6775C20.2663 19.7435 20.1103 19.8803 19.9176 20.0493C19.3674 20.5319 18.5178 21.277 16.5435 21.3846H16.2266C14.1759 21.3846 12.2744 20.3313 11.305 18.6423C10.8576 17.8433 10.6339 16.9534 10.6339 16.0635C10.6339 15.0283 10.9322 13.975 11.5474 13.067C12.0134 12.3587 12.9269 11.2145 14.5674 10.7242C15.3504 10.4881 16.0401 10.3973 16.6367 10.3973C18.5009 10.3973 19.3584 11.3598 19.3584 12.0681C19.3584 12.4495 19.1161 12.7582 18.65 12.8127C18.5941 12.8309 18.5568 12.8309 18.5009 12.8309C18.3331 12.8309 18.1467 12.7945 17.9976 12.7037C17.9416 12.6674 17.8671 12.6493 17.7925 12.6493C17.2146 12.6493 16.413 13.6299 16.413 14.4653C16.413 15.0828 16.8604 15.6276 18.184 15.6276C18.4049 15.6276 18.6392 15.6016 18.9094 15.5716C18.9584 15.5661 19.0086 15.5606 19.0602 15.555C20.5142 15.3552 21.7633 14.3382 22.1361 13.0125C22.192 12.849 22.248 12.5766 22.248 12.2134C22.248 11.378 21.9124 10.0886 20.2905 8.90811C19.1347 8.05455 17.8111 7.80029 16.618 7.80029C15.3877 7.80029 14.3064 8.07271 13.6912 8.27248C11.2677 9.05339 9.88822 10.4881 9.17981 11.5778C8.26635 12.9398 7.80029 14.5198 7.80029 16.0998C7.80029 17.4619 8.13585 18.8058 8.82561 20.0226C10.2983 22.6014 13.1506 24.1996 16.2266 24.1996C16.3011 24.1996 16.3804 24.195 16.4596 24.1905C16.5388 24.186 16.618 24.1814 16.6926 24.1814C18.7619 24.0725 22.3225 22.6922 22.3225 21.1667C22.3225 21.0396 22.2853 20.8943 22.2293 20.7672Z" fill="black"/>
			</svg>
			<h3 class="plugin-branding"><?php _e( 'Coil Web Monetization', 'coil-web-monetization' ); ?></h3>
		</div>
		<?php $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'welcome'; ?>
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo esc_url( '?page=coil_settings&tab=welcome' ); ?>" id="coil-welcome-settings" class="nav-tab <?php echo $active_tab === 'welcome' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Welcome', 'coil-web-monetization' ); ?></a>
			<a href="<?php echo esc_url( '?page=coil_settings&tab=general_settings' ); ?>" id="coil-general-settings" class="nav-tab <?php echo $active_tab === 'general_settings' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'General Settings', 'coil-web-monetization' ); ?></a>
			<a href="<?php echo esc_url( '?page=coil_settings&tab=exclusive_settings' ); ?>" id="coil-exclusive-settings" class="nav-tab <?php echo $active_tab === 'exclusive_settings' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Exclusive Content', 'coil-web-monetization' ); ?></a>
			<a href="<?php echo esc_url( '?page=coil_settings&tab=streaming_widget' ); ?>" id="streaming-widget-settings" class="nav-tab <?php echo $active_tab === 'streaming_widget' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Streaming Support Widget', 'coil-web-monetization' ); ?></a>
		</h2>
	</div>
	<div class="wrap coil plugin-settings">

		<?php settings_errors(); ?>

		<form action="options.php" method="post">
			<?php
			switch ( $active_tab ) {
				case 'welcome':
					echo '<div class="settings-main has-sidebar">';
					settings_fields( 'coil_welcome_settings_group' );
					do_settings_sections( 'coil_welcome_section' );
					echo '</div>';
					coil_settings_sidebar_render_callback();
					break;
				case 'general_settings':
					echo '<div class="settings-main">';
					settings_fields( 'coil_general_settings_group' );
					do_settings_sections( 'coil_payment_pointer_section' );
					do_settings_sections( 'coil_monetization_section' );
					submit_button();
					echo '</div>';
					break;
				case 'exclusive_settings':
					echo '<div class="settings-main">';
					settings_fields( 'coil_exclusive_settings_group' );
					do_settings_sections( 'coil_enable_exclusive_section' );
					do_settings_sections( 'coil_css_selector_section' );
					do_settings_sections( 'coil_post_visibility_section' );
					do_settings_sections( 'coil_excerpt_display_section' );
					do_settings_sections( 'coil_paywall_section' );
					do_settings_sections( 'coil_exclusive_icon_section' );
					submit_button();
					echo '</div>';
					break;
				case 'streaming_widget':
					echo '<div class="settings-main">';
					settings_fields( 'streaming_widget_settings_group' );
					do_settings_sections( 'coil_enable_streaming_widget_section' );
					do_settings_sections( 'streaming_widget_visibility_section' );
					do_settings_sections( 'streaming_widget_settings_section' );
					submit_button();
					echo '</div>';
					break;
			}
			?>
		</form>
	</div>
	<?php
}

/**
 * Add Coil status controls to the "Add Term" screen.
 *
 * @param WP_Term_Object $term
 * @return void
 */
function coil_add_term_custom_meta( $term ) {

	if ( ! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ) {
		return;
	}

	// Get monetization and visibility options.
	$monetization_options = [
		'default'       => 'Default',
		'monetized'     => 'Enabled',
		'not-monetized' => 'Disabled',
	];
	$visibility_options   = [
		'public'    => 'Everyone',
		'exclusive' => 'Coil Members Only',
	];

	// Set the default monetization and visibility statuses.
	$term_monetization = 'default';
	$term_visibility   = Admin\get_visibility_default();

	?>
	<div id="coil_dropdown">
		<label for="_coil_monetization_term_status"><?php esc_html_e( 'Select a Web Monetization status', 'coil-web-monetization' ); ?></label>

		<select name="_coil_monetization_term_status" id="monetization_dropdown" onchange="javascript: handleRadioOptionsDisplay('<?php echo esc_attr( $term_visibility ); ?>')">
			<?php
			foreach ( $monetization_options as $setting_key => $setting_value ) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $setting_key ),
					selected( $setting_key, $term_monetization ),
					$setting_key === 'default' ? esc_html__( 'Default', 'coil-web-monetization' ) : $setting_value
				);
			}
			?>
		</select>
	</div><br>

	<?php
	// Use the output buffer to set the content for the visibility settings
	ob_start();
	foreach ( $visibility_options as $setting_key => $setting_value ) :
		?>
		<label for="<?php echo esc_attr( $setting_key ); ?>">
			<?php
			printf(
				'<input type="radio" name="%s" id="%s" value="%s"%s />%s',
				esc_attr( '_coil_visibility_term_status' ),
				esc_attr( $setting_key ),
				esc_attr( $setting_key ),
				! empty( $term_visibility ) ? checked( $setting_key, $term_visibility, false ) : '',
				esc_attr( $setting_value )
			);
			?>
		</label>
		<?php
	endforeach;
	$visibility_options = ob_get_contents();
	ob_end_clean();
	?>

	<div id="coil-radio-selection" style="display: none">
		<tr class="form-field">
			<th scope="row">
				<label><?php esc_html_e( 'Who can access this content?', 'coil-web-monetization' ); ?></label>
			</th>
			<td>
				<fieldset id="coil-visibility-settings">
					<?php echo $visibility_options; ?>
				</fieldset>
			</td>
		</tr>
	</div>
	<br>

	<script>
		/**
		 *
		 * Ensures the appropriate visibility radio button is selected.
		 * @param {String} The visibility status slug
		 * @return {void}
		 */
		function handleRadioOptionsDisplay( element ) {
			var radioButtons = document.getElementById("coil-radio-selection");
			if (document.getElementById("monetization_dropdown").value === 'monetized') {
				// If monetization is enabled then the visibility options should appear
				radioButtons.removeAttribute("style");
				// Checks the button associated with the default visibility value rather than just the last button that had been selected.
				if (element !== '' ) {
					document.getElementById(element).checked = true;
				}
			} else {
				// If monetization is not enabled then the visibility options should disappear
				radioButtons.setAttribute("style", "display: none" );
			}
		}

		// For the edit screen this function is called so that the radio buttons are hidden or displayed based on the existing settings.
		handleRadioOptionsDisplay('');

	</script>

	<?php
	wp_nonce_field( 'coil_term_gating_nonce_action', 'term_gating_nonce' );
}

/**
 * Add Coil status controls to the "Edit Term" screen.
 *
 * @param WP_Term_Object $term
 * @return void
 */
function coil_edit_term_custom_meta( $term ) {

	if ( ! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ) {
		return;
	}

	// Get monetization and visibility options.
	$monetization_options = [
		'default'       => esc_html__( 'Default', 'coil-web-monetization' ),
		'monetized'     => esc_html__( 'Enabled', 'coil-web-monetization' ),
		'not-monetized' => esc_html__( 'Disabled', 'coil-web-monetization' ),
	];
	$visibility_options   = [
		'public'    => esc_html__( 'Everyone', 'coil-web-monetization' ),
		'exclusive' => esc_html__( 'Coil Members Only', 'coil-web-monetization' ),
	];

	// Retrieve the monetization and visibility meta saved on the term.
	// If these meta fields are empty they return 'default'.
	$term_monetization = Gating\get_term_status( $term->term_id, '_coil_monetization_term_status' );
	$term_visibility   = Gating\get_term_status( $term->term_id, '_coil_visibility_term_status' );
	// There is no 'default' button for visibility so if it is set to default then select the option that it is defaulting to in the exclusive settings group.
	if ( $term_visibility === 'default' ) {
		$term_visibility = Admin\get_visibility_default();
	}

	?>
	<tr class="form-field">
		<th>
			<?php esc_html_e( 'Select a Web Monetization status', 'coil-web-monetization' ); ?>
		</th>
		<td id="coil_dropdown">

			<select name="_coil_monetization_term_status" id="monetization_dropdown" onchange="javascript: handleRadioOptionsDisplay('<?php echo esc_attr( $term_visibility ); ?>')">
				<?php
				foreach ( $monetization_options as $setting_key => $setting_value ) {
					printf(
						'<option value="%s"%s>%s</option>',
						esc_attr( $setting_key ),
						selected( $setting_key, $term_monetization ),
						$setting_key === 'default' ? esc_html__( 'Default', 'coil-web-monetization' ) : $setting_value
					);
				}
				?>
			</select>
			<br>
		</td>
	</tr>

	<?php
	// Use the output buffer to set the content for the visibility settings
	ob_start();
	foreach ( $visibility_options as $setting_key => $setting_value ) :
		?>
		<label for="<?php echo esc_attr( $setting_key ); ?>">
			<?php
			printf(
				'<input type="radio" name="%s" id="%s" value="%s"%s />%s',
				esc_attr( '_coil_visibility_term_status' ),
				esc_attr( $setting_key ),
				esc_attr( $setting_key ),
				! empty( $term_visibility ) ? checked( $setting_key, $term_visibility, false ) : '',
				esc_attr( $setting_value )
			);
			?>
		</label><br>
		<?php
	endforeach;
	$visibility_options = ob_get_contents();
	ob_end_clean();
	?>

	<tr class="form-field" id="coil-radio-selection" style="display: none">

		<th scope="row">
			<label><?php esc_html_e( 'Who can access this content?', 'coil-web-monetization' ); ?></label>
		</th>
		<td>
			<fieldset id="coil-visibility-settings">
				<?php echo $visibility_options; ?>
			</fieldset>
		</td>
	</tr>

	<script>
		/**
		 *
		 * Ensures the appropriate visibility radio button is selected.
		 * @param {String} The visibility status slug
		 * @return {void}
		 */
		function handleRadioOptionsDisplay( element ) {
			var radioButtons = document.getElementById("coil-radio-selection");
			if (document.getElementById("monetization_dropdown").value === 'monetized') {
				// If monetization is enabled then the visibility options should appear
				radioButtons.removeAttribute("style");
				// Checks the button associated with the default visibility value rather than just the last button that had been selected.
				if (element !== '' ) {
					document.getElementById(element).checked = true;
				}
			} else {
				// If monetization is not enabled then the visibility options should disappear
				radioButtons.setAttribute("style", "display: none" );
			}
		}

		// For the edit screen this function is called so that the radio buttons are hidden or displayed based on the existing settings.
		handleRadioOptionsDisplay('');

	</script>

	<?php
	wp_nonce_field( 'coil_term_gating_nonce_action', 'term_gating_nonce' );
}
