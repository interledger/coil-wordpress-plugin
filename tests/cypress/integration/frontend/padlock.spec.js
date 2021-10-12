describe( 'Padlock test', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Checks if a padlock appears when enabled', () => {
		togglePadlock( 'uncheck' );

		cy.visit( '/coil-members-only/' );
		cy
			.get( '.entry-title > .emoji' )
			.should( 'not.exist' );

		togglePadlock( 'check' );

		cy.visit( '/coil-members-only/' );
		cy
			.get( '.entry-title > .emoji' )
			.should( 'exist' );
	} );
} );

/**
 * Checks or unchecks the display padlock option
 *
 * @param {('check'|'uncheck')} checkboxState state for the padlock display
 */
function togglePadlock( checkboxState ) {
	cy.visit( '/wp-admin/admin.php?page=coil_settings' );

	cy.get( '.nav-tab-wrapper > #coil-appearance-settings' )
		.contains( 'Appearance' )
		.click();

	switch ( checkboxState ) {
		case 'check':
			cy
				.get( '#display_padlock_id' )
				.check();
			break;
		case 'uncheck':
			cy
				.get( '#display_padlock_id' )
				.uncheck();
			break;
	}

	cy
		.get( '#submit' )
		.click( { force: true } );
}
