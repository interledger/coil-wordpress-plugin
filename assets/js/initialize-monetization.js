/* global Cookies */
/* global coilParams */

( function( $ ) {
	if ( typeof coilParams === 'undefined' ) {
		console.error( 'There was a problem retrieving the Coil parameters' );
		return false;
	}

	/**
	 *
	 * Returns false if the container element doesn't exist OR an invalid CSS
	 * selector was used to define it.
	 *
	 * @return {boolean} Check the content container element exists.
	*/
	function hasContentContainer() {
		let element;

		// Use try-catch to handle invalid CSS selectors.
		// Accesses coilParams object because it is called before contentContainer has been defined.
		try {
			element = document.querySelector( coilParams.content_container );
		} catch ( e ) {
			console.error( 'An error occured when attempting to retrieve the page. Invalid container.' );
			return false;
		}

		// Content container exists.
		if ( element ) {
			return true;
		}

		// Content container does not exist.
		console.error( 'An error occured when attempting to retrieve the page. Container not found.' );
		return false;
	}

	// If the content container is invalid and there is exclusive content present, then the plugin cannot function properly.
	if ( ! hasContentContainer() && ! document.body.classList.contains( 'coil-public' ) ) {
		return false;
	}

	const contentContainer = coilParams.content_container,
		paywallTitle = coilParams.paywall_title,
		loadingContent = coilParams.loading_content,
		paywallMessage = coilParams.paywall_message,
		streamingWidgetUnpaidMessage = coilParams.streaming_widget_unpaid_message,
		streamingWidgetPaidMessage = coilParams.streaming_widget_paid_message,
		showStreamingWidgetToMembers = Boolean( coilParams.show_streaming_widget_to_members ),
		streamingWidgetLink = coilParams.streaming_widget_link,
		postExcerpt = coilParams.post_excerpt,
		hasCoilDivider = Boolean( coilParams.has_coil_divider ),
		adminMissingIdNotice = coilParams.admin_missing_id_notice,
		paywallButtonText = coilParams.paywall_button_text,
		paywallButtonLink = coilParams.paywall_button_link,
		coilMessageBranding = coilParams.coil_message_branding,
		streamingWidgetTheme = coilParams.streaming_widget_theme,
		streamingWidgetSize = coilParams.streaming_widget_size,
		streamingWidgetPosition = coilParams.streaming_widget_position,
		WidgetMarginTop = coilParams.streaming_widget_margin_top,
		WidgetMarginRight = coilParams.streaming_widget_margin_right,
		WidgetMarginBottom = coilParams.streaming_widget_margin_bottom,
		WidgetMarginLeft = coilParams.streaming_widget_margin_left,
		streamingWidgetGloballyEnabled = Boolean( coilParams.streaming_widget_enabled ), // Cast to boolean - wp_localize_script forces string values.
		siteLogo = coilParams.site_logo,
		coilLogo = coilParams.coil_logo,
		coilLogoStreaming = coilParams.coil_logo_streaming,
		coilLogoWhite = coilParams.coil_logo_white,
		coilLogoWhiteStreaming = coilParams.coil_logo_white_streaming,
		exclusiveMessageTheme = coilParams.exclusive_message_theme,
		fontSelection = Boolean( coilParams.font_selection );

	const subscriberOnlyMessage = wp.template( 'subscriber-only-message' );
	const streamingWidgetMessage = wp.template( 'streaming-support-widget-message' );

	const messageWrapper = $( 'p.monetize-msg' );

	let monetizationStartEventOccurred = false;

	/**
	 * @param {String} message The message to display inside the tag.
	 * @param {String} customClass Any extra custom classes to add to this tag.
	 * @return {object} Output a monetization message when the state is changing.
	 */
	function showMonetizationMessage( message, customClass ) {
		const elem = document.createElement( 'p' );
		elem.classList.add( 'monetize-msg' );
		if ( customClass ) {
			elem.classList.add( customClass );
		}
		elem.innerHTML = message;
		return elem;
	}

	/**
	 * @param {String} message from coilParams.
	 * @return {object} Output the gated content message when content is
	 * set to Member Only
	 */
	function showSubscriberOnlyMessage( message ) {
		const modalContainer = document.createElement( 'div' );
		modalContainer.classList.add( 'entry-content', 'coil-message-container' );
		if ( exclusiveMessageTheme === 'dark' ) {
			modalContainer.classList.add( 'coil-dark-theme' );
		}
		if ( fontSelection ) {
			modalContainer.classList.add( 'coil-inherit-theme-font' );
		}

		let brandingLogo = '';

		if ( coilMessageBranding === 'site_logo' && siteLogo !== '' ) {
			brandingLogo = brandingLogo = '<img src="' + siteLogo + '">';
		} else if ( coilMessageBranding === 'coil_logo' && exclusiveMessageTheme === 'dark' ) {
			brandingLogo = '<img src="' + coilLogoWhite + '">';
		} else if ( coilMessageBranding === 'coil_logo' ) {
			brandingLogo = '<img src="' + coilLogo + '">';
		}

		const modalData = {
			headerLogo: brandingLogo,
			title: paywallTitle,
			content: message,
			button: {
				text: paywallButtonText,
				href: paywallButtonLink,
			},
		};

		$( modalContainer ).append( subscriberOnlyMessage( modalData ) );
		return modalContainer;
	}

	/**
	 * Adds the streaming support widget to the body oif the document and adds it's handler functions.
	 * @param {String} message Message shown to thank Coil members, or to encourage users to sign up.
	 * @return {void}
	*/
	function showStreamingWidget( message ) {
		const streamingWidget = createStreamingWidget( message );
		const onlyWhiteSpace = /^\s+$/;
		$( 'body' ).append( streamingWidget );
		// Hides the text div if there is no text
		if ( onlyWhiteSpace.test( message ) ) {
			$( '.streaming-support-widget a div' ).hide();
		}
		addButtonDismissClickHandler();
		addWidgetDismissAppearanceHandler();
	}

	/**
	 * @param {String} message Message shown to thank Coil members, or to encourage users to sign up.
	 * @return {object} Output a streaming support widget.
	*/
	function createStreamingWidget( message ) {
		const positionArray = streamingWidgetPosition.split( '-' );
		const verticalPosition = positionArray[ 0 ];
		const horizontalPosition = positionArray[ 1 ];
		let topMargin,
			rightMargin,
			bottomMargin,
			leftMargin;

		const modalContainer = document.createElement( 'div' );
		$( modalContainer ).addClass( 'streaming-widget-container' + ' ' + verticalPosition + ' ' + horizontalPosition );

		let brandingLogo = '';

		if ( streamingWidgetTheme === 'light' ) {
			modalContainer.classList.add( 'coil-light-theme' );
			brandingLogo = coilLogo;
		} else {
			brandingLogo = coilLogoWhite;
		}

		if ( streamingWidgetSize === 'small' ) {
			modalContainer.classList.add( 'streaming-widget-small' );
		}

		const modalData = {
			headerLogo: brandingLogo,
			button: {
				text: message,
				href: streamingWidgetLink,
			},
		};

		$( modalContainer ).append( streamingWidgetMessage( modalData ) );

		// Set the margins only for the two applicable sides of the streaming support widget based on the position selected.
		if ( horizontalPosition === 'left' ) {
			rightMargin = '0';
			leftMargin = checkMarginValues( WidgetMarginLeft );
		} else {
			rightMargin = checkMarginValues( WidgetMarginRight );
			leftMargin = '0';
		}

		if ( verticalPosition === 'top' ) {
			topMargin = checkMarginValues( WidgetMarginTop );
			bottomMargin = '0';
		} else {
			topMargin = '0';
			bottomMargin = checkMarginValues( WidgetMarginBottom );
		}

		$( modalContainer ).find( '.streaming-support-widget' ).css( { 'margin-top': topMargin + 'px', 'margin-right': rightMargin + 'px', 'margin-bottom': bottomMargin + 'px', 'margin-left': leftMargin + 'px' } );
		return modalContainer;
	}

	/**
	 * Determines whether a streaming support widget should be added to the page.
	 * The button will only be added if it is not already present, it is globally enabled, the browser doesn't have a dismiss cookie for it,
	 * and neither the pending message nor paywall are being displayed.
	 */
	function maybeAddStreamingWidget() {
		const buttonEnabled = hasStreamingWidgetEnabled();
		const buttonAlreadyExists = $( '.streaming-widget-container' ).length !== 0 ? true : false;
		const buttonDismissed = hasStreamingWidget();
		const pendingMessageDisplayed = $( 'p.monetize-msg' ).length !== 0 ? true : false;
		const paywallDisplayed = $( '.coil-message-container' ).length !== 0 ? true : false;
		if ( buttonEnabled && ! buttonAlreadyExists && ! buttonDismissed && ! pendingMessageDisplayed && ! paywallDisplayed ) {
			showStreamingWidget( streamingWidgetUnpaidMessage );
		}
	}

	/**
	 * Ensures that the margin value assigned to the streaming support widget has an integer value as expected.
	 * @param {String} marginValue from coilParams.
	 * @return {String} A string containing only digits and possibly a minus sign.
	 */
	function checkMarginValues( marginValue ) {
		// If the value is invalid simply set it to 0.
		if ( marginValue.search( /[^1234567890-]/i ) >= 0 ) {
			return '0';
		}
		return marginValue;
	}

	/**
	 * Show the content container.
	 */
	function showContentContainer() {
		const container = document.querySelector( contentContainer );

		if ( container ) {
			container.style.display = 'block';
		}
	}

	/**
	 * Show the content container.
	*/
	function showRestrictedContent() {
		const restrictedContent = document.querySelector( '.coil-restricted-content' );

		if ( restrictedContent ) {
			restrictedContent.style.display = 'block';
		}
	}

	/**
	 * Hide the content container.
	 */
	function hideContentContainer() {
		const container = document.querySelector( contentContainer );

		if ( container ) {
			container.style.display = 'none';
		}
	}

	/**
	 * @return {(null|object)} Get the post excerpt, if available.
	*/
	function getContentExcerpt() {
		if ( postExcerpt === '' ) {
			return;
		}

		const msgContainer = document.createElement( 'div' );
		msgContainer.classList.add( 'entry-content', 'coil-message-container' );
		const excerptContainer = document.createElement( 'p' );
		excerptContainer.classList.add( 'coil-post-excerpt' );
		excerptContainer.innerHTML = postExcerpt;
		$( msgContainer ).append( excerptContainer );
		return msgContainer;
	}

	/**
	 * @return {bool} Helper function to determine if the content has
	 * monetization enabled and is visible to everyone
	*/
	function isMonetizedAndPublic() {
		const isMonetized = document.body.classList.contains( 'coil-monetized' );
		const isPublic = document.body.classList.contains( 'coil-public' );
		return isMonetized && isPublic;
	}

	/**
	 * @return {bool} Helper function to determine if the content has
	 * monetization enabled and is visible to Coil members only
	 */
	function isSubscribersOnly() {
		return document.body.classList.contains( 'coil-exclusive' );
	}

	/**
	 * @return {bool} Helper function to determine if the content has
	 * the streaming support widget enabled
	*/
	function hasStreamingWidgetEnabled() {
		return streamingWidgetGloballyEnabled && document.body.classList.contains( 'show-streaming-support-widget' );
	}

	/**
	 * @return {bool} Helper function to determine if the payment pointer is not
	 * set on the body.
	 */
	function isPaymentPointerMissing() {
		return document.body.classList.contains( 'coil-missing-id' );
	}

	/**
	 * @return {bool} Helper function to determine if the user is logged in.
	 */
	function isViewingAdmin() {
		return document.body.classList.contains( 'coil-show-admin-notice' );
	}

	/**
	 * @return {bool} Determine if the content container is default.
	 */
	function usingDefaultContentContainer() {
		return contentContainer === '.content-area .entry-content, main .entry-content';
	}

	/**
	 * @return {bool} Determine if the excerpt is set to show for this post.
	 */
	function isExcerptEnabled() {
		return ( document.body.classList.contains( 'coil-show-excerpt' ) ) ? true : false;
	}

	/**
	 * Displays a message based on the body classes and verification outcome.
	 */
	function showVerificationFailureMessage() {
		if ( $( 'p.monetize-msg' ).length > 0 ) {
			$( 'p.monetize-msg' ).remove();

			if ( isSubscribersOnly() ) {
				if ( hasCoilDivider ) {
					document.body.classList.add( 'show-fw-message' );
					$( '.coil-restricted-content' ).before( showSubscriberOnlyMessage( paywallMessage ) );
				} else if ( isExcerptEnabled() && getContentExcerpt() !== null ) {
					if ( $( '.coil-post-excerpt' ).length === 0 ) {
						$( contentContainer ).before( getContentExcerpt() );
					}
					$( contentContainer ).last().before( showSubscriberOnlyMessage( paywallMessage ) );
				} else {
					document.body.classList.add( 'show-fw-message' );
					$( contentContainer ).before( showSubscriberOnlyMessage( paywallMessage ) );
				}
			} else {
				// No tagged blocks.
				document.body.classList.add( 'show-fw-message' );
				document.querySelector( contentContainer ).before( showSubscriberOnlyMessage( paywallMessage ) );
			}
		}
	}

	/**
	 * Checks class is missing on <body>.
	 *
	 * @return {bool} Determine if Coil is initialized.
	 */
	function monetizationInitialized() {
		return ! document.body.classList.contains( 'monetization-not-initialized' );
	}

	/**
	 * Add a function to remove the streaming support widget and set a Cookie.
	 *
	 * @see https://github.com/js-cookie/js-cookie
	 */
	function addButtonDismissClickHandler() {
		const cookieName = 'ShowStreamingWidgetMsg';
		$( '#js-streaming-support-widget-dismiss' ).on( 'click', function() {
			if ( ! hasStreamingWidget() ) {
				Cookies.set( cookieName, 1, { expires: 14 } );
				$( this ).parent().parent().remove();
			}
		} );
	}

	/**
	 * Add a function to show or hide the streaming support widget dismiss
	 * depending on whether you are hovering over the widget or not.
	 *
	 */
	function addWidgetDismissAppearanceHandler() {
		$( '.streaming-support-widget' ).hover(
			function() {
				$( '#js-streaming-support-widget-dismiss' ).css( 'display', 'block' );
			}, function() {
				$( '#js-streaming-support-widget-dismiss' ).css( 'display', 'none' );
			},
		);
	}

	/**
	 * Checks if the streaming support widget is dismissed.
	 *
	 * @return {bool} True if set to '1', otherwise false.
	 */
	function hasStreamingWidget() {
		const cookieName = 'ShowStreamingWidgetMsg';
		const currentCookie = Cookies.get( cookieName );

		if ( ( typeof currentCookie !== 'undefined' ) ) {
			return ( currentCookie === '1' ) ? true : false;
		}
		return false;
	}

	/**
	 * Handles an undefined monetization object.
	 *
	 * @return {void}
	 */
	function handleUndefinedMonetization() {
		// Skip if we're testing in Cypress; we can't easily reset the app state from the changes made here.
		if ( window.Cypress && window.Cypress.monetized ) {
			return;
		}

		// Update body class to show free content.
		$( 'body' ).removeClass( 'monetization-not-initialized' ).addClass( 'coil-extension-not-found' );

		if ( isSubscribersOnly() ) {
			if ( hasCoilDivider ) {
				document.body.classList.add( 'show-fw-message' );
				$( '.coil-restricted-content' ).before( showSubscriberOnlyMessage( paywallMessage ) );
			} else if ( isExcerptEnabled() && getContentExcerpt() !== null ) {
				document.body.classList.add( 'show-excerpt-message' );
				$( contentContainer ).before( getContentExcerpt() );
				$( contentContainer ).last().before( showSubscriberOnlyMessage( paywallMessage ) );
			} else {
				document.body.classList.add( 'show-fw-message' );
				$( contentContainer ).last().before( showSubscriberOnlyMessage( paywallMessage ) );
			}
		} else {
			maybeAddStreamingWidget();
		}

		// Trigger an event.
		$( 'body' ).trigger( 'coil-extension-not-found' );
	}

	/**
	 * Handles the 'pending' monetization state.
	 *
	 * @return {void}
	 */
	function handlePendingMonetization() {
		// If the site is missing it's payment pointer ID.
		if ( isPaymentPointerMissing() ) {
			if ( isViewingAdmin() ) {
				if ( hasContentContainer() ) { // Since this code is reachable with an invalid CSS selector an additional check is required.
					// Show message to site owner/administrators only.
					$( contentContainer ).before( showMonetizationMessage( adminMissingIdNotice, 'admin-message' ) );
				}
			} else {
				$( 'body' ).removeClass( 'coil-missing-id' );
			}

			// This ensures content written in Gutenberg is displayed according to
			// the block settings should the theme use different theme selectors.
			if ( ! isMonetizedAndPublic() && ! usingDefaultContentContainer() ) {
				showContentContainer();
			}

			$( 'body' ).trigger( 'coil-missing-id' );
		} else if ( ! isMonetizedAndPublic() ) {
			// Verify monetization only if there is exclusive content.
			// If post is exclusive then show verification message after excerpt.
			if ( isSubscribersOnly() && hasCoilDivider && $( 'p.monetize-msg' ).length === 0 ) {
				$( '.coil-restricted-content' ).after( showMonetizationMessage( loadingContent, '' ) );
			} else if ( isSubscribersOnly() && isExcerptEnabled() && getContentExcerpt() !== null ) {
				document.body.classList.add( 'show-excerpt-message' );
				$( contentContainer ).before( getContentExcerpt() );
				$( contentContainer ).last().before( showMonetizationMessage( loadingContent, '' ) );
			} else {
				document.querySelector( contentContainer ).before( showMonetizationMessage( loadingContent, '' ) );
			}
			// Update message if browser extension is unable to verify user.
			setTimeout( function() {
				showVerificationFailureMessage();
			}, 5000 );
		}
	}

	/**
	 * Handles the 'started' monetization state.
	 *
	 * @return {void}
	 */
	function handleStartedMonetization() {
		// User account verified, loading content. Monetization state: Started

		if ( isSubscribersOnly() && hasCoilDivider && $( 'p.monetize-msg' ).length === 0 ) {
			$( '.coil-restricted-content' ).after( showMonetizationMessage( loadingContent, '' ) );
		} else if ( isSubscribersOnly() && isExcerptEnabled() && getContentExcerpt() !== null ) {
			document.body.classList.add( 'show-excerpt-message' );
			if ( $( '.coil-post-excerpt' ).length === 0 ) {
				$( contentContainer ).before( getContentExcerpt() );
			}
			if ( $( 'p.monetize-msg' ).length === 0 ) {
				$( contentContainer ).last().before( showMonetizationMessage( loadingContent, '' ) );
			}
		} else if ( hasContentContainer() ) { // Since this code is reachable with an invalid CSS selector an additional check is required.
			document.querySelector( contentContainer ).before( showMonetizationMessage( loadingContent, '' ) );
		}
	}

	/**
	 * Handles the 'stopped' monetization state.
	 *
	 * @return {void}
	 */
	function handleStoppedMonetization() {
		if ( isSubscribersOnly() && hasCoilDivider && $( 'p.monetize-msg' ).length === 0 ) {
			$( '.coil-restricted-content' ).after( showMonetizationMessage( loadingContent, '' ) );
		} else if ( isSubscribersOnly() && isExcerptEnabled() && getContentExcerpt() !== null ) {
			hideContentContainer();
			document.body.classList.add( 'show-excerpt-message' );
			if ( $( '.coil-post-excerpt' ).length === 0 ) {
				$( contentContainer ).before( getContentExcerpt() );
			}
			if ( $( 'p.monetize-msg' ).length === 0 ) {
				$( contentContainer ).last().before( showMonetizationMessage( loadingContent, '' ) );
			}
		} else if ( isSubscribersOnly() ) {
			hideContentContainer();
			if ( $( 'p.monetize-msg' ).length === 0 ) {
				$( contentContainer ).before( showMonetizationMessage( loadingContent, '' ) );
			}
		}

		setTimeout( function() {
			// If the payment connection event listeners haven't yet been
			// initialised, display failure message
			if ( monetizationStartEventOccurred === false ) {
				if ( $( 'p.monetize-msg' ).text() === loadingContent ) {
					// Monetization not started and verification failed.
					showVerificationFailureMessage();
				}
			}
		}, 5000 );

		maybeAddStreamingWidget();
	}

	/**
	 * The listener callback for monetization starting.
	 *
	 * @param {object} event The monetizationstart event
	 *
	 * @return {void}
	 */
	function monetizationStartListener( event ) {
		monetizationStartEventOccurred = true;
		let brandingLogo = '';

		if ( document.body.classList.contains( 'show-fw-message' ) ) {
			$( 'body' ).removeClass( 'show-fw-message' );
		}

		$( 'body' ).removeClass( 'monetization-not-initialized' ).addClass( 'monetization-initialized' ); // Update body class to show content.
		messageWrapper.remove(); // Remove status message.

		if ( isExcerptEnabled() ) {
			$( 'p.coil-post-excerpt' ).remove(); // Remove post excerpt.
		}

		// Removes exclusive messages
		if ( isSubscribersOnly() ) {
			$( '.entry-content.coil-message-container' ).remove();
		}

		showContentContainer();
		showRestrictedContent();

		// Show embedded content.
		document.querySelectorAll( 'iframe, object, video' ).forEach( function( embed ) {
			// Skip embeds we want to ignore
			if ( embed.classList.contains( 'intrinsic-ignore' ) || embed.parentNode.classList.contains( 'intrinsic-ignore' ) ) {
				return true;
			}

			if ( ! embed.dataset.origwidth ) {
				// Get the embed element proportions
				embed.setAttribute( 'data-origwidth', embed.width );
				embed.setAttribute( 'data-origheight', embed.height );
			}
		} );

		$( 'body' ).trigger( 'coil-monetization-initialized', [ event ] );

		// Monetization is verified, remove any messages
		if ( $( 'p.monetize-msg' ).length > 0 ) {
			$( 'p.monetize-msg' ).remove();
		}

		// Manually triggering resize to ensure elements get sized corretly after the verification proccess has been completed and they are no longer hidden.
		jQuery( window ).trigger( 'resize' );

		if ( ! showStreamingWidgetToMembers ) {
			$( '.streaming-widget-container' ).remove();
		} else {
			const buttonEnabled = hasStreamingWidgetEnabled();
			const buttonAlreadyExists = $( '.streaming-widget-container' ).length !== 0 ? true : false;
			const buttonDismissed = hasStreamingWidget();
			if ( streamingWidgetTheme === 'light' ) {
				brandingLogo = coilLogoStreaming;
			} else {
				brandingLogo = coilLogoWhiteStreaming;
			}

			if ( buttonEnabled && ! buttonDismissed ) {
				if ( buttonAlreadyExists ) {
					// The text needs to change to the member message
					$( '.streaming-support-widget div' ).text( streamingWidgetPaidMessage );
				} else {
					showStreamingWidget( streamingWidgetPaidMessage );
				}
				$( '.streaming-support-widget a' ).removeAttr( 'href' ).css( 'cursor', 'default' );
				$( '.streaming-support-widget a' ).css( 'cursor', 'default' );
				$( '.streaming-support-widget a img' ).attr( 'src', brandingLogo );
			}
		}
	}

	/**
	 * The listener callback for monetization progress.
	 * @param {object} event The monetizationprogress event
	 *
	 * @return {void}
	 */
	function monetizationProgressListener( event ) {
		// Connect to backend to validate the payment.
		const paymentPointer = event.detail.paymentPointer,
			requestId = event.detail.requestId,
			amount = event.detail.amount,
			assetCode = event.detail.assetCode,
			assetScale = event.detail.assetScale;

		// Trigger an event.
		$( 'body' ).trigger( 'coil-monetization-progress', [
			event,
			paymentPointer,
			requestId,
			amount,
			assetCode,
			assetScale,
		] );
	}

	/**
	 * Init monetissation process.
	 */
	function bootstrapCoil() {
		// Bail early - monetization initialised successfully.
		if ( monetizationInitialized() ) {
			return;
		}

		// Hide content entry area if not default selector.
		if ( ! isMonetizedAndPublic() && ! usingDefaultContentContainer() ) {
			if ( hasCoilDivider ) {
				$( contentContainer + ' .coil-restricted-content' ).hide();
			} else {
				$( contentContainer ).not( '.coil-post-excerpt' ).hide();
			}
		}

		// Check if browser extension exists.
		if ( typeof document.monetization === 'undefined' ) {
			handleUndefinedMonetization();
			return;
		}

		switch ( document.monetization.state ) {
			case 'pending':
				handlePendingMonetization();
				break;

			case 'started':
				handleStartedMonetization();
				break;

			case 'stopped':
				handleStoppedMonetization();
				break;
		}

		// Monetization has started.
		document.monetization.addEventListener( 'monetizationstart', monetizationStartListener );

		// Monetization progress event.
		document.monetization.addEventListener( 'monetizationprogress', monetizationProgressListener );
	}

	/**
	 * Init.
	 */
	$( document ).ready( function() {
		document.addEventListener( 'coilstart', monetizationStartListener ); // For Cypress.
		bootstrapCoil();
	} );
}( jQuery ) );
