/* global coilAdminParams */

( function( $ ) {
	if ( typeof coilAdminParams === 'undefined' || ! coilAdminParams.ajax_url ) {
		return;
	}

	const siteLogoUrl = coilAdminParams.site_logo_url,
		lightCoilLogoUrl = coilAdminParams.coil_logo_url.light,
		darkCoilLogoUrl = coilAdminParams.coil_logo_url.dark,
		lightStreamingCoilLogoUrl = coilAdminParams.coil_streaming_logo_url.light,
		darkStreamingCoilLogoUrl = coilAdminParams.coil_streaming_logo_url.dark,
		notMonetizedPostTypes = coilAdminParams.not_monetized_post_types,
		exclusivePostTypes = coilAdminParams.exclusive_post_types,
		generalModalMsg = coilAdminParams.general_modal_msg,
		exclusiveModalMsg = coilAdminParams.exclusive_modal_msg,
		invalidPaymentPointerMsg = coilAdminParams.invalid_payment_pointer_msg,
		invalidBlankInputMsg = coilAdminParams.invalid_blank_input_msg,
		invalidUrlMsg = coilAdminParams.invalid_url_msg,
		invalidMarginValueMsg = coilAdminParams.invalid_margin_value_msg,
		streamingWidgetPosition = coilAdminParams.streaming_widget_position;

	const activeTabID = $( '.nav-tab-wrapper a.nav-tab-active' ).attr( 'id' ),
		red = '#EE4852',
		initialFormData = $( 'form' ).serialize();

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
		const nextElement = inputElement.next(),
			screen = $( document ).scrollTop();
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
				if ( screen > top ) {
					$( 'html, body' ).animate( { scrollTop: top + 'px' } );
				} else {
					$( 'html, body' ).animate( { scrollTop: '0px' } );
				}
			}
		} else if ( invalidMsgElement !== null ) {
			inputElement.removeAttr( 'style' );
			invalidMsgElement.remove();
		}
	}

	// Adds or removes alerting functionality for invalid streaming support widget margin input that is detected when focus leaves the field.
	function marginFocusOutValidityHandler( marginInputElement ) {
		const validMargin = /(^(-)?[0-9]+((p)|(px)|(P)|(PX))?$)/,
			nextElement = marginInputElement.next().next(), // checking element below the description
			screen = $( document ).scrollTop();
		const validCondition = validMargin.test( marginInputElement.val() ) || marginInputElement.val() === '';
		let invalidMsgElement = null;
		if ( nextElement !== null && nextElement.hasClass( 'invalid-input' ) ) {
			invalidMsgElement = nextElement;
		}
		if ( ! validCondition ) {
			marginInputElement.css( 'border-color', red );
			if ( invalidMsgElement === null ) {
				if ( marginInputElement.next() !== null ) {
					marginInputElement.next().after( '<p class="invalid-input" style="color: ' + red + '">' + invalidMarginValueMsg + '</p>' );
				}
				let top;
				let position;

				if ( $( '.coil-margin-input-group' ) !== null && $( '.coil-margin-input-group' ).prev() !== null ) {
					position = $( '.coil-margin-input-group' ).prev().position();
					if ( position !== undefined ) {
						top = position.top;
					}
				} else {
					top = 0;
				}

				if ( screen > top ) {
					$( 'html, body' ).animate( { scrollTop: top + 'px' } );
				} else {
					$( 'html, body' ).animate( { scrollTop: '0px' } );
				}
			}
		} else if ( invalidMsgElement !== null ) {
			marginInputElement.removeAttr( 'style' );
			invalidMsgElement.remove();
		}
	}

	// Adds or removes alerting functionality for invalid input that is detected during changes to an input field.
	function inputValidityHandler( inputElement, validCondition, alertWhileTyping, msg ) {
		const nextElement = inputElement.next(),
			onlyWhiteSpace = /^\s+$/;
		let invalidMsgElement = null;

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

	// Adds or removes alerting functionality for invalid margin inputs that are detected during changes to an input field.
	function marginInputValidityHandler( inputElement ) {
		const nextElement = inputElement.next().next(), // checking element below the description
			onlyWhiteSpace = /^\s+$/,
			validMarginValue = /(^(-)?[0-9]+((p)|(px)|(P)|(PX))?$)/,
			whiteSpaceValidCondition = ! onlyWhiteSpace.test( inputElement.val() ),
			marginValueValidCondition = validMarginValue.test( inputElement.val() ) || inputElement.val() === '';
		let invalidMsgElement = null;

		if ( nextElement !== undefined && nextElement !== null && nextElement.hasClass( 'invalid-input' ) ) {
			invalidMsgElement = nextElement;
		}
		if ( invalidMsgElement !== null ) {
			if ( whiteSpaceValidCondition && invalidMsgElement.text() === invalidBlankInputMsg ) {
				inputElement.removeAttr( 'style' );
				invalidMsgElement.remove();
			} else if ( marginValueValidCondition && invalidMsgElement.text() === invalidMarginValueMsg ) {
				inputElement.removeAttr( 'style' );
				invalidMsgElement.remove();
			}
		} else if ( onlyWhiteSpace.test( inputElement.val() ) && invalidMsgElement === null ) {
			if ( inputElement.next() !== null ) {
				inputElement.css( 'border-color', red );
				inputElement.next().after( '<p class="invalid-input" style="color: ' + red + '">' + invalidBlankInputMsg + '</p>' );
			}
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
		const exclusiveContentEnabled = $( 'input[name="coil_exclusive_settings_group[coil_exclusive_toggle]"]' ).is( ':checked' ),
			siteLogoSelected = $( '#coil_branding option:selected' ).val() === 'site_logo';
		if ( exclusiveContentEnabled ) {
			$( '*.exclusive-content-section' ).show();
		} else {
			$( '*.exclusive-content-section' ).hide();
		}

		if ( siteLogoSelected ) {
			$( '.set-site-logo-description' ).show();
		} else {
			$( '.set-site-logo-description' ).hide();
		}
	}

	// Streaming support widget tab
	if ( activeTabID === 'streaming-widget-settings' ) {
		// Initial set-up
		const streamingWidgetEnabled = $( 'input[name="streaming_widget_settings_group[streaming_widget_toggle]"]' ).is( ':checked' ),
			streamingWidgetPreviewSelector = 'div.coil-preview.coil-non-members .streaming-widget div > div',
			streamingWidgetMemberPreviewSelector = 'div.coil-preview.coil-members .streaming-widget div > div',
			streamingWidget = $( '#streaming_widget_text' ),
			coilMembersWidget = $( '#members_streaming_widget_text' ),
			onlyWhiteSpace = /^\s+$/,
			position = streamingWidgetPosition.split( '-' );
		if ( streamingWidgetEnabled ) {
			$( '*.streaming-widget-section' ).show();
		} else {
			$( '*.streaming-widget-section' ).hide();
		}

		// Hide the streaming support widget text div if the text is only white space
		if ( onlyWhiteSpace.test( streamingWidget.val() ) ) {
			$( streamingWidgetPreviewSelector ).hide();
		}
		if ( onlyWhiteSpace.test( coilMembersWidget.val() ) ) {
			$( streamingWidgetMemberPreviewSelector ).hide();
		}

		// Display the two margin input cells that are relevent to the streaming support widget's position
		$( '.margin-' + position[ 0 ] ).show();
		$( '.margin-' + position[ 1 ] ).show();
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
		const paymentPointer = $( '#coil_payment_pointer' ),
			pattern = /^(https:\/\/.)|^[\$]./,
			validityCondition = pattern.test( $( this ).val() );
		focusOutValidityHandler( paymentPointer, validityCondition, invalidPaymentPointerMsg );
	} );

	// Removes the invalid input warning if the input becomes valid
	$( document ).on( 'input', '#coil_payment_pointer', function() {
		const paymentPointer = $( '#coil_payment_pointer' ),
			pattern = /^(https:\/\/.)|^[\$]./,
			validityCondition = pattern.test( $( this ).val() );
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
		const buttonTextElement = $( '#coil_paywall_button_text' ),
			onlyWhiteSpace = /^\s+$/,
			validityCondition = ! onlyWhiteSpace.test( $( this ).val() );
		focusOutValidityHandler( buttonTextElement, validityCondition, invalidBlankInputMsg );
	} );

	$( document ).on( 'input', '#coil_paywall_button_text', function() {
		const buttonTextElement = $( '#coil_paywall_button_text' ),
			onlyWhiteSpace = /^\s+$/,
			validityCondition = ! onlyWhiteSpace.test( $( this ).val() );
		inputValidityHandler( buttonTextElement, validityCondition, true, invalidBlankInputMsg );

		if ( $( this ).val() !== '' && ! onlyWhiteSpace.test( $( this ).val() ) ) {
			$( '.coil-paywall-cta' ).text( $( this ).val() );
		} else {
			$( '.coil-paywall-cta' ).text( $( this ).attr( 'placeholder' ) );
		}
	} );

	// Invalid input alert
	$( document ).on( 'focusout', '#coil_paywall_button_link', function() {
		const buttonLinkElement = $( '#coil_paywall_button_link' ),
			validUrl = isValidUrl( $( this ).val() ),
			validityCondition = validUrl || $( this ).val() === '';

		focusOutValidityHandler( buttonLinkElement, validityCondition, invalidUrlMsg );
	} );

	$( document ).on( 'input', '#coil_paywall_button_link', function() {
		const buttonLinkElement = $( '#coil_paywall_button_link' ),
			onlyWhiteSpace = /^\s+$/,
			validityCondition = ! onlyWhiteSpace.test( $( this ).val() );
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
		const cssInputElement = $( '#coil_content_container' ),
			onlyWhiteSpace = /^\s+$/,
			validityCondition = ! onlyWhiteSpace.test( $( this ).val() );

		inputValidityHandler( cssInputElement, validityCondition, true, invalidBlankInputMsg );
	} );

	/* ------------------------------------------------------------------------ *
	* Streaming support widget tab
	* ------------------------------------------------------------------------ */

	$( document ).on( 'change', 'input[name="streaming_widget_settings_group[streaming_widget_toggle]"]', function() {
		$( '.streaming-widget-section' ).toggle();
	} );

	$( document ).on( 'input', '#streaming_widget_text', function() {
		const previewSelector = 'div.coil-preview.coil-non-members .streaming-widget div > div',
			onlyWhiteSpace = /^\s+$/;
		if ( $( this ).val() !== '' ) {
			if ( ! onlyWhiteSpace.test( $( this ).val() ) ) {
				$( previewSelector ).text( $( this ).val() );
				$( previewSelector ).show();
			} else {
				$( previewSelector ).hide();
			}
		} else {
			$( previewSelector ).text( $( this ).attr( 'placeholder' ) );
			$( previewSelector ).show();
		}
	} );

	// Invalid input alert
	$( document ).on( 'focusout', '#streaming_widget_link', function() {
		const widgetLinkElement = $( '#streaming_widget_link' ),
			validUrl = isValidUrl( $( this ).val() ),
			validityCondition = validUrl || $( this ).val() === '';

		focusOutValidityHandler( widgetLinkElement, validityCondition, invalidUrlMsg );
	} );

	$( document ).on( 'input', '#streaming_widget_link', function() {
		const widgetLinkElement = $( '#streaming_widget_link' ),
			onlyWhiteSpace = /^\s+$/,
			validityCondition = ! onlyWhiteSpace.test( $( this ).val() );
		inputValidityHandler( widgetLinkElement, validityCondition, true, invalidBlankInputMsg );
	} );

	$( document ).on( 'input', '#members_streaming_widget_text', function() {
		const previewSelector = 'div.coil-preview.coil-members .streaming-widget div > div',
			onlyWhiteSpace = /^\s+$/;
		if ( $( this ).val() !== '' ) {
			if ( ! onlyWhiteSpace.test( $( this ).val() ) ) {
				$( previewSelector ).text( $( this ).val() );
				$( previewSelector ).show();
			} else {
				$( previewSelector ).hide();
			}
		} else {
			$( previewSelector ).text( $( this ).attr( 'placeholder' ) );
			$( previewSelector ).show();
		}
	} );

	$( document ).on( 'change', 'input[name="streaming_widget_settings_group[streaming_widget_color_theme]"]', function() {
		const coilTheme = $( this ).val();

		let logoSrc = '',
			logoStreamingSrc = '';

		$( '.coil-preview .streaming-widget' ).attr( 'data-theme', coilTheme );

		if ( 'light' === coilTheme ) {
			logoSrc = lightCoilLogoUrl;
			logoStreamingSrc = lightStreamingCoilLogoUrl;
		} else {
			logoSrc = darkCoilLogoUrl;
			logoStreamingSrc = darkStreamingCoilLogoUrl;
		}

		$( '.coil-preview.coil-non-members .streaming-widget-image' ).attr( 'src', logoSrc );
		$( '.coil-preview.coil-members .streaming-widget-image' ).attr( 'src', logoStreamingSrc );
	} );

	$( document ).on( 'change', 'input[name="streaming_widget_settings_group[streaming_widget_member_display]"]', function() {
		$( 'div.coil-preview.coil-members' ).toggleClass( 'hide' );
	} );

	$( document ).on( 'change', 'input[name="streaming_widget_settings_group[streaming_widget_size]"]', function() {
		const widgetSize = $( this ).val();

		$( '.coil-preview .streaming-widget' ).attr( 'data-size', widgetSize );
	} );

	$( document ).on( 'change', 'select[name="streaming_widget_settings_group[streaming_widget_position]"]', function() {
		const widgetPosition = $( this ).val(),
			position = widgetPosition.split( '-' );

		$( '.coil-preview .streaming-widget' ).attr( 'data-position', widgetPosition );

		// Display only the two margin input cells that are relevent to the streaming support widget's position
		if ( position[ 0 ] === 'top' ) {
			$( '.margin-bottom' ).hide();
			$( '.margin-top' ).show();
		} else {
			$( '.margin-top' ).hide();
			$( '.margin-bottom' ).show();
		}

		if ( position[ 1 ] === 'left' ) {
			$( '.margin-right' ).hide();
			$( '.margin-left' ).show();
		} else {
			$( '.margin-left' ).hide();
			$( '.margin-right' ).show();
		}
	} );

	$( document ).on( 'input', '#streaming_widget_top_margin', function() {
		marginInputValidityHandler( $( '#streaming_widget_top_margin' ) );
	} );

	$( document ).on( 'focusout', '#streaming_widget_top_margin', function() {
		marginFocusOutValidityHandler( $( '#streaming_widget_top_margin' ) );
	} );

	$( document ).on( 'input', '#streaming_widget_bottom_margin', function() {
		marginInputValidityHandler( $( '#streaming_widget_bottom_margin' ) );
	} );

	$( document ).on( 'focusout', '#streaming_widget_bottom_margin', function() {
		marginFocusOutValidityHandler( $( '#streaming_widget_bottom_margin' ) );
	} );

	$( document ).on( 'input', '#streaming_widget_right_margin', function() {
		marginInputValidityHandler( $( '#streaming_widget_right_margin' ) );
	} );

	$( document ).on( 'focusout', '#streaming_widget_right_margin', function() {
		marginFocusOutValidityHandler( $( '#streaming_widget_right_margin' ) );
	} );

	$( document ).on( 'input', '#streaming_widget_left_margin', function() {
		marginInputValidityHandler( $( '#streaming_widget_left_margin' ) );
	} );

	$( document ).on( 'focusout', '#streaming_widget_left_margin', function() {
		marginFocusOutValidityHandler( $( '#streaming_widget_left_margin' ) );
	} );
}( jQuery ) );
