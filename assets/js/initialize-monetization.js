(function($) {
	var content_container         = coil_params.content_container,
		browser_extension_missing = coil_params.browser_extension_missing,
		unable_to_verify          = coil_params.unable_to_verify,
		voluntary_donation        = coil_params.voluntary_donation,
		partial_block_gating      = coil_params.partial_block_gating,
		loading_content           = coil_params.loading_content,
		post_excerpt              = coil_params.post_excerpt,
		admin_missing_id_notice   = coil_params.admin_missing_id_notice;

	var messageWrapper = $( 'p.monetize-msg' );
	var DEBUG_LOG = true;

	// Ensure "coil_params" exists to continue.
	if ( typeof coil_params === 'undefined' ) {
		return false;
	}

	function displayDebugMessage( debug_message ) {
		if ( false === DEBUG_LOG ) {
			return;
		}
		console.info( debug_message );
	}

	// Function to output monetization message.
	function displayMonetizationMessage( message, customClass ) {
		var elem = document.createElement( 'p' );
		elem.classList.add('monetize-msg');
		if ( customClass ) {
			elem.classList.add(customClass);
		}
		elem.innerHTML = message;
		return elem;
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
	 * Hide the content container.
	 */
	function hideContentContainer() {
		displayDebugMessage( 'Hiding content container' );
		var elem = document.querySelector( content_container );
		elem.style.display = 'none';
	}

	/**
	 * Helper function to determine if the content is "Monetized and Public"
	 */
	function isMonetizedAndPublic() {
		return document.body.classList.contains('coil-no-gating');
	}

	/**
	 * Helper function to determine if the content is "Monetized and Public"
	 */
	function isSubscribersOnly() {
		return document.body.classList.contains('coil-gate-all');
	}

	/**
	 * Helper function to determine if the content is "Split Content"
	 */
	function isSplitContent() {
		return document.body.classList.contains('coil-gate-tagged-blocks');
	}

	/**
	 * Determine if the content container is default.
	 */
	function usingDefaultContentContainer() {
		return content_container === '.content-area .entry-content';
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
				var contentExcerpt = $( 'div.coil-post-excerpt' );
				if ( contentExcerpt.length > 0 ) {
					contentExcerpt.after( displayMonetizationMessage( unable_to_verify, 'monetize-failed' ) );
				} else {
					$( content_container ).before( displayMonetizationMessage( unable_to_verify, 'monetize-failed' ) );
				}

			} else {
				if ( isSplitContent() ) {

					$( 'body').addClass( 'coil-split' ); // Only show content that is free if we can't verify.
					showContentContainer();
					$( content_container ).before( displayMonetizationMessage( unable_to_verify, 'monetize-failed' ) );

					displayDebugMessage( 'Unable to verify hidden content' );

				} else {

					$( content_container ).before( displayMonetizationMessage( unable_to_verify, 'monetize-failed' ) );

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
		return document.body.classList.contains('monetization-not-initialized');
	}

	/**
	 * Init
	 */
	$( document ).ready(function () {

		if ( monetizationNotInitialized() ) {

			// Display post excerpt for gated posts.
			if ( isSubscribersOnly() && typeof post_excerpt !== 'undefined' && typeof document.monetization !== 'undefined' && document.monetization.state !== 'stopped' ) {
				$( content_container ).before( '<div class="entry-content coil-post-excerpt"><p>' + post_excerpt + '</p></div>' );
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
							$( content_container ).before('<p class="monetize-msg">' + admin_missing_id_notice + '</p>');
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
								if ( post_excerpt !== 'undefined' ) {
									displayDebugMessage( 'Subscriber gating and no post excerpt...Verifying extension' );
									$( content_container ).before( displayMonetizationMessage( loading_content, '' ) );
								} else {
									displayDebugMessage( 'Subscriber gating and has post excerpt...Verifying extension' );
									$( 'div.coil-post-excerpt' ).after( displayMonetizationMessage( loading_content, '' ) );
								}
							} else {
								$( content_container ).before( displayMonetizationMessage( loading_content, '' ) );
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
					$( content_container ).before( displayMonetizationMessage( loading_content, '' ) );
				}
				// Final check to see if the state is stopped
				else if ( document.monetization.state === 'stopped' ) {

					// Only display the loading message if the status is not "monetized and public".
					if ( ! isMonetizedAndPublic() ) {
						displayDebugMessage( 'Status stopped and Monetized and Public' );
						$( content_container ).before( displayMonetizationMessage( loading_content, '' ) );
					}

					setTimeout( function() {

						// If the payment connection event listeners haven't yet been
						// initialised, display failure message
						if ( typeof monetizationStartEventOccurred === 'undefined' ) {

							// Failure
							displayDebugMessage( 'Monetization not started and verification failed' );

							// Update message if browser extension is unable to verify user.
							if ( $( 'p.monetize-msg' ).text() === loading_content ) {
								displayVerificationFailureMessage();
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
					}

					$( 'body' ).removeClass( 'monetization-not-initialized' ).addClass( 'monetization-initialized' ); // Update body class to show content.
					messageWrapper.remove(); // Remove status message.
					$( 'div.coil-post-excerpt' ).remove(); // Remove post excerpt.

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

				if ( isSubscribersOnly() || ! isMonetizedAndPublic() ) {

					var postExcerptDiv =  $( 'div.coil-post-excerpt' );
					var entryContentDiv =  $( 'div.entry-content' );

					if ( postExcerptDiv.length ) {
						postExcerptDiv.after( displayMonetizationMessage( browser_extension_missing, '' ) );
					} else if ( entryContentDiv.length ) {
						entryContentDiv.before( displayMonetizationMessage( browser_extension_missing, '' ) );
					}
				}

				// This ensures content written in Gutenberg is displayed according to
				// the block settings should the theme use different theme selectors.
				if ( ! isMonetizedAndPublic() ) {

					if( ! usingDefaultContentContainer() ) {
						showContentContainer();
						$( content_container + '*.coil-hide-monetize-users' ).css( 'display', 'none' );
						$( content_container + '*.coil-show-monetize-users' ).css( 'display', 'none' );
					}

				} else {
					displayDebugMessage( 'Content is monetized and public but no extension found' );
				}

				// Trigger an event.
				$( 'body' ).trigger( 'coil-extension-not-found' );

			}
		}

	});
})(jQuery);
