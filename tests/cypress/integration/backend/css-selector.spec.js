/**
 * CSS selector tests.
*/

describe( 'Plugin Settings Panel', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'check that the CSS selectors can be set and changed', function() {
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );

		const cssSelector = '.content-area .post-content';
		cy.get( '#coil_content_container' ).as( 'cssSelectorField' );
		cy.get( '@cssSelectorField' )
			.click()
			.clear()
			.type( cssSelector );
		cy.get( '#submit' ).click();

		// Settings page is reloaded.
		cy.get( '@cssSelectorField' ).should( 'have.value', cssSelector );
		cy.get( '.notice' ).should( 'have.class', 'notice-success' );
	} );
} );
