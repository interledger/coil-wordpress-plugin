(function($) {
	var content_container         = coil_params.content_container,
		verifying_browser_extension = coil_params.verifying_browser_extension,
		browser_extension_missing   = coil_params.browser_extension_missing,
		verifying_coil_account      = coil_params.verifying_coil_account,
		unable_to_verify            = coil_params.unable_to_verify,
		unable_to_verify_hidden     = coil_params.unable_to_verify_hidden,
		loading_content             = coil_params.loading_content,
		admin_missing_id_notice     = coil_params.admin_missing_id_notice,
		post_excerpt                = coil_params.post_excerpt;

	var messageWrapper = $( 'p.monetize-msg' );

	// Ensure "coil_params" exists to continue.
	if ( typeof coil_params === 'undefined' ) {
		return false;
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

	function displayVerificationFailureMessage() {

		console.log('displayVerificationFailureMessage called');

		if ( $('p.monetize-msg').length > 0 ) {

			$('p.monetize-msg').remove();

			if ( $( 'body' ).hasClass( 'coil-gate-all' ) ) {

				// Does the content have an exerpt?
				var contentExcerpt = $( 'div.coil-post-excerpt' );
				if ( contentExcerpt.length > 0 ) {
					contentExcerpt.after( displayMonetizationMessage( unable_to_verify, 'monetize-failed' ) );
				} else {
					$( content_container ).before( displayMonetizationMessage( unable_to_verify, 'monetize-failed' ) );
				}

			} else {
				if ( $( 'body').hasClass( 'coil-gate-tagged-blocks' ) ) {

					$( 'body').addClass( 'coil-split' ); // Only show content that is free if we can't verify.
					$( content_container ).css( 'display', '' );
					$( content_container ).before( displayMonetizationMessage( unable_to_verify_hidden, 'monetize-failed' ) );

				} else {

					$( content_container ).before( displayMonetizationMessage( unable_to_verify, 'monetize-failed' ) );

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
			if ( $( 'body' ).hasClass( 'coil-gate-all' ) && typeof post_excerpt !== 'undefined' && typeof document.monetization !== 'undefined' && document.monetization.state !== 'stopped' ) {
				$( content_container ).before( '<div class="entry-content coil-post-excerpt"><p>' + post_excerpt + '</p></div>' );
			}

			// Hide content entry area if not default selector.
			if ( ! $( 'body' ).hasClass( 'coil-no-gating' ) && content_container !== '.content-area .entry-content' ) {
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
						//
						// TODO: Paul thinks this string can't be hardcoded.
						if ( ! $( 'body' ).hasClass( 'coil-no-gating' ) && content_container !== '.content-area .entry-content' ) {
							$( content_container ).css( 'display', '' );
							$( content_container + '*.coil-hide-monetize-users' ).css( 'display', 'none' );
							$( content_container + '*.coil-show-monetize-users' ).css( 'display', 'none' );
						}

						$( 'body' ).trigger( 'coil-missing-id' );

					} else {
						// Verify monetization only if we are gating or partially gating content.
						if ( ! $( 'body' ).hasClass( 'coil-no-gating' ) ) {
							// If post is gated then show verification message after excerpt.
							if ( $( 'body' ).hasClass( 'coil-gate-all' ) ) {
								if ( post_excerpt !== 'undefined' ) {
									console.info( 'Subscriber gating and no post excerpt');

									$( content_container ).before( displayMonetizationMessage( verifying_browser_extension, '' ) );
								} else {
									console.info( 'Subscriber gating and has post excerpt');
									$( 'div.coil-post-excerpt' ).after( displayMonetizationMessage( verifying_browser_extension, '' ) );
								}
							} else {
								$( content_container ).before( displayMonetizationMessage( verifying_browser_extension, '' ) );
							}

							// Update message if browser extension is verifying user.
							setTimeout( function() {
								messageWrapper.html( verifying_coil_account );
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
					// $( content_container ).before('<p class="monetize-msg">' + loading_content + '</p>' );
					$( content_container ).before( displayMonetizationMessage( loading_content, '' ) );

					console.info('Monetization state: Started');

				}
				// Final check to see if the state is stopped
				else if ( document.monetization.state === 'stopped' ) {

					if ( $( 'body' ).hasClass( 'coil-no-gating' ) && content_container === '.content-area .entry-content' ) {
						$( content_container ).css( 'display', 'none' );
						$( content_container + '*.coil-hide-monetize-users' ).css( 'display', 'none' );
						$( content_container + '*.coil-show-monetize-users' ).css( 'display', 'none' );
					}

					$( content_container ).before( displayMonetizationMessage( loading_content, '' ) );

					setTimeout( function() {

						// If the payment connection event listeners haven't yet been
						// initialised, display failure message
						if ( typeof monetizationStartEventOccurred === 'undefined' ) {

							// Failure
							console.info( 'Monetization not started and verification failed');

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

					if ( ! $( 'body' ).hasClass( 'coil-no-gating' ) && content_container !== '.content-area .entry-content' ) {
						$( content_container ).css( 'display', '' ); // Show content area if not default selector.
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
				$( 'body' ).removeClass( 'monetization-not-initialized' ).addClass( 'coil-extension-not-found' ); // Update body class to show free content.

				if ( $( 'body' ).hasClass( 'coil-gate-all' ) || ! $( 'body' ).hasClass( 'coil-no-gating') ) {

					var postExcerptDiv =  $( 'div.coil-post-excerpt' );
					var entryContentDiv =  $( 'div.entry-content' );

					if ( postExcerptDiv.length ) {
						postExcerptDiv.after( displayMonetizationMessage( browser_extension_missing, '' ) );
					} else if ( entryContentDiv.length ) {
						entryContentDiv.before( displayMonetizationMessage( browser_extension_missing, '' ) );
					}

				} else {
					// $( content_container ).before( '<p class="monetize-msg">' + browser_extension_missing + '</p>' );
				}

				// This ensures content written in Gutenberg is displayed according to
				// the block settings should the theme use different theme selectors.
				//
				// TODO: Paul thinks this string can't be hardcoded.
				if ( ! $( 'body' ).hasClass( 'coil-no-gating' ) && content_container !== '.content-area .entry-content' ) {
					$( content_container ).css( 'display', '' );
					$( content_container + '*.coil-hide-monetize-users' ).css( 'display', 'none' );
					$( content_container + '*.coil-show-monetize-users' ).css( 'display', 'none' );
				}

				// Trigger an event.
				$( 'body' ).trigger( 'coil-extension-not-found' );

			}
		}

	});
})(jQuery);
