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

	// // ==== Enable / Disable
	// add_settings_section(
	// 	'coil_enable_exclusive_section',
	// 	false,
	// 	__NAMESPACE__ . '\coil_settings_enable_exclusive_toggel_render_callback',
	// 	'coil_enable_exclusive_section'
	// );

	// ==== Paywall Appearance
	add_settings_section(
		'coil_paywall_settings',
		false,
		__NAMESPACE__ . '\coil_settings_paywall_render_callback',
		'coil_paywall_section'
	);

	// ==== Exclusive Post Appearance
	add_settings_section(
		'coil_exclusive_post_section',
		false,
		__NAMESPACE__ . '\coil_settings_exclusive_post_render_callback',
		'coil_exclusive_post_section'
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

	// ==== CSS Selectors
	add_settings_section(
		'coil_css_selector_section',
		false,
		__NAMESPACE__ . '\coil_settings_css_selector_render_callback',
		'coil_css_selector_section'
	);

	// Tab 4 - Coil Button
	register_setting(
		'coil_button_settings_group',
		'coil_button_settings_group',
		__NAMESPACE__ . '\coil_button_settings_group_validation'
	);

	// // ==== Enable / Disable
	// add_settings_section(
	// 	'coil_enable_button_section',
	// 	false,
	// 	__NAMESPACE__ . '\coil_settings_enable_coil_button_toggel_render_callback',
	// 	'coil_enable_button_section'
	// );

	// ==== Button Settings
	add_settings_section(
		'coil_promotion_bar_section',
		false,
		__NAMESPACE__ . '\coil_settings_promotion_bar_render_callback',
		'coil_promotion_bar_section'
	);

	// // ==== Button Settings
	// add_settings_section(
	// 	'coil_button_section',
	// 	false,
	// 	__NAMESPACE__ . '\coil_settings_coil_button_settings_render_callback',
	// 	'coil_button_section'
	// );

	// // ==== Button Visibility
	// add_settings_section(
	// 	'coil_button_visibility_section',
	// 	false,
	// 	__NAMESPACE__ . '\coil_settings_coil_button_visibility_render_callback',
	// 	'coil_button_visibility_section'
	// );
}

/* ------------------------------------------------------------------------ *
 * Section Validation
 * ------------------------------------------------------------------------ */

/**
 * Allow the radio button options,
 * that set the global monetization defaults,
 * to be properly validated
 *
 * @param array $general_settings The posted radio options from the General Settings section
 * @return array
 */
function coil_general_settings_group_validation( $general_settings ) : array {

	if ( ! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ) {
		return [];
	}

	$post_monetization_default = Admin\get_monetization_default();
	// A list of valid monetization types (monetized or not-monetized)
	$valid_options = array_keys( Admin\get_monetization_types() );
	// Retrieves the exclusive settings to get the post type visibility defaults
	$exclusive_settings = Admin\get_exclusive_settings();

	foreach ( $general_settings as $id => $value ) {

		if ( $id === 'coil_payment_pointer' ) {
			$general_settings[ $id ] = sanitize_text_field( $general_settings[ $id ] );
		} else {
			$post_type = $id;
			// The default value is monetized
			$general_settings[ $post_type ] = in_array( $value, $valid_options, true ) ? sanitize_key( $value ) : $post_monetization_default;
			// Ensures that a post cannot default to be Not Monetized and Exclusive
			// Changing the array key suffix so that it becomes a visibility setting key instead of a monetization setting key
			$visibility_setting_key = str_replace( '_monetization', '_visibility', $post_type );
			if ( $general_settings[ $post_type ] === 'not-monetized' && $exclusive_settings[ $visibility_setting_key ] === 'exclusive' ) {
				$exclusive_settings [ $visibility_setting_key ] = 'public';
				update_option( 'coil_exclusive_settings_group', $exclusive_settings );
			}
		}
	}

	return $general_settings;
}

/**
 * Validates the post type default visibility settings.
 * Validates text inputs (the paywall title, message, button text and link and the CSS selector).
 * Validates the excerpt visibility setings per post type.
 *
 * @param array $exclusive_settings The posted text input fields.
 * @return array
 */
