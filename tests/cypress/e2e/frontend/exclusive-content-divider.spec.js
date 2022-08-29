// test paywall appears, appropriate text displays
describe( 'Exclusive content divider for WM-enabled users', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/exclusive-content-divider' );
		cy.startWebMonetization();
	} );

	afterEach( () => {
		cy.stopWebMonetization();
	} );

	it( 'Checks that web monetized viewers can see full content', () => {
		cy
			.contains( 'This is visible all the way till here.' )
			.should( 'be.visible' );

		cy
			.contains( 'This is exclusive:' )
			.should( 'be.visible' );

		cy
			.get( '.coil-message-inner' )
			.should( 'not.exist' );
	} );
} );

describe( 'Exclusive content divider for non WM-enabled users', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/exclusive-content-divider' );
	} );

	it( 'Checks that using the ECD shows the paywall as expected', () => {
		cy
			.contains( 'This is visible all the way till here.' )
			.should( 'be.visible' );

		cy
			.contains( 'This is exclusive:' )
			.should( 'not.be.visible' );

		cy
			.get( '.coil-message-inner' )
			.should( 'exist' );
	} );
} );
