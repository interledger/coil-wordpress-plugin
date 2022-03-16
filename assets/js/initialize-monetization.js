/* global Cookies */
/* global coilParams */

( function( $ ) {
	if ( typeof coilParams === 'undefined' || ! hasContentContainer() ) {
		return false;
	}

	const contentContainer = coilParams.content_container,
		paywallTitle = coilParams.paywall_title,
		loadingContent = coilParams.loading_content,
		paywallMessage = coilParams.paywall_message,
		coilButtonUnpaidMessage = coilParams.coil_button_unpaid_message,
		coilButtonPaidMessage = coilParams.coil_button_paid_message,
		showCoilButtonToMembers = Boolean( coilParams.show_coil_button_to_members ),
		coilButtonLink = coilParams.coil_button_link,
		postExcerpt = coilParams.post_excerpt,
		adminMissingIdNotice = coilParams.admin_missing_id_notice,
		paywallButtonText = coilParams.paywall_button_text,
		paywallButtonLink = coilParams.paywall_button_link,
		coilMessageBranding = coilParams.coil_message_branding,
		coilButtonTheme = coilParams.coil_button_theme,
		coilButtonSize = coilParams.coil_button_size,
		coilButtonPosition = coilParams.coil_button_position,
		ButtonMarginTop = coilParams.button_margin_top,
		ButtonMarginRight = coilParams.button_margin_right,
		ButtonMarginBottom = coilParams.button_margin_bottom,
		ButtonMarginLeft = coilParams.button_margin_left,
		coilButtonGloballyEnabled = Boolean( coilParams.coil_button_enabled ), // Cast to boolean - wp_localize_script forces string values.
		siteLogo = coilParams.site_logo,
		coilLogo = coilParams.coil_logo,
		coilLogoStreaming = coilParams.coil_logo_streaming,
		coilLogoWhite = coilParams.coil_logo_white,
		coilLogoWhiteStreaming = coilParams.coil_logo_white_streaming,
		exclusiveMessageTheme = coilParams.exclusive_message_theme,
		fontSelection = Boolean( coilParams.font_selection );

	const subscriberOnlyMessage = wp.template( 'subscriber-only-message' );
	const splitContentMessage = wp.template( 'split-content-message' );
	const coilButtonMessage = wp.template( 'coil-button-message' );

	const messageWrapper = $( 'p.monetize-msg' );

	let monetizationStartEventOccurred = false;

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
	 * Adds the Coil button to the body oif the document and adds it's handler functions.
	 * @param {String} message Message shown to thank Coil members, or to encourage users to sign up.
	 * @return {void}
	*/
	function showCoilButton( message ) {
		const coilButton = createCoilButton( message );
		$( 'body' ).append( coilButton );
		addButtonDismissClickHandler();
		addButtonDismissAppearanceHandler();
	}

	/**
	 * @param {String} message Message shown to thank Coil members, or to encourage users to sign up.
	 * @return {object} Output a Coil button message.
	*/
	function createCoilButton( message ) {
		const positionArray = coilButtonPosition.split( '-' );
		const verticalPosition = positionArray[ 0 ];
		const horizontalPosition = positionArray[ 1 ];

		const modalContainer = document.createElement( 'div' );
		$( modalContainer ).addClass( 'coil-button-message-container' + ' ' + verticalPosition + ' ' + horizontalPosition );

		let brandingLogo = '';

		if ( coilButtonTheme === 'light' ) {
			modalContainer.classList.add( 'coil-light-theme' );
			brandingLogo = coilLogo;
		} else {
			brandingLogo = coilLogoWhite;
		}

		if ( coilButtonSize === 'small' ) {
			modalContainer.classList.add( 'coil-button-small' );
		}

		const modalData = {
			headerLogo: brandingLogo,
			button: {
				text: message,
				href: coilButtonLink,
			},
		};

		$( modalContainer ).append( coilButtonMessage( modalData ) );

		const topMargin = checkMarginValues( ButtonMarginTop );
		const rightMargin = checkMarginValues( ButtonMarginRight );
		const bottomMargin = checkMarginValues( ButtonMarginBottom );
		const leftMargin = checkMarginValues( ButtonMarginLeft );

		$( modalContainer ).find( '.coil-button' ).css( { 'margin-top': topMargin + 'px', 'margin-right': rightMargin + 'px', 'margin-bottom': bottomMargin + 'px', 'margin-left': leftMargin + 'px' } );
		return modalContainer;
	}

	/**
	 * Determines whether a Coil button element should be added to the page.
	 * The button will only be added if it is not already present, it is globally enabled, the browser doesn't have a dismiss cookie for it,
	 * and neither the pending message nor paywall are being displayed.
	 */
	function maybeAddCoilButton() {
		// TODO shouldn't display if the read more block is present
		const buttonEnabled = hasCoilButtonEnabled();
		const buttonAlreadyExists = $( '.coil-button-message-container' ).length !== 0 ? true : false;
		const buttonDismissed = hasButtonDismissCookie();
		const pendingMessageDisplayed = $( 'p.monetize-msg' ).length !== 0 ? true : false;
		const paywallDisplayed = $( '.coil-message-container' ).length !== 0 ? true : false;
		if ( buttonEnabled && ! buttonAlreadyExists && ! buttonDismissed && ! pendingMessageDisplayed && ! paywallDisplayed ) {
			showCoilButton( coilButtonUnpaidMessage );
		}
	}

	/**
	 * Ensures that the margin value assigned to the Coil button has an integer value as expected.
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
	 * @param {String} message from coilParams.
	 * @return {object} Overlay "Split Content" blocks with a message when set to
	 * Only Show Coil Members. This will display if the browser is
	 * not compatible or verified.
	 */
	function showSplitContentMessage( message ) {
		return splitContentMessage( message );
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

		const excerptContainer = document.createElement( 'p' );
		excerptContainer.classList.add( 'coil-post-excerpt' );
		excerptContainer.innerHTML = postExcerpt;
		return excerptContainer;
	}

	/**
	 * @return {object} Hide the post excerpt.
	 */
	function hideContentExcerpt() {
		return jQuery( 'p.coil-post-excerpt' ).remove();
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
	 * the Coil button enabled
	*/
	function hasCoilButtonEnabled() {
		return coilButtonGloballyEnabled && document.body.classList.contains( 'show-coil-button' );
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
	 * @return {bool} Helper function to determine if the content is "Split"
	 */
	function isSplitContent() {
		const isMonetized = document.body.classList.contains( 'coil-monetized' );
		const isSplit = document.body.classList.contains( 'coil-gate-tagged-blocks' );
		return isMonetized && isSplit;
	}

	/**
	 * @return {bool} Determine if the content container is default.
	 */
	function usingDefaultContentContainer() {
		return contentContainer === '.content-area .entry-content';
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
				if ( isExcerptEnabled() && getContentExcerpt() ) {
					document.body.classList.add( 'show-excerpt-message' );
					$( contentContainer ).before( showSubscriberOnlyMessage( paywallMessage ) );
					$( contentContainer ).prepend( getContentExcerpt() );
				} else {
					document.body.classList.add( 'show-fw-message' );
					$( contentContainer ).before( showSubscriberOnlyMessage( paywallMessage ) );
				}
			} else if ( isSplitContent() ) {
				// Split content and unable to verify hidden content.
				$( '.coil-show-monetize-users' ).prepend( showSplitContentMessage( paywallMessage ) );
				$( '.coil-show-monetize-users' ).css( 'display', 'block' );

				showContentContainer();
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
	 * Add a function to remove the Coil button and set a Cookie.
	 *
	 * @see https://github.com/js-cookie/js-cookie
	 */
	function addButtonDismissClickHandler() {
		const cookieName = 'ShowCoilButtonMsg';
		$( '#js-coil-button-dismiss' ).on( 'click', function() {
			if ( ! hasButtonDismissCookie() ) {
				Cookies.set( cookieName, 1, { expires: 14 } );
				$( this ).parent().parent().remove();
			}
		} );
	}

	/**
	 * Add a function to show or hide the Coil button dismiss
	 * depending on whether you are hovering over the button or not.
	 *
	 */
	function addButtonDismissAppearanceHandler() {
		$( '.coil-button' ).hover(
			function() {
				$( '#js-coil-button-dismiss' ).css( 'display', 'block' );
			}, function() {
				$( '#js-coil-button-dismiss' ).css( 'display', 'none' );
			},
		);
	}

	/**
	 * Checks if the Coil button is dismissed.
	 *
	 * @return {bool} True if set to '1', otherwise false.
	 */
	function hasButtonDismissCookie() {
		const cookieName = 'ShowCoilButtonMsg';
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
			$( contentContainer ).before( showSubscriberOnlyMessage( paywallMessage ) );

			if ( isExcerptEnabled() && getContentExcerpt() ) {
				document.body.classList.add( 'show-excerpt-message' );
				$( contentContainer ).prepend( getContentExcerpt() );
			} else {
				document.body.classList.add( 'show-fw-message' );
			}
		} else if ( isSplitContent() ) {
			// Split content with no extension found.
			$( '.coil-show-monetize-users' ).prepend( showSplitContentMessage( paywallMessage ) );

			showContentContainer();
		} else {
			maybeAddCoilButton();
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
				// Show message to site owner/administrators only.
				$( contentContainer ).before( showMonetizationMessage( adminMissingIdNotice, 'admin-message' ) );
			} else {
				$( 'body' ).removeClass( 'coil-missing-id' );
			}

			// This ensures content written in Gutenberg is displayed according to
			// the block settings should the theme use different theme selectors.
			if ( ! isMonetizedAndPublic() && ! usingDefaultContentContainer() ) {
				showContentContainer();
				$( contentContainer + '*.coil-hide-monetize-users' ).css( 'display', 'none' );
				$( contentContainer + '*.coil-show-monetize-users' ).css( 'display', 'none' );
			}

			$( 'body' ).trigger( 'coil-missing-id' );
		} else if ( ! isMonetizedAndPublic() ) {
			// Verify monetization only if there is exclusive content.
			// If post is exclusive then show verification message after excerpt.
			if ( isSubscribersOnly() && isExcerptEnabled() ) {
				$( contentContainer ).before( getContentExcerpt() );
				$( contentContainer ).after( showMonetizationMessage( loadingContent, '' ) );
			} else {
				document.querySelector( contentContainer ).before( showMonetizationMessage( loadingContent, '' ) );
			}

			// Update message if browser extension is verifying user.
			setTimeout( function() {
				messageWrapper.html( loadingContent );
			}, 2000 );
			// Update message if browser extension is unable to verify user.
			setTimeout( function() {
				hideContentExcerpt();
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
		document.querySelector( contentContainer ).before( showMonetizationMessage( loadingContent, '' ) );
		$( '.coil-message-container' ).remove();
	}

	/**
	 * Handles the 'stopped' monetization state.
	 *
	 * @return {void}
	 */
	function handleStoppedMonetization() {
		if ( isSubscribersOnly() || isSplitContent() ) {
			hideContentExcerpt();
			hideContentContainer();
			document.querySelector( contentContainer ).before( showMonetizationMessage( loadingContent, '' ) );
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

		maybeAddCoilButton();
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
		} else if ( isSplitContent() ) {
			$( '.coil-show-monetize-users' ).removeClass( 'coil-show-monetize-users' );
			$( '.coil-split-content-message' ).remove();
			$( '*.coil-hide-monetize-users' ).css( 'display', 'none' );
		}

		showContentContainer();

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

		if ( ! showCoilButtonToMembers ) {
			$( '.coil-button-message-container' ).remove();
		} else {
			const buttonEnabled = hasCoilButtonEnabled();
			const buttonAlreadyExists = $( '.coil-button-message-container' ).length !== 0 ? true : false;
			const buttonDismissed = hasButtonDismissCookie();
			if ( coilButtonTheme === 'light' ) {
				brandingLogo = coilLogoStreaming;
			} else {
				brandingLogo = coilLogoWhiteStreaming;
			}

			if ( buttonEnabled && ! buttonDismissed ) {
				if ( buttonAlreadyExists ) {
					// The text needs to change to the member message
					$( '.coil-button div' ).text( coilButtonPaidMessage );
				} else {
					showCoilButton( coilButtonPaidMessage );
				}
				$( '.coil-button a' ).removeAttr( 'href' ).css( 'cursor', 'default' );
				$( '.coil-button a' ).css( 'cursor', 'default' );
				$( '.coil-button a img' ).attr( 'src', brandingLogo );
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
			$( contentContainer ).not( '.coil-post-excerpt' ).hide();
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