function coil_exclusive_settings_group_validation( $exclusive_settings ) : array {

	if ( ! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ) {
		return [];
	}

	// Defaults if setting fields are empty
	$post_monetization_default = Admin\get_monetization_default();
	$paywall_defaults          = Admin\get_paywall_appearance_defaults();
	$post_visibility_default   = Admin\get_post_visibility_default();

	// Monetization defaults are needed to check that the 'exclusive' and 'not-monetized' defaults are never set on a post type
	$post_monetization_settings = Admin\get_general_settings();
	// Valid visibility options are public or exclusive
	$valid_visibility_options = array_keys( Admin\get_visibility_types() );
	// A list of valid post types
	$post_type_options = Coil\get_supported_post_types( 'objects' );

	// Loops through each post type to validate post visibility defaults and excerpt display settings
	foreach ( $post_type_options as $post_type ) {

		// Validates default post visibility settings
		// Sets the keys for the post visibility and post monetization settings
		$visibility_setting_key   = $post_type->name . '_visibility';
		$monetization_setting_key = $post_type->name . '_monetization';
		// The default monetization setting is monetized
		$monetization_setting = ! empty( $post_monetization_settings[ $monetization_setting_key ] ) ? $post_monetization_settings[ $monetization_setting_key ] : $post_monetization_default;
		// The default value is public
		$visibility_setting = ! empty( $exclusive_settings[ $visibility_setting_key ] ) && in_array( $exclusive_settings[ $visibility_setting_key ], $valid_visibility_options, true ) ? sanitize_key( $exclusive_settings[ $visibility_setting_key ] ) : $post_visibility_default;

		// Ensures that a post cannot default to be 'not-monetized' and 'exclusive'
		if ( $visibility_setting === 'exclusive' && $monetization_setting === 'not-monetized' ) {
			$post_monetization_settings[ $monetization_setting_key ] = 'monetized';
			update_option( 'coil_general_settings_group', $post_monetization_settings );
		}

		$exclusive_settings[ $visibility_setting_key ] = $visibility_setting;

		// Validates excerpt display settings.
		$excerpt_setting_key                        = $post_type->name . '_excerpt';
		$excerpt_setting                            = isset( $exclusive_settings[ $excerpt_setting_key ] ) ? true : false;
		$exclusive_settings[ $excerpt_setting_key ] = $excerpt_setting;

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
			$exclusive_settings[ $field_name ] = ( isset( $exclusive_settings[ $field_name ] ) ) ? esc_url_raw( $exclusive_settings[ $field_name ] ) : '';
		} else {
			// If no CSS selector is set then the default value must be used
			if ( $field_name === 'coil_content_container' && $exclusive_settings[ $field_name ] === '' ) {
				$exclusive_settings[ $field_name ] = '.content-area .entry-content';
			} else {
				$exclusive_settings[ $field_name ] = ( isset( $exclusive_settings[ $field_name ] ) ) ? sanitize_text_field( $exclusive_settings[ $field_name ] ) : '';
			}
		}
	}

	// Theme validation
	$valid_color_choices  = Admin\get_theme_color_types();
	$coil_theme_color_key = 'coil_message_color_theme';

	$exclusive_settings[ $coil_theme_color_key ] = isset( $exclusive_settings[ $coil_theme_color_key ] ) && in_array( $exclusive_settings[ $coil_theme_color_key ], $valid_color_choices, true ) ? sanitize_key( $exclusive_settings[ $coil_theme_color_key ] ) : $paywall_defaults[ $coil_theme_color_key ];

	// Branding validation
	$valid_branding_choices = Admin\get_paywall_branding_options();
	$message_branding_key   = 'coil_message_branding';

	$exclusive_settings[ $message_branding_key ] = isset( $exclusive_settings[ $message_branding_key ] ) && in_array( $exclusive_settings[ $message_branding_key ], $valid_branding_choices, true ) ? sanitize_key( $exclusive_settings[ $message_branding_key ] ) : $paywall_defaults[ $message_branding_key ];

	// Validates all checkbox input fields
	$checkbox_fields = [
		'coil_message_font',
		'coil_title_padlock',
	];

	foreach ( $checkbox_fields as $field_name ) {
		$exclusive_settings[ $field_name ] = isset( $exclusive_settings[ $field_name ] ) ? true : false;
	}
	return $exclusive_settings;
}

/**
 * Validates the checkbox that cntrols the display of the Promotion Bar.
 *
 * @param array $coil_button_settings The checkbox input field.
 * @return array
 */
