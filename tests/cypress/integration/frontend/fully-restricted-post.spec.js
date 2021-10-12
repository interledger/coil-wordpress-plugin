describe( 'Fully restricted posts', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Checks that you can edit text that appears on fully restricted posts', () => {
		cy.visit( '/wp-admin/admin.php?page=coil_settings' );
		cy
			.get( '#coil-messaging-settings' )
			.click();

		const lockedMessage = 'This post is fully locked!';

		cy
			.get( '#coil_fully_gated_content_message' )
			.type( `{selectall}${ lockedMessage }` );
		cy
			.get( '#submit' )
			.click();

		cy.visit( '/coil-members-only/' );
		cy
			.contains( lockedMessage )
			.should( 'be.visible' );

		cy.visit( '/wp-admin/admin.php?page=coil_settings' );
		cy
			.get( '#coil-messaging-settings' )
			.click();
		cy
			.get( '#coil_fully_gated_content_message' )
			.clear().type( 'Unlock exclusive content with Coil. Need a Coil account?' );
		cy
			.get( '#submit' )
			.click();

		cy.visit( '/coil-members-only/' );
		cy
			.contains( 'Unlock exclusive content with Coil. Need a Coil account?' )
			.should( 'be.visible' );
	} );
} );

// describe( 'Check visibility of content for WM-enabled users', () => {
// 	beforeEach( () => {
// 		cy.logInToWordPress( 'admin', 'password' );
// 		cy.resetSite();
// 		cy.visit( '/coil-members-only/' );
// 		cy.startWebMonetization();
// 	} );

// 	afterEach( () => {
// 		cy.stopWebMonetization();
// 	} );

// 	it( 'Checks that a VM enabled user can view monetized content', () => {
// 		cy
// 			.contains( 'This is a test post for the Coil Members Only state.' )
// 			.should( 'be.visible' );

// 		cy
// 			.get( '.coil-message-inner' )
// 			.should( 'not.exist' );
// 	} );
// } );
