/* global coilAdminParams */

( function( $ ) {
	$( document ).ready( function() {
		const activeTabID = $( '.nav-tab-wrapper a.nav-tab-active' ).attr( 'id' );

		// Welcome notice
		if ( activeTabID === 'coil-welcome-settings' ) {
			const $welcomeNoticeDismissButton = $( '.coil-welcome-notice .notice-dismiss' );

			// No welcome notice on this screen.
			if ( ! $welcomeNoticeDismissButton ) {
				return;
			}

			if ( ! coilAdminParams || ! coilAdminParams.ajax_url ) {
				return;
			}

			// Fire ajax request to dismiss notice permanently.
			$welcomeNoticeDismissButton.on( 'click', function() {
				$.ajax( {
					url: coilAdminParams.ajax_url,
					type: 'POST',
					data: {
						action: 'dismiss_welcome_notice',
					},
				} );
			} );
		}

		// No payment pointer
		if ( activeTabID === 'coil-general-settings' ) {
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
