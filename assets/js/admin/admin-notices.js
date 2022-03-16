/* global coilAdminParams */

( function( $ ) {
	if ( typeof coilAdminParams === 'undefined' || ! coilAdminParams.ajax_url ) {
		return;
	}

	const siteLogoUrl = coilAdminParams.site_logo_url,
		lightCoilLogoUrl = coilAdminParams.coil_logo_url.light,
		darkCoilLogoUrl = coilAdminParams.coil_logo_url.dark,
		notMonetizedPostTypes = coilAdminParams.not_monetized_post_types,
		exclusivePostTypes = coilAdminParams.exclusive_post_types,
		generalModalMsg = coilAdminParams.general_modal_msg,
		exclusiveModalMsg = coilAdminParams.exclusive_modal_msg,
		invalidPaymentPointerMsg = coilAdminParams.invalid_payment_pointer_msg,
		invalidBlankInputMsg = coilAdminParams.invalid_blank_input_msg,
		invalidUrlMsg = coilAdminParams.invalid_url_message;

	const activeTabID = $( '.nav-tab-wrapper a.nav-tab-active' ).attr( 'id' );
	const red = '#EE4852';
	const initialFormData = $( 'form' ).serialize();
	// formSubmitting keeps track of whether the submit event fired prior to the beforeunload event.
	let formSubmitting = false;

	/* ------------------------------------------------------------------------ *
	* Helper functions
	* ------------------------------------------------------------------------ */

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

	// Adds or removes alerting functionality for invalid input that is detected when focus leaves an input field.
	function focusOutValidityHandler( inputElement, validCondition, msg ) {
		const nextElement = inputElement.next();
		let invalidMsgElement = null;
		if ( nextElement !== null && nextElement.hasClass( 'invalid-input' ) ) {
			invalidMsgElement = nextElement;
		}
		if ( ! validCondition ) {
			inputElement.css( 'border-color', red );
			if ( invalidMsgElement === null ) {
				inputElement.after( '<p class="invalid-input" style="color: ' + red + '">' + msg + '</p>' );
				const position = inputElement.prev().position();
				let top;
				if ( position !== undefined ) {
					top = position.top;
				} else {
					top = 0;
				}
				$( 'html, body' ).animate( { scrollTop: top + 'px' } );
			}
		} else if ( invalidMsgElement !== null ) {
			inputElement.removeAttr( 'style' );
			invalidMsgElement.remove();
		}
	}

	// Adds or removes alerting functionality for invalid input that is detected during changes to an input field.
	function inputValidityHandler( inputElement, validCondition, alertWhileTyping, msg ) {
		const nextElement = inputElement.next();
		let invalidMsgElement = null;
		const onlyWhiteSpace = /^\s+$/;
		if ( nextElement !== null && nextElement.hasClass( 'invalid-input' ) ) {
			invalidMsgElement = nextElement;
		}
		if ( invalidMsgElement !== null && validCondition ) {
			inputElement.removeAttr( 'style' );
			invalidMsgElement.remove();
		} else if ( alertWhileTyping && onlyWhiteSpace.test( inputElement.val() ) && invalidMsgElement === null ) {
			inputElement.css( 'border-color', red );
			inputElement.after( '<p class="invalid-input" style="color: ' + red + '">' + msg + '</p>' );
		}
	}

	// Returns a boolean indicating whether a URL is valid (true) or invalid (false)
	function isValidUrl( string ) {
		let url;
		try {
			url = new URL( string );
		} catch ( _ ) {
			return false;
		}
		return url.protocol === 'http:' || url.protocol === 'https:';
	}

	/* ------------------------------------------------------------------------ *
	* Initial set-up
	* ------------------------------------------------------------------------ */

	// Welcome notice
	if ( activeTabID === 'coil-welcome-settings' ) {
		const $welcomeNotice = $( '.coil-welcome-notice' );

		// No welcome notice on this screen.
		if ( $welcomeNotice.length === 0 ) {
			$( '.tab-styling .button-primary' ).show();
		} else {
			$( '.tab-styling .button-primary' ).hide();
		}
	}

	// General Settings tab
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

	// Exclusive Content tab
	if ( activeTabID === 'coil-exclusive-settings' ) {
		const exclusiveContentEnabled = $( 'input[name="coil_exclusive_settings_group[coil_exclusive_toggle]"]' ).is( ':checked' );
		if ( exclusiveContentEnabled ) {
			$( '*.exclusive-content-section' ).show();
		} else {
			$( '*.exclusive-content-section' ).hide();
		}
		const siteLogoSelected = $( '#coil_branding option:selected' ).val() === 'site_logo';
		if ( siteLogoSelected ) {
			$( '.set-site-logo-description' ).show();
		} else {
			$( '.set-site-logo-description' ).hide();
		}
	}

	// Coil Button tab
	if ( activeTabID === 'coil-button-settings' ) {
		// Initial set-up
		const coilButtonEnabled = $( 'input[name="coil_button_settings_group[coil_button_toggle]"]' ).is( ':checked' );
		if ( coilButtonEnabled ) {
			$( '*.coil-button-section' ).show();
		} else {
			$( '*.coil-button-section' ).hide();
		}
	}

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

	// Display a modal when submitting incompatible global visibility and monetization defaults.
	$( document ).on( 'submit', 'form', function() {
		formSubmitting = true;
		if ( activeTabID === 'coil-exclusive-settings' ) {
			displayModal( event, notMonetizedPostTypes, '_visibility_exclusive', exclusiveModalMsg );
		} else if ( activeTabID === 'coil-general-settings' ) {
			displayModal( event, exclusivePostTypes, '_monetization_not-monetized', generalModalMsg );
		}
	} );

	/* ------------------------------------------------------------------------ *
	* General Settings tab
	* ------------------------------------------------------------------------ */

	// Invalid input alert
	$( document ).on( 'focusout', '#coil_payment_pointer', function() {
		const paymentPointer = $( '#coil_payment_pointer' );
		const pattern = /^(https:\/\/.)|^[\$]./;
		const validityCondition = pattern.test( $( this ).val() );
		focusOutValidityHandler( paymentPointer, validityCondition, invalidPaymentPointerMsg );
	} );

	// Removes the invalid input warning if the input becomes valid
	$( document ).on( 'input', '#coil_payment_pointer', function() {
		const paymentPointer = $( '#coil_payment_pointer' );
		const pattern = /^(https:\/\/.)|^[\$]./;
		const validityCondition = pattern.test( $( this ).val() );
		inputValidityHandler( paymentPointer, validityCondition, false, '' );
	} );

	/* ------------------------------------------------------------------------ *
	* Exclusive Settings tab
	* ------------------------------------------------------------------------ */

	$( document ).on( 'change', 'input[name="coil_exclusive_settings_group[coil_exclusive_toggle]"]', function() {
		$( '.exclusive-content-section' ).toggle();
	} );

	$( document ).on( 'input', '#coil_paywall_title', function() {
		if ( $( this ).val() !== '' ) {
			$( '.coil-paywall-heading' ).text( $( this ).val() );
		} else {
			$( '.coil-paywall-heading' ).text( $( this ).attr( 'placeholder' ) );
		}
	} );

	$( document ).on( 'input', '#coil_paywall_message', function() {
		if ( $( this ).val() !== '' ) {
			$( '.coil-paywall-body' ).text( $( this ).val() );
		} else {
			$( '.coil-paywall-body' ).text( $( this ).attr( 'placeholder' ) );
		}
	} );

	// Invalid input alert
	$( document ).on( 'focusout', '#coil_paywall_button_text', function() {
		const buttonTextElement = $( '#coil_paywall_button_text' );
		const onlyWhiteSpace = /^\s+$/;
		const validityCondition = ! onlyWhiteSpace.test( $( this ).val() );
		focusOutValidityHandler( buttonTextElement, validityCondition, invalidBlankInputMsg );
	} );

	$( document ).on( 'input', '#coil_paywall_button_text', function() {
		const buttonTextElement = $( '#coil_paywall_button_text' );
		const onlyWhiteSpace = /^\s+$/;
		const validityCondition = ! onlyWhiteSpace.test( $( this ).val() );
		inputValidityHandler( buttonTextElement, validityCondition, true, invalidBlankInputMsg );

		if ( $( this ).val() !== '' && ! onlyWhiteSpace.test( $( this ).val() ) ) {
			$( '.coil-paywall-cta' ).text( $( this ).val() );
		} else {
			$( '.coil-paywall-cta' ).text( $( this ).attr( 'placeholder' ) );
		}
	} );

	// Invalid input alert
	$( document ).on( 'focusout', '#coil_paywall_button_link', function() {
		const buttonLinkElement = $( '#coil_paywall_button_link' );
		const validUrl = isValidUrl( $( this ).val() );
		const validityCondition = validUrl || $( this ).val() === '';

		focusOutValidityHandler( buttonLinkElement, validityCondition, invalidUrlMsg );
	} );

	$( document ).on( 'input', '#coil_paywall_button_link', function() {
		const buttonLinkElement = $( '#coil_paywall_button_link' );
		const onlyWhiteSpace = /^\s+$/;
		const validityCondition = ! onlyWhiteSpace.test( $( this ).val() );
		inputValidityHandler( buttonLinkElement, validityCondition, true, invalidBlankInputMsg );
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
		const siteLogoSelected = $( '#coil_branding option:selected' ).val() === 'site_logo';
		if ( siteLogoSelected ) {
			$( '.set-site-logo-description' ).show();
		} else {
			$( '.set-site-logo-description' ).hide();
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
			if ( logoSrc === '' ) {
				$( '.site_logo' ).hide();
			} else {
				$( '.site_logo' ).show();
			}
		}

		$( '.coil-paywall-image' ).attr( 'src', logoSrc );
	} );

	$( document ).on( 'change', 'input[name="coil_exclusive_settings_group[coil_title_padlock]"]', function() {
		$( this ).closest( '.coil-row' ).find( '.coil-column-5' ).toggleClass( 'hidden' );
	} );

	$( document ).on( 'change', 'input[name="coil_exclusive_settings_group[coil_padlock_icon_position]"]', function() {
		const padlockPosition = $( this ).val();

		$( '.coil-title-preview-container' ).attr( 'data-padlock-icon-position', padlockPosition );
	} );

	$( document ).on( 'change', 'input[name="coil_exclusive_settings_group[coil_padlock_icon_style]"]', function() {
		const $thisInput = $( this ),
			$padlockIcon = $( '.coil-title-preview-container .coil-padlock-icon' ),
			$selectedSvg = $thisInput.siblings( 'svg' ).clone();

		$padlockIcon.html( $selectedSvg );
	} );

	$( document ).on( 'input', '#coil_content_container', function() {
		const cssInputElement = $( '#coil_content_container' );
		const onlyWhiteSpace = /^\s+$/;
		const validityCondition = ! onlyWhiteSpace.test( $( this ).val() );
		inputValidityHandler( cssInputElement, validityCondition, true, invalidBlankInputMsg );
	} );

	/* ------------------------------------------------------------------------ *
	* Coil Button tab
	* ------------------------------------------------------------------------ */

	$( document ).on( 'change', 'input[name="coil_button_settings_group[coil_button_toggle]"]', function() {
		$( '.coil-button-section' ).toggle();
	} );

	// Invalid input alert
	$( document ).on( 'focusout', '#coil_button_link', function() {
		const buttonLinkElement = $( '#coil_button_link' );
		const validUrl = isValidUrl( $( this ).val() );
		const validityCondition = validUrl || $( this ).val() === '';

		focusOutValidityHandler( buttonLinkElement, validityCondition, invalidUrlMsg );
	} );

	$( document ).on( 'input', '#coil_button_link', function() {
		const buttonLinkElement = $( '#coil_button_link' );
		const onlyWhiteSpace = /^\s+$/;
		const validityCondition = ! onlyWhiteSpace.test( $( this ).val() );
		inputValidityHandler( buttonLinkElement, validityCondition, true, invalidBlankInputMsg );
	} );
}( jQuery ) );
