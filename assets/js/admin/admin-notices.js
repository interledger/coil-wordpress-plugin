/* global coilAdminParams */

( function( $ ) {
	if ( typeof coilAdminParams === 'undefined' || ! coilAdminParams.ajax_url ) {
		return;
	}

	const ajaxUrl = coilAdminParams.ajax_url,
		siteLogoUrl = coilAdminParams.site_logo_url,
		lightCoilLogoUrl = coilAdminParams.coil_logo_url.light,
		darkCoilLogoUrl = coilAdminParams.coil_logo_url.dark;

	const activeTabID = $( '.nav-tab-wrapper a.nav-tab-active' ).attr( 'id' );

	// Welcome notice
	if ( activeTabID === 'coil-welcome-settings' ) {
		const $welcomeNoticeDismissButton = $( '.coil-welcome-notice .notice-dismiss' );

		// No welcome notice on this screen.
		if ( ! $welcomeNoticeDismissButton ) {
			return;
		}

		// Fire ajax request to dismiss notice permanently.
		$welcomeNoticeDismissButton.on( 'click', function() {
			$.ajax( {
				url: ajaxUrl,
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

	$( document ).on( 'keyup', '#coil_paywall_title', function() {
		if ( $( this ).val() !== '' ) {
			$( '.coil-paywall-heading' ).text( $( this ).val() );
		} else {
			$( '.coil-paywall-heading' ).text( $( this ).attr( 'placeholder' ) );
		}
	} );

	$( document ).on( 'keyup', '#coil_paywall_message', function() {
		if ( $( this ).val() !== '' ) {
			$( '.coil-paywall-body' ).text( $( this ).val() );
		} else {
			$( '.coil-paywall-body' ).text( $( this ).attr( 'placeholder' ) );
		}
	} );

	$( document ).on( 'keyup', '#coil_paywall_button_text', function() {
		if ( $( this ).val() !== '' ) {
			$( '.coil-paywall-cta' ).text( $( this ).val() );
		} else {
			$( '.coil-paywall-cta' ).text( $( this ).attr( 'placeholder' ) );
		}
	} );

	$( document ).on( 'change', 'input[name="coil_exclusive_settings_group[coil_padlock_icon_position]"]', function() {
		const padlockPosition = $( this ).val();

		$( '.coil-title-preview-container' ).attr( 'data-padlock-position', padlockPosition );
	} );

	$( document ).on( 'change', 'input[name="coil_exclusive_settings_group[coil_message_color_theme]"]', function() {
		const coilTheme = $( this ).val(),
			logoSetting = $( '#coil_branding' ).val();

		let logoSrc = '';

		$( '.coil-paywall-container' ).attr( 'data-theme', coilTheme );

		if ( logoSetting === 'coil_logo' ) {
			if ( 'light' === coilTheme ) {
				logoSrc = lightCoilLogoUrl;
			} else {
				logoSrc = darkCoilLogoUrl;
			}
			$( '.coil-paywall-image' ).attr( 'src', logoSrc );
		}
	} );

	$( document ).on( 'change', '#coil_branding', function() {
		const logoSetting = $( this ).val(),
			coilTheme = $( 'input[name="coil_exclusive_settings_group[coil_message_color_theme]"]:checked' ).val();

		let logoSrc = '';

		$( '.coil-paywall-image' ).removeClass( 'no_logo site_logo coil_logo' ).addClass( logoSetting );

		if ( logoSetting === 'coil_logo' ) {
			if ( 'light' === coilTheme ) {
				logoSrc = lightCoilLogoUrl;
			} else {
				logoSrc = darkCoilLogoUrl;
			}
		} else if ( logoSetting === 'site_logo' ) {
			logoSrc = siteLogoUrl;
		}

		$( '.coil-paywall-image' ).attr( 'src', logoSrc );
	} );

	$( document ).on( 'change', 'input[name="coil_exclusive_settings_group[coil_padlock_icon_style]"]', function() {
		const $thisInput = $( this ),
			$padlockIcon = $( '.coil-title-preview-container .coil-padlock-icon' ),
			$selectedSvg = $thisInput.siblings( 'svg' ).clone();

		$padlockIcon.html( $selectedSvg );
	} );
}( jQuery ) );
