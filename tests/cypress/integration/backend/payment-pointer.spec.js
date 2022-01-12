/**
 * Site setting / option tests.
 */

describe( 'Plugin Settings Panel', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Check warning pops up if payment pointer is empty', function( ) {
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=general_settings' );

		cy.get( '#coil_payment_pointer' )
			.click()
			.clear();
		cy.get( '#submit' ).click();
		cy.get( '.coil-no-payment-pointer-notice__content' ).should( 'exist' );
	} );

	it( 'check that the payment pointer can be set', function() {
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=general_settings' );

		const paymentPointer = 'https://example.com/' + Math.random().toString( 36 ) + '/.well-known/pay';
		cy.get( '#coil_payment_pointer' ).as( 'paymentPointerField' );
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
} );
