/**
 * Payment pointer tests.
*/

describe( 'Plugin Settings Panel', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=general_settings' );
	} );

	it( 'Check warning pops up if payment pointer is empty', function( ) {
		cy.get( '#coil_payment_pointer' )
			.click()
			.clear();
		cy.get( '#submit' ).click( { force: true } );
		cy.get( '.coil-no-payment-pointer-notice__content' ).should( 'exist' );
	} );

	it( 'Check warning appears if payment pointer is invalid', function( ) {
		const invalidInput = 'abc';
		const validInput = 'https://well-known-pay';
		cy
			.get( '#coil_payment_pointer' )
			.type( `{selectall}${ invalidInput }` )
			.blur();

		cy.checkForInvalidAlert( true, '#coil_payment_pointer' );

		cy
			.get( '#coil_payment_pointer' )
			.type( `{selectall}${ validInput }` );

		cy.checkForInvalidAlert( false, '#coil_payment_pointer' );

		cy
			.get( '#coil_payment_pointer' )
			.type( `{selectall}${ invalidInput }` )
			.blur();

		cy.checkForInvalidAlert( true, '#coil_payment_pointer' );

		cy.get( '#submit' ).click();

		cy
			.get( '#coil_payment_pointer' )
			.should( 'have.value', '' );

		cy.checkForInvalidAlert( false, '#coil_payment_pointer' );
	} );

	it( 'check that the payment pointer can be set', function() {
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
		cy.get( 'link[rel="monetization"]' ).should( 'have.attr', 'href', paymentPointer );
	} );
} );