function coil_button_settings_group_validation( $coil_button_settings ): array {
	$checkbox_fields = [ 'coil_show_promotion_bar' ];

	foreach ( $checkbox_fields as $field_name ) {
		$coil_button_settings[ $field_name ] = isset( $coil_button_settings[ $field_name ] ) ? true : false;
	}
	return $coil_button_settings;
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
	<div class="coil tab-styling">
		<?php

			printf(
				'<h1>%1$s</h1>',
				esc_html__( 'About the Coil Plugin', 'coil-web-monetization' )
			);
		?>

		<div style="padding-top: 10px;">
		<?php

			echo '<h2>' . esc_html__( 'Monetization', 'coil-web-monetization' ) . '</h2>';

			echo '<p>' . esc_html__( 'The Coil WordPress Plugin lets you enable Web Monetization on your website. With Web Monetization, you automatically receive streaming payments whenever Coil Members visit your site.', 'coil-web-monetization' ) . '</p>';
		?>
		</div>

		<div style="padding-top: 10px;">
		<?php
			echo '<h2>' . esc_html__( 'Exclusive Content', 'coil-web-monetization' ) . '</h2>';

			echo '<p>' . esc_html__( 'Offer exclusive content to Coil Members as a perk for them supporting you.', 'coil-web-monetization' ) . '</p>';
			printf(
				'<a class="button button-primary" href="%s">%s</a>',
				esc_url( admin_url( 'admin.php?page=coil_settings&tab=exclusive_settings', COIL__FILE__ ) ),
				esc_html__( 'Enable Exclusive Content', 'coil-web-monetization' )
			);
		?>
		</div>

		<div style="padding-top: 10px;">
		<?php
			echo '<h2>' . esc_html__( 'Coil Button', 'coil-web-monetization' ) . '</h2>';

			echo '<p>' . esc_html__( 'Show that you accept support from Coil Members by displaying a Coil button on your page.', 'coil-web-monetization' ) . '</p>';
			printf(
				'<a class="button button-primary" href="%s">%s</a>',
				esc_url( admin_url( 'admin.php?page=coil_settings&tab=coil_button', COIL__FILE__ ) ),
				esc_html__( 'Add Coil Button', 'coil-web-monetization' )
			);
		?>
		</div>
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
	<div class="coil settings-sidebar">
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
					esc_url( 'https://help.coil.com/docs/monetize/content/wp-faq-troubleshooting' ),
					esc_html__( 'FAQs and Troubleshooting', 'coil-web-monetization' )
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
	<?php
}

/**
 * Renders the output of the payment pointer input field.
 *
 * @return void
 */
