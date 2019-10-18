(function($) {
	var version                     = coil_params.coil_version,
		content_container           = coil_params.coil_content_container,
		verifying_browser_extension = coil_params.verifying_browser_extension,
		browser_extension_missing   = coil_params.browser_extension_missing;

	// Ensure "coil_params" exists to continue.
	if ( typeof coil_params === 'undefined' ) {
		return false;
	}

	$( document ).ready(function () {
		var content = ( typeof content_container !== 'undefined' ) ? content_container : '.content-area'; // If not set, use default content container class.

		if ( $('body').hasClass('monetization-not-initialized') ) {
			$( content ).before('<p class="monetize-msg" style="text-align:center;">' + verifying_browser_extension + '</p>');
		}

		// Check if monetization is implemented.
		if ( document.monetization ) {
			$('p.monetize-msg').remove(); // Remove message.
			$('body').removeClass('monetization-not-initialized').addClass('monetization-initialized'); // Update body class to show content.

			// Monetization has started.
			document.monetization.addEventListener( 'monetizationstart', function(event) {
				monetizationStartEventOccurred = true;
			});

			// Monetization progress event.
			document.monetization.addEventListener('monetizationprogress', function(event){
				//container.innerText = container.innerText + 'monetizationprogress: \n' + JSON.stringify(event.detail, null, 2) + '\n\n';
				console.log('monetizationprogress', event);
			});
		} else {
			$('p.monetize-msg').html( browser_extension_missing );
		}
	});
})(jQuery);