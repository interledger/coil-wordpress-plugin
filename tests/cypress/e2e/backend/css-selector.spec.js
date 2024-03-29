/**
 * CSS selector tests.
*/

describe( 'Plugin Settings Panel', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
	} );

	it( 'check that the CSS selectors can be set and changed', function() {
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

	it( 'checks the CSS selector sugestor is working', () => {
		cy.on( 'window:confirm', ( text ) => {
			expect( text ).to.contains( 'Would you like to set your CSS selector to' );
			return true;
		} );
		cy.get( '#css_selector_button' ).click();
		// The assertion above doesn't fail the test as it should when it fails.
		// To be safe the changed value needs to be explicitly checked.
		// Note: this test assumes the Twenty Twenty-Two theme
		cy.get( '#coil_content_container' ).should( 'have.value', 'main .entry-content' );
	} );

	it( 'checks that the CSS input cannot be filled with whitespace', () => {
		cy
			.get( '#coil_content_container' )
			.type( `{selectall}${ '   ' }` );

		cy.checkForInvalidAlert( true, '#coil_content_container' );

		cy
			.get( '#coil_content_container' )
			.type( '.' );

		cy.checkForInvalidAlert( false, '#coil_content_container' );

		cy
			.get( '#coil_content_container' )
			.clear();

		cy.checkForInvalidAlert( false, '#coil_content_container' );
	} );

	it( 'checks that when the CSS input is removed it saves as the deafualt value', () => {
		cy
			.get( '#coil_content_container' )
			.clear();

		cy.get( '#submit' ).click();

		cy
			.get( '#coil_content_container' )
			.should( 'have.value', '.content-area .entry-content, main .entry-content' );
	} );
} );
