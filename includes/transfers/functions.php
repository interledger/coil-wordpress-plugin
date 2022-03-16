<?php
declare(strict_types=1);
/**
 * Coil settings.
 * Creates and renders the Coil settings panel
 */

namespace Coil\Transfers;

use Coil;
use Coil\Admin;

/* ------------------------------------------------------------------------ *
 * Section Database setup and data migrations
 * ------------------------------------------------------------------------ */

/**
 * If certain database entries are empty this functions adds them.
 * This includes the post monetization defaults, paywall appearance settings,
 * exclusive post settings, and post and excerpt visibility settings.
 *
 * @return void
 */
function maybe_load_database_defaults() {

	// Loads monetization defaults if they have not yet been entered into the database
	$monetization_settings = get_option( 'coil_general_settings_group', 'absent' );

	if ( $monetization_settings === 'absent' ) {
		// Monetization default is 'monetized'
		$monetization_default      = Admin\get_monetization_default();
		$new_monetization_settings = [];
		$post_type_options         = Coil\get_supported_post_types( 'objects' );

		// Set monetization default for each post type
		foreach ( $post_type_options as $post_type ) {
			$new_monetization_settings[ $post_type->name . '_monetization' ] = $monetization_default;
		}
		add_option( 'coil_general_settings_group', $new_monetization_settings );
	}

	// Loads applicable exclusive setting defaults if they have not yet been entered into the database.
	// This includes paywall appearance settings, exclusive post settings, post visibility, and excerpt display settings.
	$exclusive_settings = get_option( 'coil_exclusive_settings_group', 'absent' );

	if ( $exclusive_settings === 'absent' ) {

		$paywall_appearance_settings = Admin\get_paywall_appearance_defaults();
		$exclusive_post_settings     = Admin\get_exclusive_post_defaults();

		// Visibility default is 'public'
		$post_visibility_default = Admin\get_visibility_default();
		// Excerpt display default is false
		$excerpt_display_default = Admin\get_excerpt_display_default();

		$post_visibility_settings = [];
		$excerpt_display_settings = [];
		$post_type_options        = Coil\get_supported_post_types( 'objects' );

		$exclusive_toggle_settings['coil_exclusive_toggle'] = Admin\get_exclusive_content_enabled_default();

		// Set post visibility and excerpt display default for each post type
		foreach ( $post_type_options as $post_type ) {
			$post_visibility_settings[ $post_type->name . '_visibility' ] = $post_visibility_default;
			$excerpt_display_settings[ $post_type->name . '_excerpt' ]    = $excerpt_display_default;
		}

		// Merges all the sections together and updates the option group in the database.
		$new_exclusive_settings = array_merge( $paywall_appearance_settings, $exclusive_post_settings, $post_visibility_settings, $excerpt_display_settings, $exclusive_toggle_settings );
		add_option( 'coil_exclusive_settings_group', $new_exclusive_settings );
	}

	// Loads the Coil button defaults if they have not yet been entered into the database
	$coil_button_settings = get_option( 'coil_button_settings_group', 'absent' );

	if ( $coil_button_settings === 'absent' ) {
		$defaults                                  = Admin\get_coil_button_defaults();
		$new_button_settings                       = [];
		$new_button_settings['coil_button_toggle'] = $defaults['coil_button_toggle'];
		$new_button_settings['coil_button_member_display'] = $defaults['coil_button_member_display'];

		$post_type_options = Coil\get_supported_post_types( 'objects' );
		// Button visibility default is 'show'
		$button_visibility_default = $defaults['post_type_button_visibility'];

		// Set post visibility and excerpt display default for each post type
		foreach ( $post_type_options as $post_type ) {
			$new_button_settings[ $post_type->name . '_button_visibility' ] = $button_visibility_default;
		}

		add_option( 'coil_button_settings_group', $new_button_settings );
	}
}

/**
 * Transfer settings saved in version 1.9 of the plugin where deprecated option groups are being used in the wp_options table
 *
 * @return void
 *
 */
