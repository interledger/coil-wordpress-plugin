/**
 * Streaming support widget visibility settings.
*/

describe( 'Streaming support widget visibility settings', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=streaming_widget' );
	} );

	it( 'Checks streaming support widget visibility defaults', () => {
		cy
			.get( '#post_streaming_widget_visibility_show' )
			.should( 'be.checked' );

		cy
			.get( '#page_streaming_widget_visibility_show' )
			.should( 'be.checked' );
	} );

	it( 'Checks streaming support widget visibility settings can be changed', () => {
		cy
			.get( '#post_streaming_widget_visibility_hide' )
			.click();

		cy
			.get( '#page_streaming_widget_visibility_hide' )
			.click();

		cy
			.get( '#submit' )
			.click();

		cy
			.get( '#post_streaming_widget_visibility_hide' )
			.should( 'be.checked' );

		cy
			.get( '#page_streaming_widget_visibility_hide' )
			.should( 'be.checked' );
	} );
} );
