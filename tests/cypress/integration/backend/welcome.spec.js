/**
 * Welcome settings tab.
*/

describe( 'Welcome settings tab', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=welcome' );
	} );

	it( 'Checks the coil-welcome-notice gets displayed', () => {
		// Remove the payment pointer from the database
		const optionName = 'coil_general_settings_group';
		cy.exec( 'wp db query \'DELETE FROM wp_options WHERE option_name IN ("' + optionName + '");\' --allow-root' );
		cy.reload();

		cy
			.get( '.coil-welcome-notice' )
			.should( 'exist' );

		cy
			.get( '.coil-welcome-notice__content > :nth-child(3) > .button' )
			.should( 'contain', 'Add Payment Pointer' )
			.should( 'have.attr', 'href', '?page=coil_settings&tab=general_settings' );

		cy
			.get( '.tab-styling .button-primary' )
			.should( 'not.be.visible' );
	} );

	it( 'Checks the settings sidebar gets displayed', () => {
		cy
			.get( '.settings-sidebar' )
			.should( 'exist' );
	} );
} );
