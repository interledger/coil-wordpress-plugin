describe( 'Exclusive post appearance test', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Checks padlock settings', () => {
		// Padlock should not display when disabled
		togglePadlock( 'uncheck' );

		cy.visit( '/coil-members-only/' );
		cy
			.get( '.entry-title > svg' )
			.should( 'not.exist' );

		// Padlock should display when enabled
		togglePadlock( 'check' );

		cy.visit( '/coil-members-only/' );
		cy
			.get( '.entry-title > svg' )
			.should( 'exist' );
	} );
} );

/**
 * Checks or unchecks the display padlock option
 *
 * @param {('check'|'uncheck')} checkboxState state for the padlock display
 */
function togglePadlock( checkboxState ) {
	cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );

	switch ( checkboxState ) {
		case 'check':
			cy
				.get( '#coil_title_padlock' )
				.click()
				.check();
			break;
		case 'uncheck':
			cy
				.get( '#coil_title_padlock' )
				.click()
				.uncheck();
			break;
	}

	cy
		.get( '#submit' )
		.click( { force: true } );
}
