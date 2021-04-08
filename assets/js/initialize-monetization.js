( function( $ ) {
	if ( typeof coil_params === 'undefined' || ! hasContentContainer() ) {
		return false;
	}

	const contentContainer = coil_params.content_container,
		browserExtensionMissing = coil_params.browser_extension_missing,
		unableToVerify = coil_params.unable_to_verify,
		voluntaryDonation = coil_params.voluntary_donation,
		loadingContent = coil_params.loading_content,
		partialGating = coil_params.partial_gating,
		postExcerpt = coil_params.post_excerpt,
		adminMissingIdNotice = coil_params.admin_missing_id_notice,
		learnMoreButtonText = coil_params.learn_more_button_text,
		learnMoreButtonLink = coil_params.learn_more_button_link,
		siteLogo = coil_params.site_logo,
		showDonationBar = Boolean( coil_params.show_donation_bar ); // Cast to boolean - wp_localize_script forces string values.

	const subscriberOnlyMessage = wp.template( 'subscriber-only-message' );
	const splitContentMessage = wp.template( 'split-content-message' );
	const bannerMessage = wp.template( 'banner-message' );

	const messageWrapper = $( 'p.monetize-msg' );

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
		try {
			element = document.querySelector( coil_params.content_container );
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
	 * @param {String} message from coil_params.
	 * @return {object} Output the gated content message when content is
	 * set to Member Only
	 */
	function showSubscriberOnlyMessage( message ) {
		const modalContainer = document.createElement( 'div' );
		modalContainer.classList.add( 'entry-content', 'coil-message-container' );

		const modalData = {
			headerLogo: siteLogo,
			title: 'This content is for Coil members only',
			content: message,
			button: {
				text: learnMoreButtonText,
				href: learnMoreButtonLink,
			},
		};

		$( modalContainer ).append( subscriberOnlyMessage( modalData ) );
		return modalContainer;
	}

	/**
	 * @param {String} message from coil_params.
	 * @return {object} Output a slim banner message.
	 */
	function showBannerMessage( message ) {
		const modalContainer = document.createElement( 'div' );
		modalContainer.classList.add( 'coil-banner-message-container' );

		const modalData = {
			content: message,
			button: {
				text: learnMoreButtonText,
				href: learnMoreButtonLink,
			},
		};

		$( modalContainer ).append( bannerMessage( modalData ) );
		return modalContainer;
	}

	/**
	 * @param {String} message from coil_params.
	 * @return {object} Overlay "Split Content" blocks with a message when set to
	 * show for monetized users. This will display if the browser is
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
	 * @return {bool} Helper function to determine if the content is "Monetized and Public"
	 */
	function isMonetizedAndPublic() {
		return document.body.classList.contains( 'coil-no-gating' );
	}

	/**
	 * @return {bool} Helper function to determine if the content is "Coil Members Only"
	 */
	function isSubscribersOnly() {
		return document.body.classList.contains( 'coil-gate-all' );
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
	 * @return {bool} Helper function to determine if the content is "Split Content"
	 */
	function isSplitContent() {
		return document.body.classList.contains( 'coil-gate-tagged-blocks' );
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
					$( contentContainer ).before( showSubscriberOnlyMessage( unableToVerify ) );
					$( contentContainer ).prepend( getContentExcerpt() );
				} else {
					document.body.classList.add( 'show-fw-message' );
					$( contentContainer ).before( showSubscriberOnlyMessage( unableToVerify ) );
				}
			} else if ( isSplitContent() ) {
				// Split content and unable to verify hidden content.
				$( '.coil-show-monetize-users' ).prepend( showSplitContentMessage( unableToVerify ) );

				// Show non-Coil-members content.
				// Removing class means blocks revert to their *original* display values.
				$( '.coil-hide-monetize-users' ).removeClass( 'coil-hide-monetize-users' );

				showContentContainer();

				if ( showDonationBar && ! hasBannerDismissCookie( 'ShowCoilPublicMsg' ) ) {
					$( 'body' ).append( showBannerMessage( voluntaryDonation ) );
					addBannerDismissClickHandler( 'ShowCoilPublicMsg' );
				}
			} else {
				// No tagged blocks.
				document.body.classList.add( 'show-fw-message' );
				document.querySelector( contentContainer ).before( showSubscriberOnlyMessage( unableToVerify ) );
			}
		}
	}

	/**
	 * Checks for class on <body>.
	 *
	 * @return {bool} Determine if Coil is not yet initialized.
	 */
	function monetizationNotInitialized() {
		return document.body.classList.contains( 'monetization-not-initialized' );
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
	 * Add a function to remove the banner and set a Cookie.
	 *
	 * @param {int} expiresInDays Define when the cookie will be removed.
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
		} );
	}

	/**
	 * Checks if the footer banner message is dismissed.
	 *
	 * @param {string} cookieName Name of the cookie to check against.
	 *
	 * @return {bool} True if set to '1', otherwise false.
	 */
	function hasBannerDismissCookie( cookieName ) {
		const currentCookie = Cookies.get( cookieName );

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
			$( contentContainer ).before( showSubscriberOnlyMessage( browserExtensionMissing ) );

			if ( isExcerptEnabled() && getContentExcerpt() ) {
				document.body.classList.add( 'show-excerpt-message' );
				$( contentContainer ).prepend( getContentExcerpt() );
			} else {
				document.body.classList.add( 'show-fw-message' );
			}
		} else if ( isSplitContent() ) {
			// Split content with no extension found.
			$( '.coil-show-monetize-users' ).prepend( showSplitContentMessage( partialGating ) );

			// Show non-coil-members content.
			// Removing class means blocks revert to their *original* display values.
			$( '.coil-hide-monetize-users' ).removeClass( 'coil-hide-monetize-users' );

			showContentContainer();

			if ( showDonationBar && ! hasBannerDismissCookie( 'ShowCoilPublicMsg' ) ) {
				$( 'body' ).append( showBannerMessage( voluntaryDonation ) );
				addBannerDismissClickHandler( 'ShowCoilPublicMsg' );
			}
		} else if ( isMonetizedAndPublic() ) {
			// Content is monetized and public but no extension found.

			if ( showDonationBar && ! hasBannerDismissCookie( 'ShowCoilPublicMsg' ) ) {
				$( 'body' ).append( showBannerMessage( voluntaryDonation ) );
				addBannerDismissClickHandler( 'ShowCoilPublicMsg' );
			}
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
			// If post is gated then show verification message after excerpt.
			if ( isSubscribersOnly() ) {
				if ( isExcerptEnabled() ) {
					// Subscriber gating and no post excerpt...Verifying extension.
					document.querySelector( contentContainer ).before( showMonetizationMessage( loadingContent, '' ) );
				} else {
					// Subscriber gating and has post excerpt...Verifying extension.
					$( 'p.coil-post-excerpt' ).after( showMonetizationMessage( loadingContent, '' ) );
				}
			} else {
				document.querySelector( contentContainer ).before( showMonetizationMessage( loadingContent, '' ) );
			}

			// Update message if browser extension is verifying user.
			setTimeout( function() {
				hideContentExcerpt();
				messageWrapper.html( loadingContent );
			}, 2000 );

			// Update message if browser extension is unable to verify user.
			setTimeout( function() {
				showVerificationFailureMessage();
			}, 5000 );
		} else if ( showDonationBar && monetizationNotInitialized() && ! hasBannerDismissCookie( 'ShowCoilPublicMsg' ) ) {
			$( 'body' ).append( showBannerMessage( voluntaryDonation ) );
			addBannerDismissClickHandler( 'ShowCoilPublicMsg' );
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
			if ( typeof monetizationStartEventOccurred === 'undefined' ) {
				if ( $( 'p.monetize-msg' ).text() === loadingContent ) {
					// Monetization not started and verification failed.
					showVerificationFailureMessage();
				} else if ( isMonetizedAndPublic() ) {
					// Content is monetized and public but extension is stopped.
					if ( showDonationBar && ! hasBannerDismissCookie( 'ShowCoilPublicMsg' ) ) {
						$( 'body' ).append( showBannerMessage( voluntaryDonation ) );
						addBannerDismissClickHandler( 'ShowCoilPublicMsg' );
					}
				}
			}
		}, 5000 );
	}

	/**
	 * The listener callback for monetization starting.
	 *
	 * @return {void}
	 */
	function monetizationStartListener( event ) {
		monetizationStartEventOccurred = true;

		if ( ! isMonetizedAndPublic() && ! usingDefaultContentContainer() ) {
			showContentContainer();
			document.body.classList.remove( 'show-fw-message' );
			if ( isExcerptEnabled() ) {
				hideContentExcerpt();
			}
		}

		$( 'body' ).removeClass( 'monetization-not-initialized' ).addClass( 'monetization-initialized' ); // Update body class to show content.
		messageWrapper.remove(); // Remove status message.

		if ( ! isExcerptEnabled() ) {
			$( 'div.coil-post-excerpt' ).remove(); // Remove post excerpt.
		}

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
	 * @return {void}
	 */
	function monetizationProgressListener( event ) {
		// Connect to backend to validate the payment.
		let paymentPointer = event.detail.paymentPointer,
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
		document.addEventListener( 'coilstart', bootstrapCoil ); // For Cypress.
		bootstrapCoil();
	} );
}( jQuery ) );