function transfer_customizer_message_settings() {

	$messaging_settings = [];

	$coil_partial_gating_message          = 'coil_partial_gating_message';
	$coil_unsupported_message             = 'coil_unsupported_message';
	$coil_verifying_status_message        = 'coil_verifying_status_message';
	$coil_unable_to_verify_message        = 'coil_unable_to_verify_message';
	$coil_voluntary_donation_message      = 'coil_voluntary_donation_message';
	$coil_learn_more_button_text          = 'coil_learn_more_button_text';
	$coil_learn_more_button_link          = 'coil_learn_more_button_link';
	$coil_fully_gated_excerpt_message     = 'coil_fully_gated_excerpt_message';
	$coil_partially_gated_excerpt_message = 'coil_partially_gated_excerpt_message';

	// Checking if deprecated custom messages have been saved and removing them if that is the case.
	if ( get_theme_mod( $coil_fully_gated_excerpt_message, 'null' ) !== 'null' ) {
		remove_theme_mod( $coil_fully_gated_excerpt_message );
	}
	if ( get_theme_mod( $coil_partially_gated_excerpt_message, 'null' ) !== 'null' ) {
		remove_theme_mod( $coil_partially_gated_excerpt_message );
	}
	if ( get_theme_mod( $coil_partial_gating_message, 'null' ) !== 'null' ) {
		remove_theme_mod( $coil_partial_gating_message );
	}
	if ( get_theme_mod( $coil_verifying_status_message, 'null' ) !== 'null' ) {
		remove_theme_mod( $coil_verifying_status_message );
	}
	if ( get_theme_mod( $coil_voluntary_donation_message, 'null' ) !== 'null' ) {
		remove_theme_mod( $coil_voluntary_donation_message );
	}

	// Using 'null' for comparrison becasue custom messages that were deleted remain in the database with the value false, but still need to be removed.
	$customizer_empty = (
		get_theme_mod( $coil_unsupported_message, 'null' ) === 'null'
		&& get_theme_mod( $coil_unable_to_verify_message, 'null' ) === 'null'
		&& get_theme_mod( $coil_learn_more_button_text, 'null' ) === 'null'
		&& get_theme_mod( $coil_learn_more_button_link, 'null' ) === 'null'
	);

	if ( $customizer_empty ) {
		return;
	}

	// The two fully gated content messages have been combined into one; coil_paywall_message.
	// If one has been added to the customizer and not the other then it will be migrated across.
	// If both are present the coil_unsupported_message will be selected.
	$unable_to_verify_message_exists = get_theme_mod( $coil_unable_to_verify_message, 'null' ) !== 'null';
	$unsupported_message_exists      = get_theme_mod( $coil_unsupported_message, 'null' ) !== 'null';
	if ( $unable_to_verify_message_exists || $unsupported_message_exists ) {
		if ( $unsupported_message_exists ) {
			$messaging_settings['coil_paywall_message'] = get_theme_mod( $coil_unsupported_message );
		} else {
			$messaging_settings['coil_paywall_message'] = get_theme_mod( $coil_unable_to_verify_message );
		}
		remove_theme_mod( $coil_unsupported_message );
		remove_theme_mod( $coil_unable_to_verify_message );
	}

	if ( get_theme_mod( $coil_learn_more_button_text, 'null' ) !== 'null' ) {
		$messaging_settings['coil_paywall_button_text'] = get_theme_mod( $coil_learn_more_button_text );
		remove_theme_mod( $coil_learn_more_button_text );
	}

	if ( get_theme_mod( $coil_learn_more_button_link, 'null' ) !== 'null' ) {
		$messaging_settings['coil_paywall_button_link'] = get_theme_mod( $coil_learn_more_button_link );
		remove_theme_mod( $coil_learn_more_button_link );
	}

	$existing_options = get_option( 'coil_exclusive_settings_group', [] );

	if ( $existing_options !== [] ) {
		update_option( 'coil_exclusive_settings_group', array_merge( $existing_options, $messaging_settings ) );
	} else {
		add_option( 'coil_exclusive_settings_group', $messaging_settings );
	}
}

