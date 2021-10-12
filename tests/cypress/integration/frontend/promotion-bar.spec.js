describe( 'Prmotion bar', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'checks that the promotion bar be can be enabled/disabled', function() {
		togglePromotionBar( 'uncheck' );
		cy.visit( '/monetized-and-public/' );
		cy
			.get( '.banner-message-inner' )
			.should( 'not.exist' );

		togglePromotionBar( 'check' );
		cy.visit( '/monetized-and-public/' );
		cy
			.get( '.banner-message-inner' )
			.should( 'be.visible' );
	} );

	it( 'Checks that you are not shown the promotion bar as a WM enabled user', () => {
		cy.visit( '/monetized-and-public/' );

		cy.startWebMonetization();

		cy
			.get( '.banner-message-inner' )
			.should( 'not.exist' );

		cy.stopWebMonetization();
	} );

	it( 'Checks that you can dissmiss the promotion bar as a WM disabled user', () => {
		cy.visit( '/monetized-and-public/' );
		cy
			.get( '.banner-message-inner' )
			.should( 'be.visible' );
		cy
			.get( '#js-coil-banner-dismiss' )
			.click();

		cy
			.get( '.banner-message-inner' )
			.should( 'not.exist' );

		cy.reload();

		cy
			.get( '.banner-message-inner' )
			.should( 'not.exist' );
	} );
} );

/**
 * Set the state of the Coil Promotion Bar option.
 *
 * @param {('check'|'uncheck')} checkboxState that the promotion bar should be set to
 */
function togglePromotionBar( checkboxState ) {
	cy.visit( '/wp-admin/admin.php?page=coil_settings' );

	cy.get( '.nav-tab-wrapper > #coil-appearance-settings' )
		.contains( 'Appearance' )
		.click();

	switch ( checkboxState ) {
		case 'check':
			cy.get( '#display_promotion_bar' )
				.check();
			break;
		case 'uncheck':
			cy.get( '#display_promotion_bar' )
				.uncheck();
			break;
	}

	cy.get( '#submit' )
		.click( { force: true } );

	cy
		.get( '.notice-success' )
		.should( 'exist' );
}
