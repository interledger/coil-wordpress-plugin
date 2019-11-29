(function($) {
	if ( typeof coil_params === 'undefined' ) {
		return false;
	}

	var content_container = coil_params.content_container,
		browser_extension_missing = coil_params.browser_extension_missing,
		unable_to_verify = coil_params.unable_to_verify,
		voluntary_donation = coil_params.voluntary_donation,
		loading_content = coil_params.loading_content,
		post_excerpt = coil_params.post_excerpt,
		admin_missing_id_notice = coil_params.admin_missing_id_notice;
		site_logo = coil_params.site_logo;

	var subscriberOnlyMessage = wp.template( 'subscriber-only-message' );
	var splitContentMessage = wp.template( 'split-content-message' );
	var bannerMessage = wp.template( 'banner-message' );

	var messageWrapper = $( 'p.monetize-msg' );
	var DEBUG_LOG = true;

	function displayDebugMessage( debug_message ) {
		if ( false === DEBUG_LOG ) {
			return;
		}
		console.info( debug_message );
	}

	// Function to output monetization message.
	function displayMonetizationMessage( message, customClass ) {
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
	 * set to Subscriber Only
	 *
	 * @param string Message from coil_params
	 */
	function displaySubscriberOnlyMessage( message ) {
		var modalContainer = document.createElement( 'div' );
		modalContainer.classList.add( 'entry-content', 'coil-message-container' );

		var modalData = {
			headerLogo: site_logo,
			title: 'This content is for subscribers only',
			content: message,
			button: {
				text: 'Get coil to access',
				href: 'https://coil.com/learn-more/'
			},
		};

		$( modalContainer ).append( subscriberOnlyMessage( modalData ) );
		return modalContainer;
	}

	/**
	 * Output a slim banner message
	 *
	 * @param string Message from coil_params
	 */
	function displayBannerMessage( message ) {
		var modalContainer = document.createElement( 'div' );
		modalContainer.classList.add( 'coil-banner-message-container' );

		var modalData = {
			content: message,
			button: {
				text: 'Get Coil to access',
				href: 'https://coil.com/learn-more/'
			},
		};

		$( modalContainer ).append( bannerMessage( modalData ) );
		return modalContainer;
	}

	/**
	 * Overlay "Split Content" blocks with a message when set to
	 * show for monetized users. This will display if the browser is
	 * not compatible or verified.
	 * @param string Message from coil_params
	 */
	function displaySplitContentMessage( message ) {
		return splitContentMessage( message );
	}

	/**
	 * Show the content container.
	 */
	function showContentContainer() {
		displayDebugMessage( 'Showing content container' );
		var elem = document.querySelector( content_container );
		elem.style.display = 'block';
	}

	/**
	 * Get the post excerpt, if available.
	 */
	function getContentExcerpt() {
		if ( post_excerpt === "" ) {
			return;
		}
		return jQuery('<p>').addClass( 'coil-post-excerpt' ).text( post_excerpt ).prop( 'outerHTML' );
	}

	/**
	 * Hide the post excerpt.
	 */
	function hideContentExcerpt() {
		return jQuery('p.coil-post-excerpt').hide();
	}

	/**
	 * Helper function to determine if the content is "Monetized and Public"
	 */
	function isMonetizedAndPublic() {
		return document.body.classList.contains( 'coil-no-gating' );
	}

	/**
	 * Helper function to determine if the content is "Subscribers Only"
	 */
	function isSubscribersOnly() {
		return document.body.classList.contains( 'coil-gate-all' );
	}

	/**
	 * Helper function to determine if the content is "Split Content"
	 */
	function isSplitContent() {
		return document.body.classList.contains( 'coil-gate-tagged-blocks' );
	}

	/**
	 * Determine if the content container is default.
	 */
	function usingDefaultContentContainer() {
		return content_container === '.content-area .entry-content';
	}

	/**
	 * Determine if the excerpt is set to show for this post.
	 */
	function isExcerptEnabled() {
		return ( document.body.classList.contains( 'coil-show-excerpt' ) ) ? true : false;
	}

	/**
	 * Displays a message based on the body classes and verification outcome.
	 */
	function displayVerificationFailureMessage() {

		displayDebugMessage( 'Verification Failure' );

		if ( $( 'p.monetize-msg' ).length > 0 ) {

			$( 'p.monetize-msg' ).remove();

			if ( isSubscribersOnly() ) {

				// Does the content have an excerpt?
				var contentExcerpt = $( 'p.coil-post-excerpt' );
				if ( contentExcerpt.length > 0 ) {
					contentExcerpt.after( displayMonetizationMessage( unable_to_verify, 'monetize-failed' ) );
				} else {

					document.body.classList.add('show-fw-message');
					$( content_container ).before( displaySubscriberOnlyMessage( unable_to_verify ) );
				}

			} else {
				if ( isSplitContent() ) {

					$( 'body' ).addClass( 'coil-split' ); // Only show content that is free if we can't verify.
					showContentContainer();

					// document.querySelector( content_container ).before( displayMonetizationMessage( unable_to_verify, 'monetize-failed' ) );
					document.querySelector( content_container ).before( displayMonetizationMessage( unable_to_verify, 'monetize-failed' ) );


					displayDebugMessage( 'Unable to verify hidden content' );

				} else {

					document.body.classList.add('show-fw-message');
					document.querySelector( content_container ).before( displaySubscriberOnlyMessage( unable_to_verify ) );
					displayDebugMessage( 'No tagged blocks' );

				}
			}
		}
	}

	/**
	 * Detect class on the body if coil is not yet initialized
	 *
	 * @return bool
	 */
	function monetizationNotInitialized() {
		return document.body.classList.contains( 'monetization-not-initialized' );
	}


	/**
	 * Add a function to remove the banner and set a Cookie
	 *
	 * @param int expiresInDays Define when the cookie will be removed.
	 * @see https://github.com/js-cookie/js-cookie
	 */
	function addBannerDismissClickHandler( expiresInDays ) {
		$('#js-coil-banner-dismiss').on( 'click', function(event) {
			if ( ! Cookies.get( 'ShowCoilPartialMsg' ) == 1 ) {
				if ( false !== expiresInDays ) {
					Cookies.set( 'ShowCoilPartialMsg', 1, { expires: expiresInDays } );
				} else {
					Cookies.set( 'ShowCoilPartialMsg', 1 );
				}
				$(this).parent().parent().remove();
			}
		});

	}

	/**
	 * Init
	 */
	$( document ).ready(function () {

		if ( monetizationNotInitialized() ) {

			// Display post excerpt for gated posts.
			if ( isSubscribersOnly() && typeof post_excerpt !== 'undefined' && typeof document.monetization !== 'undefined' && document.monetization.state !== 'stopped' ) {
				if ( post_excerpt !== '' ) {
					document.querySelector( content_container ).before( getContentExcerpt() );
				}
			}

			// Hide content entry area if not default selector.
			if ( ! isMonetizedAndPublic() && ! usingDefaultContentContainer() ) {
				$( content_container ).not( '.coil-post-excerpt' ).hide();
			}

			// Check if browser extension exists.
			if ( typeof document.monetization !== 'undefined' ) {

				// User might be paying, hold on a second.
				if ( document.monetization.state === 'pending' ) {

					// If the site is missing it's payment pointer ID.
					if ( $( 'body' ).hasClass( 'coil-missing-id' ) ) {

						if ( $( 'body' ).hasClass( 'coil-show-admin-notice' ) ) {
							// Show message to site owner/administrators only.
							$( content_container ).before( '<p class="monetize-msg">' + admin_missing_id_notice + '</p>' );
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
							// If post is gated then show verification message after excerpt.
							if ( isSubscribersOnly() ) {
								if ( post_excerpt !== '' ) {
									displayDebugMessage( 'Subscriber gating and no post excerpt...Verifying extension' );
									document.querySelector( content_container ).before( displayMonetizationMessage( loading_content, '' ) );
								} else {
									displayDebugMessage( 'Subscriber gating and has post excerpt...Verifying extension' );
									$( 'div.coil-post-excerpt' ).after( displayMonetizationMessage( loading_content, '' ) );
								}
							} else {
								document.querySelector( content_container ).before( displayMonetizationMessage( loading_content, '' ) );
							}

							// Update message if browser extension is verifying user.
							setTimeout( function() {
								displayDebugMessage( 'Verifying Coil account' );
								messageWrapper.html( loading_content );
							}, 2000 );

							// Update message if browser extension is unable to verify user.
							setTimeout( function() {
								displayVerificationFailureMessage();
							}, 5000 );
						}
					}
				}
				// User account verified, loading content.
				else if ( document.monetization.state === 'started' ) {
					displayDebugMessage( 'Monetization state: Started' );
					document.querySelector( content_container ).before( displayMonetizationMessage( loading_content, '' ) );
				}
				// Final check to see if the state is stopped
				else if ( document.monetization.state === 'stopped' ) {

					displayDebugMessage( 'Status stopped and Monetized and Public' );

					if ( isSubscribersOnly() ) {

						// Hide content


						if ( isExcerptEnabled() ) {
							if ( post_excerpt !== '' ) {
								document.querySelector( content_container ).insertAdjacentHTML( 'beforebegin', getContentExcerpt() );
							}
						}
						document.querySelector( content_container ).insertAdjacentHTML( 'beforebegin', '<p class="monetize-msg">' + loading_content + '</p>' );

					} else if ( isSplitContent() ) {

						document.querySelector( content_container ).insertAdjacentHTML( 'beforebegin', '<p class="monetize-msg">' + loading_content + '</p>' );

					}

					setTimeout( function() {

						// If the payment connection event listeners haven't yet been
						// initialised, display failure message
						if ( typeof monetizationStartEventOccurred === 'undefined' ) {

							if ( $( 'p.monetize-msg' ).text() === loading_content ) {
								// Update message if browser extension is unable to verify user.
								displayDebugMessage( 'Monetization not started and verification failed' );
								displayVerificationFailureMessage();

							} else if ( isMonetizedAndPublic() ) {
								// Voluntary donation.
								displayDebugMessage( 'Content is monetized and public but extension is stopped' );
								if ( ! Cookies.get( 'ShowCoilPartialMsg' ) == 1 ) {
									$('body').append( displayBannerMessage( voluntary_donation ) );
								}
								addBannerDismissClickHandler( 31 );
							}
						}
					}, 5000 );
				}

				// Monetization has started.
				document.monetization.addEventListener( 'monetizationstart', function(event) {
					// Connect to backend to validate the session using the request id.
					var paymentPointer = event.detail.paymentPointer,
						requestId      = event.detail.requestId;

					monetizationStartEventOccurred = true;

					if ( ! isMonetizedAndPublic() && ! usingDefaultContentContainer() ) {
						showContentContainer();
						document.body.classList.remove('show-fw-message');
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

				});

				// Monetization progress event.
				document.monetization.addEventListener( 'monetizationprogress', function(event) {
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
				});

			} else {

				/* Coil Extension not found or using unsupported browser */
				displayDebugMessage( 'Coil Extension not found or using unsupported browser' );

				// Update body class to show free content.
				$( 'body' ).removeClass( 'monetization-not-initialized' ).addClass( 'coil-extension-not-found' );

				if ( isSubscribersOnly() ) {

					if ( ! usingDefaultContentContainer() ) {

						$( content_container ).html( displaySubscriberOnlyMessage( browser_extension_missing ) );
					} else {
						$( content_container ).before( displaySubscriberOnlyMessage( browser_extension_missing ) );
					}


					if ( isExcerptEnabled() ) {
						document.body.classList.add('show-excerpt-message');
						$( content_container ).prepend( getContentExcerpt() );
					} else {
						document.body.classList.add('show-fw-message');
					}

				} else if ( isSplitContent() ) {
					displayDebugMessage( 'Split content with no extension found' );

					if ( ! Cookies.get( 'ShowCoilPartialMsg' ) == 1 ) {
						$('body').append( displayBannerMessage( 'This content is for Coil subscribers only.  To access, subscribe to Coil and install the browser extension.' ) );
					}

					$( '.coil-show-monetize-users' ).prepend( displaySplitContentMessage( 'This content is for Coil subscribers only! To access, visit coil.com and install the browser extension!' ) );
					addBannerDismissClickHandler( false );


				} else if ( isMonetizedAndPublic() ) {
					// Voluntary donation.
					displayDebugMessage( 'Content is monetized and public but no extension found' );

					if ( ! Cookies.get( 'ShowCoilPartialMsg' ) == 1 ) {
						$('body').append( displayBannerMessage( voluntary_donation ) );
					}
					addBannerDismissClickHandler( 31 );
				}

				// Trigger an event.
				$( 'body' ).trigger( 'coil-extension-not-found' );

			}
		}

	});
})(jQuery);
