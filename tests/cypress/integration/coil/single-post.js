/**
 * Site setting / option tests.
 */
const paymentPointer = 'https://example.com/' + Math.random().toString(36) + '/.well-known/pay';

describe('Plugin Settings', function () {
  beforeEach(() => {
    cy.logInToWordPress('admin', 'password');
		cy.visit('/wp-admin/admin.php?page=coil_settings');

		// Make sure a payment pointer is set.
		cy.get('#coil-global-settings').click();
		cy.get('#coil_payment_pointer_id').as('paymentPointerField');
		cy.get('@paymentPointerField')
			.click()
			.clear()
			.type(paymentPointer);
		cy.get('#submit').click();
	})

	it('check that the payment pointer is printed when viewing a single post', function() {
		cy.visit('/');

		cy.get('.hentry .entry-title a')
			.contains('Monetized and Public')
			.click();

		cy.get('head meta[name="monetization"]').should('have.attr', 'content', paymentPointer);
	} );

	// This test assumes you have the test posts loaded.
	it('check that I can view a monetised and public single post.', function() {
		cy.visit('/');

		cy.get('.hentry .entry-title a')
			.contains('Monetized and Public')
			.click();

		cy.get('.entry-content').should('contain', 'ID: TESTPOST1');
	} );
});
