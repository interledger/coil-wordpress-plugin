(function($) {
	if ( typeof coil_params === 'undefined' || ! hasContentContainer() ) {
		return false;
	}

	var content_container = coil_params.content_container,
		browser_extension_missing = coil_params.browser_extension_missing,
		unable_to_verify = coil_params.unable_to_verify,
		voluntary_donation = coil_params.voluntary_donation,
		loading_content = coil_params.loading_content,
		partial_gating = coil_params.partial_gating,
		post_excerpt = coil_params.post_excerpt,
		admin_missing_id_notice = coil_params.admin_missing_id_notice,
		learn_more_button_text = coil_params.learn_more_button_text,
		learn_more_button_link = coil_params.learn_more_button_link,
		site_logo = coil_params.site_logo,
		show_donation_bar = Boolean( coil_params.show_donation_bar ); // Cast to boolean - wp_localize_script forces string values.

	var subscriberOnlyMessage = wp.template( 'subscriber-only-message' );
	var splitContentMessage = wp.template( 'split-content-message' );
	var bannerMessage = wp.template( 'banner-message' );

	var messageWrapper = $( 'p.monetize-msg' );


	/**
	 * Check the content container element exists.
	 *
	 * Returns false if the container element doesn't exist OR an invalid CSS
	 * selector was used to define it.
	 *
	 * @return bool
	 */
	function hasContentContainer() {
		var element;

		// Use try-catch to handle invalid CSS selectors.
		try {
			element = document.querySelector( coil_params.content_container );
		} catch ( e ) {
			console.log( 'An error occured when attempting to retrieve the page. Invalid container.' );
			return false;
		}

		// Content container exists.
		if ( element ) {
			return true;
		}

		// Content container does not exist.
		console.log( 'An error occured when attempting to retrieve the page. Container not found.' );
		return false;
	}

	/**
	 * Output a monetization message when the state is changing.
	 *
	 * @param string message The message to display inside the tag.
	 * @param string customClass Any extra custom classes to add to this tag.
	 */
	function showMonetizationMessage( message, customClass ) {
		var elem = document.createElement( 'p' );
		elem.classList.add( 'monetize-msg' );
		if ( customClass ) {
			elem.classList.add( customClass );
		}
		elem.innerHTML = message;
		return elem;
	}

	/**
	 * Output the gated content message when content is
	 * set to Member Only
	 *
	 * @param string Message from coil_params.
	 */
	function showSubscriberOnlyMessage( message ) {
		var modalContainer = document.createElement( 'div' );
		modalContainer.classList.add( 'entry-content', 'coil-message-container' );

		var modalData = {
			headerLogo: site_logo,
			title: 'This content is for Coil members only',
			content: message,
			button: {
				text: learn_more_button_text,
				href: learn_more_button_link
			}
		};

		$( modalContainer ).append( subscriberOnlyMessage( modalData ) );
		return modalContainer;
	}

	/**
	 * Output a slim banner message.
	 *
	 * @param string Message from coil_params.
	 */
	function showBannerMessage( message ) {
		var modalContainer = document.createElement( 'div' );
		modalContainer.classList.add( 'coil-banner-message-container' );

		var modalData = {
			content: message,
			button: {
				text: learn_more_button_text,
				href: learn_more_button_link
			},
		};

		$( modalContainer ).append( bannerMessage( modalData ) );
		return modalContainer;
	}

	/**
	 * Overlay "Split Content" blocks with a message when set to
	 * show for monetized users. This will display if the browser is
	 * not compatible or verified.
	 * @param string Message from coil_params.
	 */
	function showSplitContentMessage( message ) {
		return splitContentMessage( message );
	}

	/**
	 * Show the content container.
	 */
	function showContentContainer() {
		var container = document.querySelector( content_container );

		if ( container ) {
			container.style.display = 'block';
		}
	}

	/**
	 * Hide the content container.
	 */
	function hideContentContainer() {
		var container = document.querySelector( content_container );

		if ( container ) {
			container.style.display = 'none';
		}
	}

	/**
	 * Get the post excerpt, if available.
	 */
	function getContentExcerpt() {
		if ( post_excerpt === "" ) {
			return;
		}

		var excerptContainer = document.createElement( 'p' );
		excerptContainer.classList.add( 'coil-post-excerpt' );
		excerptContainer.innerHTML = post_excerpt;
		return excerptContainer;
	}

	/**
	 * Hide the post excerpt.
	 */
	function hideContentExcerpt() {
		return jQuery( 'p.coil-post-excerpt' ).remove();
	}

	/**
	 * Helper function to determine if the content is "Monetized and Public"
	 *
	 * @return bool
	 */
	function isMonetizedAndPublic() {
		return document.body.classList.contains( 'coil-no-gating' );
	}

	/**
	 * Helper function to determine if the content is "Coil Members Only"
	 *
	 * @return bool
	 */
	function isSubscribersOnly() {
		return document.body.classList.contains( 'coil-gate-all' );
	}

	/**
	 * Helper function to determine if the payment pointer is not
	 * set on the body.
	 *
	 * @return bool
	 */
	function isPaymentPointerMissing() {
		return document.body.classList.contains( 'coil-missing-id' );
	}

	/**
	 * Helper function to determine if the user is logged in.
	 *
	 * @return bool
	 */
	function isViewingAdmin() {
		return document.body.classList.contains( 'coil-show-admin-notice' );
	}

	/**
	 * Helper function to determine if the content is "Split Content"
	 *
	 * @return bool
	 */
	function isSplitContent() {
		return document.body.classList.contains( 'coil-gate-tagged-blocks' );
	}

	/**
	 * Determine if the content container is default.
	 *
	 * @return bool
	 */
	function usingDefaultContentContainer() {
		return content_container === '.content-area .entry-content';
	}

	/**
	 * Displays a message based on the body classes and verification outcome.
	 */
	function showVerificationFailureMessage() {

		// E.g. when content is loading then length > 0
		if ( $( 'p.monetize-msg' ).length > 0 ) {

			$( 'p.monetize-msg' ).remove();

			if ( isSubscribersOnly() ) {

				if ( getContentExcerpt() ) {

					document.body.classList.add( 'show-excerpt-message' );
					$( content_container ).before( showSubscriberOnlyMessage( unable_to_verify ) );
					$( content_container ).prepend( getContentExcerpt() );

				} else {

					document.body.classList.add( 'show-fw-message' );
					$( content_container ).before( showSubscriberOnlyMessage( unable_to_verify ) );
				}

			} else {

				if ( isSplitContent() ) {

					// Split content and unable to verify hidden content.
					$( '.coil-show-monetize-users' ).prepend( showSplitContentMessage( unable_to_verify ) );

					// Show non-Coil-members content.
					// Removing class means blocks revert to their *original* display values.
					$( '.coil-hide-monetize-users' ).removeClass('coil-hide-monetize-users');

					showContentContainer();

					if ( show_donation_bar && ! hasBannerDismissCookie( 'ShowCoilPublicMsg' ) ) {
						$( 'body' ).append( showBannerMessage( voluntary_donation ) );
						addBannerDismissClickHandler( 'ShowCoilPublicMsg' );
					}

				} else {

					// No tagged blocks.
					document.body.classList.add( 'show-fw-message' );
					document.querySelector( content_container ).before( showSubscriberOnlyMessage( unable_to_verify ) );

				}
			}
		}
	}

	/**
	 * Determine if Coil is not yet initialized.
	 *
	 * Checks for class on <body>.
	 *
	 * @return bool
	 */
	function monetizationNotInitialized() {
		return document.body.classList.contains( 'monetization-not-initialized' );
	}

	/**
	 * Determine if Coil is initialized.
	 *
	 * Checks class is missing on <body>.
	 *
	 * @return bool
	 */
	function monetizationInitialized() {
		return ! document.body.classList.contains( 'monetization-not-initialized' );
	}

	/**
	 * Add a function to remove the banner and set a Cookie.
	 *
	 * @param int expiresInDays Define when the cookie will be removed.
	 * @see https://github.com/js-cookie/js-cookie
	 */
	function addBannerDismissClickHandler( cookieName ) {
		$( '#js-coil-banner-dismiss' ).on( 'click', function() {
			if ( ! hasBannerDismissCookie( cookieName ) ) {
				if ( cookieName === 'ShowCoilPublicMsg' ) {
					Cookies.set( cookieName, 1, { expires: 31 } );
				} else if ( cookieName === 'ShowCoilPartialMsg' ) {
					Cookies.set( cookieName, 1 );
				}
				$( this ).parent().parent().remove();
			}
		});
	}

	/**
	 * Checks if the footer banner message is dismissed.
	 *
	 * @param string cookieName Name of the cookie to check against.
	 *
	 * @return bool. True if set to '1', otherwise false.
	 */
	function hasBannerDismissCookie( cookieName ) {
		var currentCookie = Cookies.get( cookieName );

		if ( ( typeof( currentCookie ) !== 'undefined' ) ) {
			if ( cookieName === 'ShowCoilPublicMsg' || cookieName === 'ShowCoilPartialMsg' ) {
				return ( currentCookie === '1' ) ? true : false;
			}
		}
		return false;
	}


	/**
	 * Handles an undefined monetization object.
	 *
	 * @return void.
	 */
	function handleUndefinedMonetization() {

		// Skip if we're testing in Cypress; we can't easily reset the app state from the changes made here.
		if ( window.Cypress && window.Cypress.monetized ) {
			return;
		}

		// Update body class to show only free content.
		$( 'body' ).removeClass( 'monetization-not-initialized' ).addClass( 'coil-extension-not-found' );

		if ( isSubscribersOnly() ) {

			$( content_container ).before( showSubscriberOnlyMessage( browser_extension_missing ) );

			if ( getContentExcerpt() ) {
				$( content_container ).prepend( getContentExcerpt() );
			} else {
				document.body.classList.add( 'show-fw-message' );
			}

		} else if ( isSplitContent() ) {

			// Split content with no extension found.
			$( '.coil-show-monetize-users' ).prepend( showSplitContentMessage( partial_gating ) );

			// Show non-coil-members content.
			// Removing class means blocks revert to their *original* display values.
			$( '.coil-hide-monetize-users' ).removeClass('coil-hide-monetize-users');

			showContentContainer();

			if ( show_donation_bar && ! hasBannerDismissCookie( 'ShowCoilPublicMsg' ) ) {
				$( 'body' ).append( showBannerMessage( voluntary_donation ) );
				addBannerDismissClickHandler( 'ShowCoilPublicMsg' );
			}

		} else if ( isMonetizedAndPublic() ) {

			// Content is monetized and public but no extension found.

			if ( show_donation_bar && ! hasBannerDismissCookie( 'ShowCoilPublicMsg' ) ) {
				$( 'body' ).append( showBannerMessage( voluntary_donation ) );
				addBannerDismissClickHandler( 'ShowCoilPublicMsg' );
			}
		}

		// Trigger an event.
		$( 'body' ).trigger( 'coil-extension-not-found' );

	}

	/**
	 * Handles the 'pending' monetization state.
	 *
	 * @return void.
	 */
	function handlePendingMonetization() {
		// If the site is missing it's payment pointer ID.
		if ( isPaymentPointerMissing() ) {

			if ( isViewingAdmin() ) {

				// Show message to site owner/administrators only.
				$( content_container ).before( showMonetizationMessage( admin_missing_id_notice, 'admin-message' ) );

			} else {
				$( 'body' ).removeClass( 'coil-missing-id' );
			}

			// This ensures content written in Gutenberg is displayed according to
			// the block settings should the theme use different theme selectors.
			if ( ! isMonetizedAndPublic() && ! usingDefaultContentContainer() ) {
				showContentContainer();
				$( content_container + '*.coil-hide-monetize-users' ).css( 'display', 'none' );
				$( content_container + '*.coil-show-monetize-users' ).css( 'display', 'none' );
			}

			$( 'body' ).trigger( 'coil-missing-id' );

		} else {

			// Verify monetization only if we are gating or partially gating content.
			if ( ! isMonetizedAndPublic() ) {

				// If post is gated then show verification message
				document.querySelector( content_container ).before( showMonetizationMessage( loading_content, '' ) );

				// Update message if browser extension is verifying user.
				setTimeout( function() {
					hideContentExcerpt();
					messageWrapper.html( loading_content );
				}, 2000 );

				// Update message if browser extension is unable to verify user.
				setTimeout( function() {
					showVerificationFailureMessage();
				}, 5000 );

			} else {

				if ( show_donation_bar && monetizationNotInitialized() && ! hasBannerDismissCookie( 'ShowCoilPublicMsg' ) ) {
					$( 'body' ).append( showBannerMessage( voluntary_donation ) );
					addBannerDismissClickHandler( 'ShowCoilPublicMsg' );
				}
			}
		}
	}

	/**
	 * Handles the 'started' monetization state.
	 *
	 * @return void.
	 */
	function handleStartedMonetization() {
		// User account verified, loading content. Monetization state: Started
		document.querySelector( content_container ).before( showMonetizationMessage( loading_content, '' ) );
	}

	/**
	 * Handles the 'stopped' monetization state.
	 *
	 * @return void.
	 */
	function handleStoppedMonetization() {
		if ( isSubscribersOnly() || isSplitContent() ) {
			hideContentExcerpt();
			hideContentContainer();
			document.querySelector( content_container ).before( showMonetizationMessage( loading_content, '' ) );
		}

		setTimeout( function() {

			// If the payment connection event listeners haven't yet been
			// initialised, display failure message
			if ( typeof monetizationStartEventOccurred === 'undefined' ) {

				if ( $( 'p.monetize-msg' ).text() === loading_content ) {

					// Monetization not started and verification failed.
					showVerificationFailureMessage();

				} else if ( isMonetizedAndPublic() ) {

					// Content is monetized and public but extension is stopped.
					if ( show_donation_bar && ! hasBannerDismissCookie( 'ShowCoilPublicMsg' ) ) {
						$( 'body' ).append( showBannerMessage( voluntary_donation ) );
						addBannerDismissClickHandler( 'ShowCoilPublicMsg' );
					}
				}
			}
		}, 5000 );
	}

	/**
	 * The listener callback for monetization starting.
	 *
	 * @return void.
	 */
	function monetizationStartListener(event) {
		monetizationStartEventOccurred = true;

		if ( ! isMonetizedAndPublic() && ! usingDefaultContentContainer() ) {
			showContentContainer();
			document.body.classList.remove( 'show-fw-message' );
			hideContentExcerpt();
		}

		$( 'body' ).removeClass( 'monetization-not-initialized' ).addClass( 'monetization-initialized' ); // Update body class to show content.
		messageWrapper.remove(); // Remove status message.

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
			hideContentExcerpt();
			showContentContainer();
		}
	}

	/**
	 * The listener callback for monetization progress.
	 *
	 * @return void.
	 */
	function monetizationProgressListener(event) {
		// Connect to backend to validate the payment.
		var paymentPointer = event.detail.paymentPointer,
			requestId      = event.detail.requestId,
			amount         = event.detail.amount,
			assetCode      = event.detail.assetCode,
			assetScale     = event.detail.assetScale;

		// Trigger an event.
		$( 'body' ).trigger( 'coil-monetization-progress', [
			event,
			paymentPointer,
			requestId,
			amount,
			assetCode,
			assetScale
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
			$( content_container ).not( '.coil-post-excerpt' ).hide();
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
		document.addEventListener( 'coilstart', bootstrapCoil );  // For Cypress.
		bootstrapCoil();
	} );
})(jQuery);
