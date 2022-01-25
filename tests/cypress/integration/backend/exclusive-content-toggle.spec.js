describe( 'Exclusive content on / off toggle', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Checks that by default exclusive content is enabled', () => {
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
		cy
			.get( '#coil_exclusive_toggle' )
			.should( 'be.checked' );

		cy
			.get( '.exclusive-content' )
			.should( 'be.visible' );
	} );

	it( 'Checks that exclusive settings are shown or hidden according to exclusive content setting', () => {
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
		cy
			.get( '.exclusive-content' )
			.should( 'be.visible' );

		cy
			.get( '.coil-checkbox' )
			.click();

		cy
			.get( '.exclusive-content' )
			.should( 'not.be.visible' );

		cy
			.get( '.coil-checkbox' )
			.click();

		cy
			.get( '.exclusive-content' )
			.should( 'be.visible' );
	} );

	it( 'Checks that exclusive settings are hidden when exclusive content is disabled', () => {
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
		cy
			.get( '.coil-checkbox' )
			.click();

		cy.get( '#submit' ).click();

		cy.reload();

		cy
			.get( '.exclusive-content' )
			.should( 'not.be.visible' );
	} );
} );
