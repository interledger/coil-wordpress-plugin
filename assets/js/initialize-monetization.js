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

	// Ensure "coil_params" exists to continue.
	if ( typeof coil_params === 'undefined' ) {
		return false;
	}

	$( document ).ready(function () {
		var content  = ( typeof content_container !== 'undefined' && content_container !== '' ) ? content_container : '.content-area .entry-content'; // If not set, use default entry content container class.
		var is_valid = true;

		// If post is set for monetization then we run some magic.
		if ( $( 'body' ).hasClass( 'monetization-not-initialized' ) ) {

			// Display post excerpt for gated posts.
			if ( $( 'body' ).hasClass( 'coil-gate-all' ) && typeof post_excerpt !== 'undefined' ) {
				$( content ).before( '<div class="entry-content coil-post-excerpt"><p>' + post_excerpt + '</p></div>' );
			}

			// Hide content entry area if not default selector.
			if ( ! $( 'body' ).hasClass( 'coil-no-gating' ) && content !== '.content-area .entry-content' ) {
				$( content ).not( '.coil-post-excerpt' ).hide();
			}

			// Check if browser extension exists.
			if ( document.monetization ) {

				// User might be paying, hold on a second.
				if ( document.monetization.state === 'pending' ) {

					// If the site is missing it's payment pointer ID.
					if ( $( 'body' ).hasClass( 'coil-missing-id' ) ) {

						if ( $( 'body' ).hasClass( 'coil-show-admin-notice' ) ) {
							// Show message to site owner/administrators only.
							$( content ).before('<p class="monetize-msg">' + admin_missing_id_notice + '</p>');
						} else {
							$( 'body' ).removeClass( 'coil-missing-id' );
						}

						// This ensures content written in Gutenberg is displayed according to
						// the block settings should the theme use different theme selectors.
						//
						// TODO: Paul thinks this string can't be hardcoded.
						if ( ! $( 'body' ).hasClass( 'coil-no-gating' ) && content !== '.content-area .entry-content' ) {
							$( content ).css( 'display', '' );
							$( content + '*.coil-hide-monetize-users' ).css( 'display', 'none' );
							$( content + '*.coil-show-monetize-users' ).css( 'display', 'none' );
						}

						$( 'body' ).trigger( 'coil-missing-id' );

					} else {
						// Verify monetization only if we are gating or partially gating content.
						if ( ! $( 'body' ).hasClass( 'coil-no-gating' ) ) {
							// If post is gated then show verification message after excerpt.
							if ( $( 'body' ).hasClass( 'coil-gate-all' ) ) {
								if ( post_excerpt !== 'undefined' ) {
									$( content ).before( '<p class="monetize-msg">' + verifying_browser_extension + '</p>' );
								} else {
									$( 'div.coil-post-excerpt' ).after( '<p class="monetize-msg">' + verifying_browser_extension + '</p>' );
								}
							} else {
								$( content ).before( '<p class="monetize-msg">' + verifying_browser_extension + '</p>' );
							}

							var message = $( 'p.monetize-msg' );

							// Update message if browser extension is verifying user.
							setTimeout( function() {
								$( 'p.monetize-msg' ).html( verifying_coil_account );
							}, 2000 );

							// Update message if browser extension is unable to verify user.
							setTimeout( function() {
								if ( $( 'p.monetize-msg' ).length > 0 ) {
									$( 'p.monetize-msg' ).remove();

									if ( $( 'body' ).hasClass( 'coil-gate-all' ) ) {
										$( 'div.coil-post-excerpt' ).after( '<p class="monetize-failed">' + unable_to_verify + '</p>' );
									} else {
										if ( $( 'body').hasClass( 'coil-gate-tagged-blocks' ) ) {
											$( 'body').addClass( 'coil-split' ); // Only show content that is free if we can't verify.
											$( content ).css( 'display', '' );
											$( content ).before( '<p class="monetize-failed">' + unable_to_verify_hidden + '</p>' );

										} else {
											$( content ).before( '<p class="monetize-failed">' + unable_to_verify + '</p>' );
										}
									}

								}
							}, 5000 );
						}
					}
				}
				// User account verified, loading content.
				else if ( document.monetization.state === 'started' ) {

					$( content ).before('<p class="monetize-msg">' + loading_content + '</p>' );
				}

				// Monetization has started.
				document.monetization.addEventListener( 'monetizationstart', function(event) {
					// Connect to backend to validate the session using the request id.
					var paymentPointer = event.detail.paymentPointer,
						requestId      = event.detail.requestId;

					monetizationStartEventOccurred = true;

					if ( ! $( 'body' ).hasClass( 'coil-no-gating' ) && content !== '.content-area .entry-content' ) {
						$( content ).css( 'display', '' ); // Show content area if not default selector.
					}

					$( 'body' ).removeClass( 'monetization-not-initialized' ).addClass( 'monetization-initialized' ); // Update body class to show content.
					$( 'p.monetize-msg' ).remove(); // Remove status message.
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
				$( 'body' ).removeClass( 'monetization-not-initialized' ).addClass( 'coil-extension-not-found' ); // Update body class to show free content.

				if ( $( 'body' ).hasClass( 'coil-gate-all' ) ) {
					$( 'div.coil-post-excerpt' ).after( '<p class="monetize-msg">' + browser_extension_missing + '</p>' );
				} else {
					$( content ).before( '<p class="monetize-msg">' + browser_extension_missing + '</p>' );
				}

				// This ensures content written in Gutenberg is displayed according to
				// the block settings should the theme use different theme selectors.
				//
				// TODO: Paul thinks this string can't be hardcoded.
				if ( ! $( 'body' ).hasClass( 'coil-no-gating' ) && content !== '.content-area .entry-content' ) {
					$( content ).css( 'display', '' );
					$( content + '*.coil-hide-monetize-users' ).css( 'display', 'none' );
					$( content + '*.coil-show-monetize-users' ).css( 'display', 'none' );
				}

				// Trigger an event.
				$( 'body' ).trigger( 'coil-extension-not-found' );

			}
		}

	});
})(jQuery);
