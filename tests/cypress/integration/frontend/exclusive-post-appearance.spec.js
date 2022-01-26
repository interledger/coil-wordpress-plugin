/**
 * Exclusive post settings.
*/

describe( 'Exclusive post appearance test', () => {
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

		// This test works in Circle CI where it recognizes the emoji but not always when run locally.
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
