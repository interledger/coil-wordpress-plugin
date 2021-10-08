<?php

/**
 * Translate customizer settings
 *
 * If a user has message settings which they saved in the customizer, switch them to settings saved in the wp_options table
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

	$customizer_empty = (
		get_theme_mod( $coil_partial_gating_message, 'null' ) === 'null'
		&& get_theme_mod( $coil_unsupported_message, 'null' ) === 'null'
		&& get_theme_mod( $coil_verifying_status_message, 'null' ) === 'null'
		&& get_theme_mod( $coil_unable_to_verify_message, 'null' ) === 'null'
		&& get_theme_mod( $coil_voluntary_donation_message, 'null' ) === 'null'
		&& get_theme_mod( $coil_learn_more_button_text, 'null' ) === 'null'
		&& get_theme_mod( $coil_learn_more_button_link, 'null' ) === 'null'
	);

	if ( $customizer_empty ) {
		return;
	}

	// Using 'null' for comparrison becasue custom messages that were deleted remain in the database with the value false, but still need to be removed.
	// coil_partial_gating_message has changed name to coil_partially_gated_content_message.
	if ( get_theme_mod( $coil_partial_gating_message, 'null' ) !== 'null' ) {
		$messaging_settings['coil_partially_gated_content_message'] = get_theme_mod( $coil_partial_gating_message );
		remove_theme_mod( $coil_partial_gating_message );
	}

	// The two fully gated content messages have been combined into one; coil_fully_gated_content_message.
	// If one has been added to the customizer and not the other then it will be migrated across.
	// If both are present the coil_unsupported_message will be selected.
	if ( get_theme_mod( $coil_unable_to_verify_message ) !== 'null' && get_theme_mod( $coil_unsupported_message, 'null' ) === 'null' ) {
		$messaging_settings['coil_fully_gated_content_message'] = get_theme_mod( $coil_unable_to_verify_message );
		remove_theme_mod( $coil_unable_to_verify_message );
	} elseif ( get_theme_mod( $coil_unable_to_verify_message ) !== 'null' && get_theme_mod( $coil_unsupported_message, 'null' ) !== 'null' ) {
		$messaging_settings['coil_fully_gated_content_message'] = get_theme_mod( $coil_unsupported_message );
		remove_theme_mod( $coil_unsupported_message );
		remove_theme_mod( $coil_unable_to_verify_message );
	} elseif ( get_theme_mod( $coil_unsupported_message, 'null' ) !== 'null' && get_theme_mod( $coil_unable_to_verify_message ) === 'null' ) {
		$messaging_settings['coil_fully_gated_content_message'] = get_theme_mod( $coil_unsupported_message );
		remove_theme_mod( $coil_unsupported_message );
	}

	if ( get_theme_mod( $coil_verifying_status_message, 'null' ) !== 'null' ) {
		$messaging_settings['coil_verifying_status_message'] = get_theme_mod( $coil_verifying_status_message );
		remove_theme_mod( $coil_verifying_status_message );
	}

	// coil_voluntary_donation_message has changed name to coil_promotion_bar_message.
	if ( get_theme_mod( $coil_voluntary_donation_message, 'null' ) !== 'null' ) {
		$messaging_settings['coil_promotion_bar_message'] = get_theme_mod( $coil_voluntary_donation_message );
		remove_theme_mod( $coil_voluntary_donation_message );
	}

	if ( get_theme_mod( $coil_learn_more_button_text, 'null' ) !== 'null' ) {
		$messaging_settings['coil_learn_more_button_text'] = get_theme_mod( $coil_learn_more_button_text );
		remove_theme_mod( $coil_learn_more_button_text );
	}

	if ( get_theme_mod( $coil_learn_more_button_link, 'null' ) !== 'null' ) {
		$messaging_settings['coil_learn_more_button_link'] = get_theme_mod( $coil_learn_more_button_link );
		remove_theme_mod( $coil_learn_more_button_link );
	}

	$existing_options = get_option( 'coil_messaging_settings_group' );

	if ( false !== $existing_options ) {
		update_option( 'coil_messaging_settings_group', array_merge( $existing_options, $messaging_settings ) );
	} else {
		update_option( 'coil_messaging_settings_group', $messaging_settings );
	}

}
