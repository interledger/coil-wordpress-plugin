const paywallMessage = 'We partnered with Coil to offer exclusive content. Access this and other great content with a Coil membership.';

describe( 'Visibility of content blocks for non WM-enabled users', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/block-visibility/' );
	} );

	it( 'Check visibility of content blocks hidden to non WM-enabled users', () => {
		cy
			.get( '.coil-show-monetize-users' )
			.invoke( 'text' )
			.should( 'contain', paywallMessage );
	} );

	it( 'Check visibility of content blocks shown to non WM-enabled users', () => {
		cy
			.contains( 'This block is public.' )
			.should( 'not.contain', paywallMessage )
			.should( 'be.visible' );

		cy
			.get( 'img' )
			.invoke( 'text' )
			.should( 'not.contain', paywallMessage );
	} );

	it( 'Check visibility of content blocks hidden from WM-enabled users', () => {
		cy
			.contains( 'This block is hidden for Coil members.' )
			.should( 'not.contain', paywallMessage )
			.should( 'be.visible' );
	} );
} );

describe( 'Check visibility of content blocks for WM-enabled users', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/block-visibility/' );
		cy.startWebMonetization();
	} );

	afterEach( () => {
		cy.stopWebMonetization();
	} );

	it( 'Check visibility of content blocks hidden to non WM-enabled users', () => {
		cy
			.get( '.coil-show-monetize-users' )
			.invoke( 'text' )
			.should( 'not.contain', paywallMessage )
			.should( 'contain', 'This block is only visible to Coil members.' );
	} );

	it( 'Check visibility of content blocks shown to non WM-enabled users', () => {
		cy
			.contains( 'This block is public.' )
			.should( 'not.contain', paywallMessage )
			.should( 'be.visible' );

		cy
			.get( 'img' )
			.invoke( 'text' )
			.should( 'not.contain', paywallMessage );
	} );

	it( 'Check visibility of content blocks hidden from WM-enabled users', () => {
		cy
			.get( '.coil-hide-monetize-users' )
			.should( 'not.be.visible' );
	} );
} );

