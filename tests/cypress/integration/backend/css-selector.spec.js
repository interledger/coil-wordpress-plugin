/**
 * CSS selector tests.
*/

describe( 'Plugin Settings Panel', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Check warning pops up if CSS selector is empty', function( ) {
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );

		cy.get( '#coil_content_container' )
			.click()
			.clear();
		cy.get( '#submit' ).click();
		cy.get( '#coil_content_container' ).invoke( 'prop', 'validationMessage' ).should( 'match', /Please fill ((in)|(out)) this field./ );
		cy.get( '.notice-success' ).should( 'not.exist' );
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
