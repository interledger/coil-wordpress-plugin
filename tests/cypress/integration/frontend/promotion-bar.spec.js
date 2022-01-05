describe( 'Promotion bar for WM-enabled users', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/monetized-and-public/' );
		cy.startWebMonetization();
	} );

	afterEach( () => {
		cy.stopWebMonetization();
	} );

	it( 'Checks that the promotion bar is not shown as a WM enabled user', () => {
		cy
			.get( '.banner-message-inner' )
			.should( 'not.exist' );
	} );
} );

describe( 'Promotion bar for non WM-enabled users', function() {
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
 * @param {String} checkboxState either check or uncheck
 */
function togglePromotionBar( checkboxState ) {
	cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=coil_button' );

	switch ( checkboxState ) {
		case 'check':
			cy.get( '#coil_show_promotion_bar' )
				.click()
				.check();
			break;
		case 'uncheck':
			cy.get( '#coil_show_promotion_bar' )
				.click()
				.uncheck();
			break;
	}

	cy.get( '#submit' )
		.click( { force: true } );
}
