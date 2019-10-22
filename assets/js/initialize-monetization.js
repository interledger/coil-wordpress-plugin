(function($) {
	var version                     = coil_params.coil_for_wp_version,
		content_container           = coil_params.content_container,
		verifying_browser_extension = coil_params.verifying_browser_extension,
		browser_extension_missing   = coil_params.browser_extension_missing,
		verifying_coil_account      = coil_params.verifying_coil_account,
		loading_content             = coil_params.loading_content;

	// Ensure "coil_params" exists to continue.
	if ( typeof coil_params === 'undefined' ) {
		return false;
	}

	$( document ).ready(function () {
		console.log( 'Coil for WordPress version: ' + version );

		var content  = ( typeof content_container !== 'undefined' ) ? content_container : '.content-area .entry-content'; // If not set, use default entry content container class.
		var is_valid = true;

		// If post is set for monetization then we run some magic.
		if ( $('body').hasClass('monetization-not-initialized') ) {

			if ( content !== '.content-area .entry-content' ) { $( content ).hide(); } // Hide content entry area if not default selector.
			$( content ).before('<p class="monetize-msg" style="text-align:center;">' + verifying_browser_extension + '</p>');

			// Check if monetization is implemented.
			if ( document.monetization ) {

				// User might be paying you, hold on a second.
				if ( document.monetization.state === 'pending' ) {
					// Update message if browser extension is still verifying user.
					setTimeout( function() {
						$('p.monetize-msg').html( verifying_coil_account );
					}, 2000 );
				}
				// User account verified, loading content.
				else if ( document.monetization.state === 'started' ) {
					$('p.monetize-msg').html( loading_content );
				}

				// Monetization has started.
				document.monetization.addEventListener( 'monetizationstart', function(event) {
					// Connect to backend to validate the session using the request id.
					var paymentPointer = event.detail.paymentPointer,
						requestId      = event.detail.requestId;

					console.log(event.detail); // All event details.

					// @todo: Add validation condition here.
					/*if ( ! isValidSession( paymentPointer, requestId ) ) {
						is_valid = false;
						console.error( 'Invalid requestId for monetization.' );

						// Trigger an event.
						$( 'body' ).trigger( 'coil-invalid-session', [ event, paymentPointer, requestId ] );
					} else {*/
						monetizationStartEventOccurred = true;

						console.log( 'Monetization has started. Yeah!' );

						if ( content !== '.content-area' ) { $( content ).show(); } // Show content area if not default selector.
						$('body').removeClass('monetization-not-initialized').addClass('monetization-initialized'); // Update body class to show content.
						$('p.monetize-msg').remove(); // Remove status message.
	
						// Trigger an event.
						$( 'body' ).trigger( 'coil-monetization-initialized', [ event ] );
					//}
				});

				// Monetization progress event.
				document.monetization.addEventListener( 'monetizationprogress', function(event) {
					// Connect to backend to validate the payment.
					var paymentPointer = event.detail.paymentPointer,
						requestId      = event.detail.requestId,
						amount         = event.detail.amount,
						assetCode      = event.detail.assetCode,
						assetScale     = event.detail.assetScale;

					// @todo: Add validation condition here.
					//if ( isValidPayment(paymentPointer, requestId, amount, assetCode, assetScale) ) {
						// A payment has been received.
						console.log('monetizationprogress', event);

						// Trigger an event.
						$( 'body' ).trigger( 'coil-monetization-progress', [
							event,
							paymentPointer,
							requestId,
							amount,
							assetCode,
							assetScale
						] );
					//}
				});
			} else {
				$('body').addClass('coil-extension-not-found'); // Update body class to show free content.

				$('p.monetize-msg').html( browser_extension_missing );

				// Trigger an event.
				$( 'body' ).trigger( 'coil-extension-not-found' );
			}
		} // END if post is ready for monetization.

		// If the site is missing it's payment pointer ID.
		if ( $('body').hasClass('coil-missing-id') ) {
			if ( $('body').hasClass('coil-show-admin-notice') ) {
				// Show message to site owner/administrators only.
				$( content ).before('<p class="monetize-msg">Sebastien, this post is monetized but you have not set your payment pointer ID in the <a href="#">Coil settings page</a>. Only content set to show for all visitors will show.</p>');
			}

			// This ensures content written in Gutenberg is displayed according to 
			// the block settings should the theme use different theme selectors.
			if ( content !== '.content-area .entry-content' ) {
				$( content ).css( 'display', 'initial' );
				$( content + '*.coil-hide-monetize-users' ).css( 'display', 'none' );
				$( content + '*.coil-show-monetize-users' ).css( 'display', 'none' );

				// Trigger an event.
				$( 'body' ).trigger( 'coil-missing-id' );
		}
		} // END if missing ID

	});
})(jQuery);