/**
 * Translate customizer settings
 *
 * If a user has appearance settings which they saved in the customizer, switch them to settings saved in the wp_options table
 *
 * @return void
 *
 */
function transfer_customizer_appearance_settings() {

	// If the setting has already been saved or transferred then simply return
	// Using 'null' for comparison becasue if the padlock and support creator messages were unselected they were stored in the database with the value false, but still need to be transferred.
	if ( 'absent' === get_theme_mod( 'coil_title_padlock', 'absent' ) && 'absent' === get_theme_mod( 'coil_show_donation_bar', 'absent' ) ) {
		return;
	}

	$coil_title_padlock     = 'coil_title_padlock';
	$coil_show_donation_bar = 'coil_show_donation_bar';

	// The padlock display setting is now in the coil_exclusive_settings_group
	if ( get_theme_mod( $coil_title_padlock, 'absent' ) !== 'absent' ) {
		$existing_padlock_settings                  = get_option( 'coil_exclusive_settings_group', [] );
		$new_padlock_settings['coil_title_padlock'] = get_theme_mod( $coil_title_padlock );
		remove_theme_mod( $coil_title_padlock );
		if ( [] !== $existing_padlock_settings ) {
			update_option( 'coil_exclusive_settings_group', array_merge( $existing_padlock_settings, $new_padlock_settings ) );
		} else {
			add_option( 'coil_exclusive_settings_group', $new_padlock_settings );
		}
	}

	// The promotion bar has been deprecated and a Coil button is taking its place instead.
	if ( get_theme_mod( $coil_show_donation_bar, 'absent' ) !== 'absent' ) {
		remove_theme_mod( $coil_show_donation_bar );
	}
}

/**
 * Translate settings in version 1.9
 *
 * If a user has message settings which they saved in the customizer, switch them to settings saved in the wp_options table
 *
 * @return void
 *
 */
