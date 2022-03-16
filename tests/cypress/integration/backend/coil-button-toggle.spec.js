/**
 * Coil button settings.
*/

describe( 'Coil button settings tab', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=coil_button' );
	} );

	it( 'Checks that by default the Coil button is enabled', () => {
		cy
			.get( '#coil_button_toggle' )
			.should( 'be.checked' );

		cy
			.get( '.coil-button-section' )
			.should( 'be.visible' );
	} );

	it( 'Checks that the setting sections are shown or hidden depending on whether the Coil button is enabled', () => {
		// By default the Coil button is enabled.
		cy
			.get( '.coil-button-section' )
			.should( 'be.visible' );

		// Disable the Coil button and check that the other settings are hidden.
		cy
			.get( '.coil-checkbox' )
			.click();

		cy
			.get( '.coil-button-section' )
			.should( 'not.be.visible' );

		// Enabling the Coil button should reveal the other settings.
		cy
			.get( '.coil-checkbox' )
			.click();

		cy
			.get( '.coil-button-section' )
			.should( 'be.visible' );
	} );

	it( 'Checks that Coil button settings are hidden when the Coil button is disabled', () => {
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
