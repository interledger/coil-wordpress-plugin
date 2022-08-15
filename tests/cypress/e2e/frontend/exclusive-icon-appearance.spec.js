/**
 * Exclusive icon settings.
*/

describe( 'Exclusive icon appearance test', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Checks icon settings', () => {
		// Icons should not display when disabled
		toggleIconDisplay( 'uncheck' );

		cy.visit( '/coil-members-only/' );
		cy
			.get( 'h1 > svg' )
			.should( 'not.exist' );

		// Icons should display when enabled
		toggleIconDisplay( 'check' );

		cy.visit( '/coil-members-only/' );
		cy
			.get( 'h1 > svg' )
			.should( 'exist' );
	} );

	it( 'check that no icon is added when exclusive content has been disabled.', function() {
		// Ensure exclusive content is disabled and the icon display is enabled
		cy.addSetting( 'coil_exclusive_settings_group', [
			{
				key: 'coil_exclusive_toggle',
				value: '0',
			},
			{
				key: 'coil_title_padlock',
				value: '1',
			},
		] );

		cy.visit( '/coil-members-only/' );

		// Even though the icon is enabled it should not be displayed.
		cy
			.get( 'h1 > svg' )
			.should( 'not.exist' );
	} );
} );

/**
 * Checks or unchecks the display icon option
 *
 * @param {('check'|'uncheck')} checkboxState state for the icon display
 */
function toggleIconDisplay( checkboxState ) {
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
