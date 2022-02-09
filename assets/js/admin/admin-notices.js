/* global coilAdminParams */

( function( $ ) {
	if ( typeof coilAdminParams === 'undefined' || ! coilAdminParams.ajax_url ) {
		return;
	}

	const ajaxUrl = coilAdminParams.ajax_url,
		siteLogoUrl = coilAdminParams.site_logo_url,
		lightCoilLogoUrl = coilAdminParams.coil_logo_url.light,
		darkCoilLogoUrl = coilAdminParams.coil_logo_url.dark,
		notMonetizedPostTypes = coilAdminParams.not_monetized_post_types,
		exclusivePostTypes = coilAdminParams.exclusive_post_types,
		generalModalMsg = coilAdminParams.general_modal_msg,
		exclusiveModalMsg = coilAdminParams.exclusive_modal_msg;

	const activeTabID = $( '.nav-tab-wrapper a.nav-tab-active' ).attr( 'id' );
	const initialFormData = $( 'form' ).serialize();
	// formSubmitting keeps track of whether the submit event fired prior to the beforeunload event.
	let formSubmitting = false;

	// Welcome notice
	if ( activeTabID === 'coil-welcome-settings' ) {
		const $welcomeNoticeDismissButton = $( '.coil-welcome-notice .notice-dismiss' );

		// No welcome notice on this screen.
		if ( ! $welcomeNoticeDismissButton ) {
			return;
		}

		// Fire ajax request to dismiss notice permanently.
		$welcomeNoticeDismissButton.on( 'click', function() {
			$.ajax( {
				url: ajaxUrl,
				type: 'POST',
				data: {
					action: 'dismiss_welcome_notice',
				},
			} );
		} );
	}

	// Exclusive Content tab
	if ( activeTabID === 'coil-exclusive-settings' ) {
		const exclusiveContentEnabled = $( 'input[name="coil_exclusive_settings_group[coil_exclusive_toggle]"]' ).is( ':checked' );
		if ( exclusiveContentEnabled ) {
			$( '*.exclusive-content' ).show();
		} else {
			$( '*.exclusive-content' ).hide();
		}
	}

	// No payment pointer
	if ( activeTabID === 'coil-general-settings' ) {
		// No payment pointer warning
		const noPaymentPointerNotice = $( '.coil-no-payment-pointer-notice' );
		if ( noPaymentPointerNotice.length > 0 ) {
			noPaymentPointerNotice.hide();

			const settingsUpdated = $( '#setting-error-settings_updated' );
			if ( settingsUpdated.length > 0 ) {
				noPaymentPointerNotice.show();
			}
		}
	}

	// Display a modal when submitting incompatible global visibility and monetization defaults.
	$( document ).on( 'submit', 'form', function() {
		formSubmitting = true;
		if ( activeTabID === 'coil-exclusive-settings' ) {
			displayModal( event, notMonetizedPostTypes, '_visibility_exclusive', exclusiveModalMsg );
		} else if ( activeTabID === 'coil-general-settings' ) {
			displayModal( event, exclusivePostTypes, '_monetization_not-monetized', generalModalMsg );
		}
	} );

	// Gets a list of post types that can cause conflicts (e.g. exclusive, or not-monetized).
	// Checks each option on the current tab for the specified post types to see if anything incompatible has been selected.
	// A setting is incompatible if it sets a post type to be exclusive and not-monetized by default.
	// If an incompatibility is found then a modal is displayed which explains which settings will be changed to ensure compatibility.
	function displayModal( event, postTypesToCheck, suffix, modalMsg ) {
		let incompatiblePostTypes = '';
		// Runs through a list of post types from the database that have the potential to cause conflicts with settings on this tab.
		// E.g. On the General Content tab all post types that default to exclusive can lead to incompatible settings.
		postTypesToCheck.forEach( ( postType ) => {
			// The element ID is formed using the suffix and checks each checkbox that could lead to incompatible settings.
			const elementId = postType + suffix;
			// Gathers the names for all post types that have been checked and will create incompatibilities.
			if ( document.getElementById( elementId ).checked ) {
				incompatiblePostTypes += postType + 's, ';
			}
		} );

		if ( incompatiblePostTypes.length > 0 ) {
			// Remove trailing comma
			incompatiblePostTypes = incompatiblePostTypes.slice( 0, -2 );
			// Inserting an 'and' if there are more than two items in the list
			incompatiblePostTypes = incompatiblePostTypes.replace( /,([^,]*)$/, ' and$1' );
			modalMsg = modalMsg.replace( '{postTypes}', incompatiblePostTypes );
			if ( ! confirm( modalMsg ) && ( typeof event.cancelable !== 'boolean' || event.cancelable ) ) { // eslint-disable-line
				// The changes have not been confirmed.
				event.preventDefault();
				formSubmitting = false;
			}
		}
	}

	$( document ).on( 'change', 'input[name="coil_exclusive_settings_group[coil_exclusive_toggle]"]', function() {
		$( '.exclusive-content' ).toggle();
	} );

	$( document ).on( 'keyup', '#coil_paywall_title', function() {
		if ( $( this ).val() !== '' ) {
			$( '.coil-paywall-heading' ).text( $( this ).val() );
		} else {
			$( '.coil-paywall-heading' ).text( $( this ).attr( 'placeholder' ) );
		}
	} );

	$( document ).on( 'keyup', '#coil_paywall_message', function() {
		if ( $( this ).val() !== '' ) {
			$( '.coil-paywall-body' ).text( $( this ).val() );
		} else {
			$( '.coil-paywall-body' ).text( $( this ).attr( 'placeholder' ) );
		}
	} );

	$( document ).on( 'keyup', '#coil_paywall_button_text', function() {
		if ( $( this ).val() !== '' ) {
			$( '.coil-paywall-cta' ).text( $( this ).val() );
		} else {
			$( '.coil-paywall-cta' ).text( $( this ).attr( 'placeholder' ) );
		}
	} );

	$( document ).on( 'change', 'input[name="coil_exclusive_settings_group[coil_title_padlock]"]', function() {
		$( this ).closest( '.coil-row' ).find( '.coil-column-5' ).toggleClass( 'hidden' );
	} );

	$( document ).on( 'change', 'input[name="coil_exclusive_settings_group[coil_padlock_icon_position]"]', function() {
		const padlockPosition = $( this ).val();

		$( '.coil-title-preview-container' ).attr( 'data-padlock-icon-position', padlockPosition );
	} );

	$( document ).on( 'change', 'input[name="coil_exclusive_settings_group[coil_message_color_theme]"]', function() {
		const coilTheme = $( this ).val(),
			logoSetting = $( '#coil_branding' ).val();

		let logoSrc = '';

		$( '.coil-paywall-container' ).attr( 'data-theme', coilTheme );

		if ( logoSetting === 'coil_logo' ) {
			if ( 'light' === coilTheme ) {
				logoSrc = lightCoilLogoUrl;
			} else {
				logoSrc = darkCoilLogoUrl;
			}
			$( '.coil-paywall-image' ).attr( 'src', logoSrc );
		}
	} );

	$( document ).on( 'change', '#coil_branding', function() {
		const logoSetting = $( this ).val(),
			coilTheme = $( 'input[name="coil_exclusive_settings_group[coil_message_color_theme]"]:checked' ).val();

		let logoSrc = '';

		$( '.coil-paywall-image' ).removeClass( 'no_logo site_logo coil_logo' ).addClass( logoSetting );

		if ( logoSetting === 'coil_logo' ) {
			if ( 'light' === coilTheme ) {
				logoSrc = lightCoilLogoUrl;
			} else {
				logoSrc = darkCoilLogoUrl;
			}
		} else if ( logoSetting === 'site_logo' ) {
			logoSrc = siteLogoUrl;
		}

		$( '.coil-paywall-image' ).attr( 'src', logoSrc );
	} );

	$( document ).on( 'change', 'input[name="coil_exclusive_settings_group[coil_padlock_icon_style]"]', function() {
		const $thisInput = $( this ),
			$padlockIcon = $( '.coil-title-preview-container .coil-padlock-icon' ),
			$selectedSvg = $thisInput.siblings( 'svg' ).clone();

		$padlockIcon.html( $selectedSvg );
	} );

	// A modal to alert users to unsaved settings
	window.addEventListener( 'beforeunload', function( event ) {
		if ( ! formSubmitting && initialFormData !== $( 'form' ).serialize() ) {
			// Cancel the event, preventing default behavior will prompt the user.
			event.preventDefault();
			// Chrome requires returnValue to be set
			event.returnValue = '';
		} else {
			delete event.returnValue;
		}
	} );
}( jQuery ) );
