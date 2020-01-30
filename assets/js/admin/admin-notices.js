(function($) {
	$( document ).ready(function() {
		$welcomeNoticeDismissButton = $('.coil-welcome-notice .notice-dismiss');

		// No welcome notice on this screen.
		if ( ! $welcomeNoticeDismissButton ) {
			return;
		}

		if ( ! coil_admin_params || ! coil_admin_params.ajax_url ) {
			return;
		}

		// Fire ajax request to dismiss notice permanently.
		$welcomeNoticeDismissButton.on( 'click', function() {
			$.ajax({
				url: coil_admin_params.ajax_url,
				type: 'POST',
				data: {
					action: 'dismiss_welcome_notice'
				}
			})
		} );
	});
})(jQuery);
