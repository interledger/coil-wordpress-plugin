/**
 * Exclusive post settings.
*/

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

	it( 'check that no padlock is added when exclusive content has been disabled.', function() {
		// Ensure exclusive content is disabled and the padlock display is enabled
		const optionString = 'a:2:{s:21:\\\"coil_exclusive_toggle\\\";b:0;s:18:\\\"coil_title_padlock\\\";b:1;}';
		cy.exec( 'wp db query \'DELETE FROM wp_options WHERE option_name IN ("coil_exclusive_settings_group");\' --allow-root' );
		cy.exec( 'wp db query \'INSERT INTO wp_options (option_name, option_value) VALUES ( \"coil_exclusive_settings_group\", \"' + optionString + '\");\' --allow-root' );

		cy.visit( '/coil-members-only/' );

		// Even though the padlock is enabled it should not be displayed.
		cy
			.get( '.entry-title > .emoji' )
			.should( 'not.exist' );
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
