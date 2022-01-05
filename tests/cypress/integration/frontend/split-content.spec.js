describe( 'Check visibility of content blocks', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/block-visibility/' );
	} );

	it( 'Check visibility of content blocks when viewed by WM-enabled users', () => {
		cy.startWebMonetization();

		// Exclusive block should be visible
		cy
			.contains( 'This block is only visible to Coil members.' )
			.should( 'be.visible' );

		// Paywall should not exist
		cy
			.get( '.coil-split-content-message' )
			.should( 'not.exist' );

		// Public block should be visible
		cy
			.contains( 'This block is public.' )
			.should( 'be.visible' );

		// This block should be hidden for Coil members
		cy
			.get( '.coil-hide-monetize-users' )
			.invoke( 'css', 'display' )
			.should( 'equal', 'none' )
			.should( 'not.be.visible' );

		cy.stopWebMonetization();
	} );
} );

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
			.should( 'contain', 'We partnered with Coil to offer exclusive content. Access this and other great content with a Coil membership.' );
	} );

	it( 'Check visibility of content blocks shown to non WM-enabled users', () => {
		cy
			.contains( 'This block is public.' )
			.should( 'be.visible' );
	} );

	it( 'Check visibility of content blocks hidden from WM-enabled users', () => {
		cy
			.contains( 'This block is hidden for Coil members.' )
			.should( 'be.visible' );
	} );
} );