function coil_settings_payment_pointer_render_callback() {
	?>
	<div class="coil tab-styling">
	<?php
		printf(
			'<h1>%1$s</h1>',
			esc_html__( 'Payment Pointer', 'coil-web-monetization' )
		);

		echo '<p>' . esc_html__( 'Enter your digital wallet\'s payment pointer to receive payments', 'coil-web-monetization' ) . '</p>';
		printf(
			'<input class="%s" type="%s" name="%s" id="%s" value="%s" placeholder="%s" />',
			esc_attr( 'wide-input' ),
			esc_attr( 'text' ),
			esc_attr( 'coil_general_settings_group[coil_payment_pointer]' ),
			esc_attr( 'coil_payment_pointer' ),
			esc_attr( Admin\get_payment_pointer_setting( 'coil_payment_pointer' ) ),
			esc_attr( '$wallet.example.com/alice' )
		);

		printf(
			'<p class="%s">%s<a href="%s" target="%s" >%s</a></p>',
			esc_attr( 'description' ),
			esc_html__( 'Don\'t have a digital wallet or know your payment pointer? ', 'coil-web-monetization' ),
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
	<div class="coil tab-styling">
	<?php
		echo '<h1>' . esc_html__( 'Monetization Settings', 'coil-web-monetization' ) . '</h1>';

		echo '<p>' . esc_html_e( 'Manage monetization for specific post types', 'coil-web-monetization' ) . '</p>';

		// Using a function to generate the table with the global monetization radio button options.
		$group                = 'coil_general_settings_group';
		$columns              = Admin\get_monetization_types();
		$input_type           = 'radio';
		$suffix               = 'monetization';
		$monetization_options = Admin\get_general_settings();
		render_generic_post_type_table( $group, $columns, $input_type, $suffix, $monetization_options );

		printf(
			'<p class="%s">%s</p>',
			esc_attr( 'description' ),
			esc_html__( 'You can override these settings in the Category, Tag, Page and Post menus.', 'coil-web-monetization' )
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
	<div class="coil tab-styling">
	<?php
		echo '<h1>' . esc_html__( 'Paywall Appearance', 'coil-web-monetization' ) . '</h1>';
		echo '<p>' . esc_html_e( 'This paywall replaces the post content for users without an active Coil Membership, when access is set to exclusive.', 'coil-web-monetization' ) . '</p>';

		// Renders the textfield for each paywall text field input.
		$text_fields = [ 'coil_paywall_title', 'coil_paywall_message', 'coil_paywall_button_text', 'coil_paywall_button_link' ];
	foreach ( $text_fields as $field_name ) {
		coil_paywall_appearance_text_field_settings_render_callback( $field_name );
	}

		// Renders the color theme radio buttons
		echo '<br><h3>' . esc_html__( 'Color Theme', 'coil-web-monetization' ) . '</h3>';
		paywall_theme_render_callback();

		// Renders the branding selection box
		echo '<br><h3>' . esc_html__( 'Branding', 'coil-web-monetization' ) . '</h3>';
		printf(
			'<select name="%s" id="%s">',
			esc_attr( 'coil_exclusive_settings_group[coil_message_branding]' ),
			esc_attr( 'coil_branding' )
		);

		// Defaults to the Coil logo
		$branding_selected_input = Admin\get_paywall_appearance_setting( 'coil_message_branding' );

		paywall_branding_render_callback();

		echo '</select><br>';
	?>

	<script type="text/javascript">
		document.getElementById('coil_branding').value = "<?php echo $branding_selected_input; ?>";
	</script>
	<?php
		// Renders the font checkbox
		echo '<br><h3>' . esc_html__( 'Font Style', 'coil-web-monetization' ) . '</h3>';
		paywall_font_render_callback();

	?>
	</div>
	<?php
}

/**
 * Renders the output of the paywall theme radio button settings.
 *
 * @return void
 */
function paywall_theme_render_callback() {
	// The default color theme is the light theme.
	$theme_color_checked_input = 'checked="true"';

	printf(
		'<input type="radio" name="%s" id="%s" value="%s" %s />',
		esc_attr( 'coil_exclusive_settings_group[coil_message_color_theme]' ),
		esc_attr( 'light_color_theme' ),
		esc_attr( 'light' ),
		$theme_color_checked_input
	);

	printf(
		'<label for="%s">%s</label>',
		esc_attr( 'light_color_theme' ),
		esc_html_e( 'Light theme', 'coil-web-monetization' )
	);

	echo '<br>';

	$theme_color = Admin\get_paywall_appearance_setting( 'coil_message_color_theme' );

	if ( ! empty( $theme_color ) && $theme_color === 'dark' ) {
		$theme_color_checked_input = 'checked="true"';
	} else {
		$theme_color_checked_input = false;
	}

	printf(
		'<input type="radio" name="%s" id="%s" value="%s" %s />',
		esc_attr( 'coil_exclusive_settings_group[coil_message_color_theme]' ),
		esc_attr( 'dark_color_theme' ),
		esc_attr( 'dark' ),
		$theme_color_checked_input
	);

	printf(
		'<label for="%s">%s</label><br>',
		esc_attr( 'dark_color_theme' ),
		esc_html_e( 'Dark theme', 'coil-web-monetization' )
	);
}

/**
 * Renders the output of the branding selection box settings.
 *
 * @return void
 */
function paywall_branding_render_callback() {
	printf(
		'<option value="%s">%s</option>',
		esc_attr( 'coil_logo' ),
		esc_attr( 'Show Coil logo' )
	);

	printf(
		'<option value="%s">%s</option>',
		esc_attr( 'site_logo' ),
		esc_attr( 'Show website logo' )
	);

	printf(
		'<option value="%s">%s</option>',
		esc_attr( 'no_logo' ),
		esc_attr( 'Show no logo' )
	);
}

/**
 * Renders the output of the font option checkbox
 * The default is unchecked
 * @return void
 */
function paywall_font_render_callback() {

	$font_id = 'coil_message_font';
	$value   = Admin\get_inherited_font_setting( $font_id );

	if ( $value === true ) {
		$checked_input = 'checked="checked"';
	} else {
		$checked_input = false;
		$value         = false;
	}

	printf(
		'<input type="%s" name="%s" id="%s" value=%b %s>',
		esc_attr( 'checkbox' ),
		esc_attr( 'coil_exclusive_settings_group[' . $font_id . ']' ),
		esc_attr( $font_id ),
		esc_attr( $value ),
		$checked_input
	);

	printf(
		'<label for="%s">%s</label>',
		esc_attr( $font_id ),
		esc_html_e( 'Use theme font styles', 'coil-web-monetization' )
	);
}

/**
 * Renders the output of the exclusive post appearance settings.
 * This includes choosing whether to tdisplay the padloc, where to display it and which icon to use.
 *
 * @return void
 */
function coil_settings_exclusive_post_render_callback() {

	?>
	<div class="coil tab-styling">
	<?php
		echo '<h1>' . esc_html__( 'Exclusive Post Appearance', 'coil-web-monetization' ) . '</h1>';
		echo '<p>' . esc_html_e( 'Customize the appearance for exclusive posts on archive pages.', 'coil-web-monetization' ) . '</p>';

		// Renders the padlock display checkbox
		echo '<br>';
		coil_padlock_display_checkbox_render_callback();
	?>
	</div>
	<?php
}

/**
 * Renders the output of the display title padlock checkbox
 * @return void
 */
function coil_padlock_display_checkbox_render_callback() {

	/**
	 * Specify the default checked state for the input from
	 * any settings stored in the database. If the
	 * input status is not set, default to checked.
	 */

	$padlock_id = 'coil_title_padlock';
	$value      = Admin\get_exlusive_post_setting( $padlock_id );

	if ( $value === true ) {
		$checked_input = 'checked="checked"';
	} else {
		$checked_input = false;
		$value         = false;
	}

	printf(
		'<input type="%s" name="%s" id="%s" value=%b %s>',
		esc_attr( 'checkbox' ),
		esc_attr( 'coil_exclusive_settings_group[' . $padlock_id . ']' ),
		esc_attr( $padlock_id ),
		esc_attr( $value ),
		$checked_input
	);

	printf(
		'<label for="%s">%s</label>',
		esc_attr( $padlock_id ),
		esc_html_e( 'Show padlock icon next to exclusive post titles.', 'coil-web-monetization' )
	);
}

/**
 * Renders the output of the global post type visibility default settings
 * showing radio buttons based on the post types available in WordPress.
 * @return void
 */
function coil_settings_post_visibility_render_callback() {
	?>
	<div class="coil tab-styling">
	<?php
		echo '<h1>' . esc_html__( 'Visibility Settings', 'coil-web-monetization' ) . '</h1>';
		echo '<p>' . esc_html_e( 'Select whether you want to designate posts and pages as \'Exclusive\' by default', 'coil-web-monetization' ) . '</p>';
		printf(
			'<p>%1$s<a href="%2$s">%3$s</a>%4$s</p>',
			esc_html( 'Post types can only be marked as exclusive if they are also marked as monetized under ', 'coil-web-monetization' ),
			esc_url( admin_url( 'admin.php?page=coil_settings&tab=general_settings', COIL__FILE__ ) ),
			esc_html( 'General Settings', 'coil-web-monetization' ),
			'.'
		);

		// Using a function to generate the table with the global visibility radio button options.
		$group             = 'coil_exclusive_settings_group';
		$columns           = Admin\get_visibility_types();
		$input_type        = 'radio';
		$suffix            = 'visibility';
		$exclusive_options = Admin\get_exclusive_settings();
		render_generic_post_type_table( $group, $columns, $input_type, $suffix, $exclusive_options );

		printf(
			'<p class="%s">%s</p>',
			esc_attr( 'description' ),
			esc_html__( 'You can override these settings in the Category, Tag, Page and Post menus.', 'coil-web-monetization' )
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
	<div class="coil tab-styling">
	<?php
		echo '<h1>' . esc_html__( 'Excerpt Settings', 'coil-web-monetization' ) . '</h1>';
		echo '<p>' . esc_html_e( 'Use the settings below to select whether to show a short excerpt for any pages, posts, or other content types you choose to gate access to. Support for displaying an excerpt may depend on your particular theme and setup of WordPress.', 'coil-web-monetization' ) . '</p>';

		// Using a function to generate the table with the post type excerpt checkboxes.
		$group             = 'coil_exclusive_settings_group';
		$columns           = [ 'Display Excerpt' ];
		$input_type        = 'checkbox';
		$suffix            = 'excerpt';
		$exclusive_options = Admin\get_exclusive_settings();
		render_generic_post_type_table( $group, $columns, $input_type, $suffix, $exclusive_options );
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
	<div class="coil tab-styling">
	<?php
		echo '<h1>' . esc_html__( 'CSS Selector', 'coil-web-monetization' ) . '</h1>';

		$exclusive_settings = Admin\get_exclusive_settings();

		printf(
			'<input class="%s" type="%s" name="%s" id="%s" value="%s" placeholder="%s" required="required"/>',
			esc_attr( 'wide-input' ),
			esc_attr( 'text' ),
			esc_attr( 'coil_exclusive_settings_group[coil_content_container]' ),
			esc_attr( 'coil_content_container' ),
			esc_attr( $exclusive_settings['coil_content_container'] ),
			esc_attr( '.content-area .entry-content' )
		);

		echo '<p class="description">';

		printf(
			/* translators: 1) HTML link open tag, 2) HTML link close tag, 3) HTML link open tag, 4) HTML link close tag. */
			esc_html__( 'Enter the CSS selectors set by your theme that could include gated content. Most themes use the pre-filled CSS selectors. (%1$sLearn more%2$s)', 'coil-web-monetization' ),
			sprintf( '<a href="%s" target="_blank">', esc_url( 'https://help.coil.com/docs/monetize/content/wp-faq-troubleshooting#everyoneno-one-can-see-my-monetized-content-why' ) ),
			'</a>'
		);

		echo '</p>';
	?>
	</div>
	<?php
}

/**
 * Renders the output of the content messaging customization setting
 * @return void
 */
function coil_paywall_appearance_text_field_settings_render_callback( $field_name ) {

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

	if ( '' !== $heading ) {
		?>
		<h3><?php echo esc_html( $heading ); ?></h3>
		<?php
	}

	// Print <textarea> containing the setting value
	printf(
		'<textarea class="%s" name="%s" id="%s" placeholder="%s">%s</textarea>',
		esc_attr( 'wide-input' ),
		esc_attr( 'coil_exclusive_settings_group[' . $field_name . ']' ),
		esc_attr( $field_name ),
		esc_attr( Admin\get_paywall_appearance_setting( $field_name, true ) ),
		esc_attr( Admin\get_paywall_appearance_setting( $field_name ) )
	);

	if ( $field_name === 'coil_paywall_button_link' ) {
		echo '<p class="description">' . __( 'If you have an affiliate link add it here.', 'coil-web-monetization' ) . '</p>';
	}
}

/**
 * Renders the output of the show Coil Promotion Bar footer checkbox
 * @return void
 */
function coil_settings_promotion_bar_render_callback() {

	/**
	 * Specify the default checked state on the input from
	 * any settings stored in the database. If the
	 * input status is not set, default to checked
	 */
	$checked_input_value = Admin\get_coil_button_setting( 'coil_show_promotion_bar' );

	printf(
		'<input type="%s" name="%s" id="%s" "%s">',
		esc_attr( 'checkbox' ),
		esc_attr( 'coil_button_settings_group[coil_show_promotion_bar]' ),
		esc_attr( 'coil_show_promotion_bar' ),
		checked( 1, $checked_input_value, false )
	);

	printf(
		'<label for="%s">%s</label>',
		esc_attr( 'coil_show_promotion_bar' ),
		esc_html_e( 'Show the support creator message in a footer bar on posts that are monetized and publicly visible.', 'coil-web-monetization' )
	);
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
	$notice_dismissed   = get_user_meta( $current_user->ID, 'coil-welcome-notice-dismissed', true );

	if ( $payment_pointer_id || $notice_dismissed === 'true' ) {
		return;
	}

	$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'welcome';

	if ( $active_tab !== 'welcome' ) {
		return;
	}
	?>

	<div class="notice is-dismissible coil-welcome-notice">
		<img width="48" height="48" class="coil-welcome-notice__icon" src="<?php echo esc_url( plugins_url( 'assets/images/web-mon-icon.svg', COIL__FILE__ ) ); ?>" alt="<?php esc_attr_e( 'Coil', 'coil-web-monetization' ); ?>" />
		<div class="coil-welcome-notice__content">
			<h3><?php esc_html_e( 'Welcome to Coil Web Monetization for WordPress', 'coil-web-monetization' ); ?></h3>
			<p>
			<?php
			printf(
				/* translators: 1) HTML link open tag, 2) HTML link close tag */
				esc_html__( 'To start using Web Monetization please set up your %1$spayment pointer%2$s.', 'coil-web-monetization' ),
				sprintf( '<a href="%1$s">', esc_url( '?page=coil_settings&tab=general_settings' ) ),
				'</a>'
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
			<p><?php esc_html_e( 'You haven\'t entered a payment pointer. A payment pointer is required to receive payments and for content gating to be recognized.', 'coil-web-monetization' ); ?></p>
		</div>
	</div>
	<?php
}

/**
 * Sets up a table to create radio button / checkbox options for the different post types available.
 * @return void
 * @param array $column_names
 * @param string $input_type checkbox or radio.
 * @param array $value_id_suffix The suffix that goes after the post type name to create an id for it.
 */
function render_generic_post_type_table( $settings_group, $column_names, $input_type, $value_id_suffix, $current_options ) {
	$post_type_options = Coil\get_supported_post_types( 'objects' );

	// If there are post types available, output them:
	if ( ! empty( $post_type_options ) ) {
		// Get the values behind the column names
		$keys = array_keys( $column_names );

		?>
		<table class="widefat" style="border-radius: 4px;">
			<thead>
				<th><?php esc_html_e( 'Post Type', 'coil-web-monetization' ); ?></th>
				<?php foreach ( $column_names as $setting_key => $setting_value ) : ?>
					<th class="posts_table_header">
						<?php echo esc_html( $setting_value ); ?>
					</th>
				<?php endforeach; ?>
			</thead>
			<tbody>
				<?php foreach ( $post_type_options as $post_type ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $post_type->label ); ?></th>
						<?php
						foreach ( $column_names as $setting_key => $setting_value ) :
							if ( $input_type === 'checkbox' ) {
								$input_id = $post_type->name . '_' . $value_id_suffix;
							} else {
								$input_id = $post_type->name . '_' . $value_id_suffix . '_' . $setting_key;
							}
							$input_name = $settings_group . '[' . $post_type->name . '_' . $value_id_suffix . ']';

							/**
							 * The default checked state is the first option on the input from.
							 */
							$checked_input = false;
							if ( $input_type === 'radio' && $setting_key === $keys[0] ) {
								$checked_input = 'checked="true"';
							} elseif ( $input_type === 'radio' && isset( $current_options[ $post_type->name . '_' . $value_id_suffix ] ) ) {
								$checked_input = checked( $setting_key, $current_options[ $post_type->name . '_' . $value_id_suffix ], false );
							} elseif ( $input_type === 'checkbox' ) {
								if ( isset( $current_options[ $post_type->name . '_' . $value_id_suffix ] ) && $current_options[ $post_type->name . '_' . $value_id_suffix ] === true ) {
									$checked_input = 'checked="true"';
									$setting_key   = true;
								} else {
									$checked_input = false;
									$setting_key   = false;
								}
							}
							?>
							<td>
								<?php
								printf(
									'<input type="%s" name="%s" id="%s" value="%s"%s />',
									esc_attr( $input_type ),
									esc_attr( $input_name ),
									esc_attr( $input_id ),
									esc_attr( $setting_key ),
									$checked_input
								);

								?>
							</td>
							<?php
						endforeach;
						?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
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
			<a href="<?php echo esc_url( '?page=coil_settings&tab=coil_button' ); ?>" id="coil-button-settings" class="nav-tab <?php echo $active_tab === 'coil_button' ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Coil Button', 'coil-web-monetization' ); ?></a>

		</h2>
	</div>
	<div class="wrap coil plugin-settings">

		<?php settings_errors(); ?>

		<form action="options.php" method="post">
			<?php
			switch ( $active_tab ) {
				case 'welcome':
					coil_settings_sidebar_render_callback();
					settings_fields( 'coil_welcome_settings_group' );
					do_settings_sections( 'coil_welcome_section' );
					break;
				case 'general_settings':
					settings_fields( 'coil_general_settings_group' );
					do_settings_sections( 'coil_payment_pointer_section' );
					do_settings_sections( 'coil_monetization_section' );
					submit_button();
					break;
				case 'exclusive_settings':
					settings_fields( 'coil_exclusive_settings_group' );
					// do_settings_sections( 'coil_enable_exclusive_section' );
					do_settings_sections( 'coil_paywall_section' );
					do_settings_sections( 'coil_exclusive_post_section' );
					do_settings_sections( 'coil_post_visibility_section' );
					do_settings_sections( 'coil_excerpt_display_section' );
					do_settings_sections( 'coil_css_selector_section' );
					submit_button();
					break;
				case 'coil_button':
					settings_fields( 'coil_button_settings_group' );
					do_settings_sections( 'coil_promotion_bar_section' );
					// 	do_settings_sections( 'coil_enable_button_section' );
					// 	do_settings_sections( 'coil_button_section' );
					// 	do_settings_sections( 'coil_button_visibility_section' );
					submit_button();
					break;
			}
			?>
		</form>
	</div>
	<?php
}

function coil_add_term_custom_meta( $term ) {
	coil_term_custom_meta( 'add', $term );
}

function coil_edit_term_custom_meta( $term ) {
	coil_term_custom_meta( 'edit', $term );
}

/**
 * Add monetization and visibility controls to the "Add Term" and "Edit Term" screens.
 * The functions differ slightly in structure due to html requirements of the different screens.
 *
 * @param String $action {'add' | 'edit'}
 * @param WP_Term_Object $term
 * @return void
 */
function coil_term_custom_meta( $action, $term ) {

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

	if ( ! current_user_can( apply_filters( 'coil_settings_capability', 'manage_options' ) ) ) {
		return;
	}

	// Retrieve the post's default gating
	$general_settings     = Admin\get_general_settings();
	$default_monetization = isset( $general_settings['post_monetization'] ) ? $general_settings['post_monetization'] : 'monetized';
	$exclusive_settings   = Admin\get_exclusive_settings();
	$default_visibility   = isset( $exclusive_settings['post_visibility'] ) ? $exclusive_settings['post_visibility'] : 'public';
	if ( $default_monetization === 'not-monetized' ) {
		$default_value = 'Disabled';
	} elseif ( $default_visibility === 'exclusive' ) {
		$default_value = 'Enabled & exclusive';
	} else {
		$default_value = 'Enabled & public';
	}

	// Retrieve the monetization and visibility meta saved on the term.
	// If these meta fields are empty they return 'default'.
	$term_monetization = Gating\get_term_monetization( $term->term_id );
	$term_visibility   = Gating\get_term_visibility( $term->term_id );
	if ( $term_visibility === 'default' ) {
		$term_visibility = $default_visibility;
	}

	if ( $action === 'add' ) {
		?>
		<div id="coil_dropdown">
		<label for="_coil_monetization_term_status"><?php esc_html_e( 'Select a monetization status', 'coil-web-monetization' ); ?></label>
		<?php
	} else {
		?>
		<tr class="form-field">
		<th>
			<?php esc_html_e( 'Select a monetization status', 'coil-web-monetization' ); ?>
		</th>
		<td id="coil_dropdown">
		<?php
	}

	?>

	<select name="_coil_monetization_term_status" id="monetization_dropdown">
		<?php
		foreach ( $monetization_options as $setting_key => $setting_value ) {

			$selected_input = '';
			if ( $setting_key === $term_monetization ) {
				$selected_input = 'selected';
			}
			?>
				<label for="<?php echo esc_attr( $setting_key ); ?>">
				<?php
				if ( $setting_key === 'default' ) {
					$setting_value = esc_html( 'Default (', 'coil-web-monetization' ) . esc_html( $default_value, 'coil-web-monetization' ) . esc_html( ')', 'coil-web-monetization' );
				}
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $setting_key ),
					$selected_input,
					esc_attr( $setting_value )
				);
				?>
				</label><br>
			<?php
		}
		?>
	</select>
	<?php
	if ( $action === 'add' ) {
		?>
		</div><br>
		<div id="coil-radio-selection" style="display: none">
			<tr class="form-field">
		<?php
	} else {
		?>
		<br>
			</td>
		</tr>
		<tr class="form-field" id="coil-radio-selection" style="display: none">
		<?php
	}
	?>

		<th scope="row">
			<label><?php esc_html_e( 'Who can access this content?', 'coil-web-monetization' ); ?></label>
		</th>
		<td>
			<fieldset id="coil-category-settings">
			<?php
			foreach ( $visibility_options as $setting_key => $setting_value ) {

				$checked_input = false;
				if ( ! empty( $term_visibility ) ) {
					$checked_input = checked( $setting_key, $term_visibility, false );
				}
				?>
				<label for="<?php echo esc_attr( $setting_key ); ?>">
				<?php
				printf(
					'<input type="radio" name="%s" id="%s" value="%s"%s />%s',
					esc_attr( '_coil_visibility_term_status' ),
					esc_attr( $setting_key ),
					esc_attr( $setting_key ),
					$checked_input,
					esc_attr( $setting_value )
				);
				?>
				</label><br>
				<?php
			}
			?>
			</fieldset>
		</td>
	</tr>
	<?php
	if ( $action === 'add' ) {
		echo '</div>';
	}
	?>

	<script>

	function displayRadioOptions() {
		var radioButtons = document.getElementById("coil-radio-selection");
		radioButtons.removeAttribute("style");
	}

	function hideRadioOptions() {
		var radioButtons = document.getElementById("coil-radio-selection");
		radioButtons.setAttribute("style", "display: none" );
	}

	document.getElementById("monetization_dropdown").addEventListener("click", function () {
		if (document.getElementById("monetization_dropdown").value === 'monetized') {
			displayRadioOptions();
		} else {
			hideRadioOptions();
		}
	});

	</script>

	<?php
	wp_nonce_field( 'coil_term_gating_nonce_action', 'term_gating_nonce' );
}

function dismiss_welcome_notice() {

	global $current_user;

	// Bail early - no user set (somehow).
	if ( empty( $current_user ) ) {
		return;
	}

	// User meta stored as strings, so use 'true' to avoid data type issues.
	update_user_meta( $current_user->ID, 'coil-welcome-notice-dismissed', 'true' );
}
