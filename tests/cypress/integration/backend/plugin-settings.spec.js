// Need to add tests for the rest of the settings panel

/**
 * Site setting / option tests.
 */

describe( 'Plugin Settings Panel', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'check that admin users can access the plugins settings screen', function() {
		cy.visit( '/wp-admin/' );
		cy.get( '#adminmenu' )
			.find( 'div.wp-menu-name' )
			.contains( 'Coil' )
			.click();

		cy.get( 'div.plugin-branding > .plugin-branding' ).should( 'contain', 'Coil Web Monetization' );
	} );

	it( 'Check warning pops up if payment pointer is empty', function( ) {
		cy.get( '#adminmenu' )
			.find( 'div.wp-menu-name' )
			.contains( 'Coil' )
			.click();

		cy.get( '#coil-global-settings' ).click();

		cy.get( '#coil_payment_pointer_id' )
			.click()
			.clear();
		cy.get( '#submit' ).click();
		cy.get( '.coil-no-payment-pointer-notice__content' ).should( 'exist' );
	} );

	it( 'check that the payment pointer can be set', function() {
		cy.get( '#adminmenu' )
			.find( 'div.wp-menu-name' )
			.contains( 'Coil' )
			.click();

		cy.get( '#coil-global-settings' ).click();

		const paymentPointer = 'https://example.com/' + Math.random().toString( 36 ) + '/.well-known/pay';
		cy.get( '#coil_payment_pointer_id' ).as( 'paymentPointerField' );
		cy.get( '@paymentPointerField' )
			.click()
			.clear()
			.type( paymentPointer );
		cy.get( '#submit' ).click();
		cy.get( '.coil-no-payment-pointer-notice__content' ).should( 'not.exist' );

		// Settings page is reloaded.
		cy.get( '@paymentPointerField' ).should( 'have.value', paymentPointer );
		cy.get( '.notice' ).should( 'have.class', 'notice-success' );

		// Check the payment pointer is output in a post's HTML.
		// The Hello World post is set to the default settings with monetization enabled and full visibility
		cy.visit( '/hello-world/' );
		cy.get( 'head meta[name="monetization"]' ).should( 'have.attr', 'content', paymentPointer );
	} );

	it( 'Check warning pops up if CSS selector is empty', function( ) {
		cy.get( '#adminmenu' )
			.find( 'div.wp-menu-name' )
			.contains( 'Coil' )
			.click();

		cy.get( '#coil-global-settings' ).click();

		cy.get( '#coil_content_container' )
			.click()
			.clear();
		cy.get( '#submit' ).click();
		cy.get( '#coil_content_container' ).invoke( 'prop', 'validationMessage' ).should( 'equal', 'Please fill out this field.' );
		cy.get( '.notice-success' ).should( 'not.exist' );
	} );

	it( 'check that the CSS selectors can be set and changed', function() {
		cy.get( '#adminmenu' )
			.find( 'div.wp-menu-name' )
			.contains( 'Coil' )
			.click();

		cy.get( '#coil-global-settings' ).click();

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
