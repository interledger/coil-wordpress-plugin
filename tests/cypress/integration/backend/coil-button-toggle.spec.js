/**
 * Streaming support widget settings.
*/

describe( 'Streaming support widget settings tab', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=streaming_support_widget' );
	} );

	it( 'Checks that by default the Streaming support widget is enabled', () => {
		cy
			.get( '#coil_button_toggle' )
			.should( 'be.checked' );

		cy
			.get( '.coil-button-section' )
			.should( 'be.visible' );
	} );

	it( 'Checks that the setting sections are shown or hidden depending on whether the Streaming support widget is enabled', () => {
		// By default the Streaming support widget is enabled.
		cy
			.get( '.coil-button-section' )
			.should( 'be.visible' );

		// Disable the Streaming support widget and check that the other settings are hidden.
		cy
			.get( '.coil-checkbox' )
			.click();

		cy
			.get( '.coil-button-section' )
			.should( 'not.be.visible' );

		// Enabling the Streaming support widget should reveal the other settings.
		cy
			.get( '.coil-checkbox' )
			.click();

		cy
			.get( '.coil-button-section' )
			.should( 'be.visible' );
	} );

	it( 'Checks that Streaming support widget settings are hidden when the Streaming support widget is disabled', () => {
		cy
			.get( '.coil-checkbox' )
			.click();

		cy.get( '#submit' ).click();

		cy.reload();

		cy
			.get( '.coil-button-section' )
			.should( 'not.be.visible' );
	} );
} );
