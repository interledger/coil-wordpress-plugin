( function( $ ) {
	$( document ).ready( function() {
		const activeTabID = $( '.nav-tab-wrapper a.nav-tab-active' ).attr( 'id' );

		// Welcome notice
		if ( activeTabID === 'coil-getting-started' ) {
			const $welcomeNoticeDismissButton = $( '.coil-welcome-notice .notice-dismiss' );

			// No welcome notice on this screen.
			if ( ! $welcomeNoticeDismissButton ) {
				return;
			}

			if ( ! coil_admin_params || ! coil_admin_params.ajax_url ) {
				return;
			}

			// Fire ajax request to dismiss notice permanently.
			$welcomeNoticeDismissButton.on( 'click', function() {
				$.ajax( {
					url: coil_admin_params.ajax_url,
					type: 'POST',
					data: {
						action: 'dismiss_welcome_notice',
					},
				} );
			} );
		}

		// No payment pointer
		if ( activeTabID === 'coil-global-settings' ) {
			const noPaymentPointerNotice = $( '.coil-no-payment-pointer-notice' );
			if ( noPaymentPointerNotice.length > 0 ) {
				noPaymentPointerNotice.hide();

				const settingsUpdated = $( '#setting-error-settings_updated' );
				if ( settingsUpdated.length > 0 ) {
					noPaymentPointerNotice.show();
				}
			}
		}
	} );
}( jQuery ) );
