$(document).ready(function() {
	var coil_meta_container = $('.metabox-location-side').find('.coil');

	if ( coil_meta_container.length > 0 ) {
		var post_status = $('input[type="radio"][name="coil_monetize_post_status"]').val();

		if ( post_status == 'no' ) {
			$('body').find('.components-panel__body.coil-panel').hide();
		}

		// Shows or Hides the inspector control based on the document setting.
		$('input[type="radio"][name="coil_monetize_post_status"]').on('change', function() {
			if ( $(this).val() != 'no' ) {
				$('body').find('.components-panel__body.coil-panel').show();
			} else {
				$('body').find('.components-panel__body.coil-panel').hide();
			}
		});
	}
});