function transfer_version_1_9_panel_settings() {

	// Retrieve all current option groups from the database
	$general_settings     = get_option( 'coil_general_settings_group', 'absent' );
	$exclusive_settings   = get_option( 'coil_exclusive_settings_group', 'absent' );
	$coil_button_settings = get_option( 'coil_button_settings_group', 'absent' );

	$new_general_settings     = [];
	$new_exclusive_settings   = [];
	$new_coil_button_settings = [];

	$global_settings = get_option( 'coil_global_settings_group', 'absent' );
	if ( $global_settings !== 'absent' ) {
		if ( isset( $global_settings['coil_payment_pointer_id'] ) ) {
			$new_general_settings['coil_payment_pointer'] = $global_settings['coil_payment_pointer_id'];
		}
		if ( isset( $global_settings['coil_content_container'] ) ) {
			$new_exclusive_settings['coil_content_container'] = $global_settings['coil_content_container'];
		}
		delete_option( 'coil_global_settings_group' );
	}

	// Splits the monetization and visibility data into different option groups
	$monetization_settings = get_option( 'coil_content_settings_posts_group', 'absent' );
	if ( $monetization_settings !== 'absent' ) {
		$post_type_options = Coil\get_supported_post_types( 'objects' );
		foreach ( $post_type_options as $post_type ) {
			if ( isset( $monetization_settings[ $post_type->name ] ) ) {
				if ( $monetization_settings[ $post_type->name ] === 'gate-all' ) {
					$new_general_settings[ $post_type->name . '_monetization' ] = 'monetized';
					$new_exclusive_settings[ $post_type->name . '_visibility' ] = 'exclusive';
				} elseif ( $monetization_settings[ $post_type->name ] === 'no-gating' ) {
					$new_general_settings[ $post_type->name . '_monetization' ] = 'monetized';
					$new_exclusive_settings[ $post_type->name . '_visibility' ] = 'public';
				} elseif ( $monetization_settings[ $post_type->name ] === 'no' ) {
					$new_general_settings[ $post_type->name . '_monetization' ] = 'not-monetized';
					$new_exclusive_settings[ $post_type->name . '_visibility' ] = 'public';
				}
			}
		}
		delete_option( 'coil_content_settings_posts_group' );
	}

	$excerpt_settings = get_option( 'coil_content_settings_excerpt_group', 'absent' );
	if ( $excerpt_settings !== 'absent' ) {
		$post_type_options = Coil\get_supported_post_types( 'objects' );
		foreach ( $post_type_options as $post_type ) {
			if ( isset( $excerpt_settings[ $post_type->name ] ) ) {
				$new_exclusive_settings[ $post_type->name . '_excerpt' ] = $excerpt_settings[ $post_type->name ];
			}
		}
		delete_option( 'coil_content_settings_excerpt_group' );
	}

	$message_settings = get_option( 'coil_messaging_settings_group', 'absent' );
	if ( $message_settings !== 'absent' ) {
		if ( isset( $message_settings['coil_fully_gated_content_message'] ) ) {
			$new_exclusive_settings['coil_paywall_message'] = $message_settings['coil_fully_gated_content_message'];
		}
		if ( isset( $message_settings['coil_learn_more_button_text'] ) ) {
			$new_exclusive_settings['coil_paywall_button_text'] = $message_settings['coil_learn_more_button_text'];
		}
		if ( isset( $message_settings['coil_learn_more_button_link'] ) ) {
			$new_exclusive_settings['coil_paywall_button_link'] = $message_settings['coil_learn_more_button_link'];
		}
		delete_option( 'coil_messaging_settings_group' );
	}

	$appearance_settings = get_option( 'coil_appearance_settings_group', 'absent' );
	if ( $appearance_settings !== 'absent' ) {
		if ( isset( $appearance_settings['coil_title_padlock'] ) ) {
			$new_exclusive_settings['coil_title_padlock'] = $appearance_settings['coil_title_padlock'];
		}
		delete_option( 'coil_appearance_settings_group' );
	}

	// Update all option groups
	if ( $new_general_settings !== [] ) {
		update_option( 'coil_general_settings_group', array_merge( $general_settings, $new_general_settings ) );
	}

	if ( $new_exclusive_settings !== [] ) {
		update_option( 'coil_exclusive_settings_group', array_merge( $exclusive_settings, $new_exclusive_settings ) );
	}

	if ( $new_coil_button_settings !== [] ) {
		update_option( 'coil_button_settings_group', array_merge( $coil_button_settings, $new_coil_button_settings ) );
	}
}

/**
 * Transfer settings post meta from version 1.9 of the plugin where deprecated meta fields are replaced with the new separate monetization and visibility meta fields.
 *
 * @param int post_id
 * @return void
 *
 */
function update_post_meta( $post_id ) {

	$monetize_post_status = get_post_meta( $post_id, '_coil_monetize_post_status', true );
	if ( $monetize_post_status ) {
		switch ( $monetize_post_status ) {
			case 'no':
				$monetization_state = 'not-monetized';
				$visibility_state   = 'public';
				break;
			case 'no-gating':
				$monetization_state = 'monetized';
				$visibility_state   = 'public';
				break;
			case 'gate-all':
				$monetization_state = 'monetized';
				$visibility_state   = 'exclusive';
				break;
			case 'gate-tagged-blocks':
				$monetization_state = 'monetized';
				$visibility_state   = 'gate-tagged-blocks';
				break;
			default:
				$monetization_state = 'default';
				$visibility_state   = 'default';
				break;
		}
		add_post_meta( $post_id, '_coil_monetization_post_status', $monetization_state, true );
		add_post_meta( $post_id, '_coil_visibility_post_status', $visibility_state, true );
		delete_post_meta( $post_id, '_coil_monetize_post_status' );
	}
}
