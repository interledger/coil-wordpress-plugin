/**
 * Site setting / option tests.
 */
const paymentPointer = 'https://example.com/' + Math.random().toString(36) + '/.well-known/pay';

// Most of these tests assume you have the test posts loaded in your WordPress.
describe('Single Posts', function () {
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
	});

	it('check that the payment pointer is printed when viewing a single post.', function() {
		cy.visit('/');

		cy.get('.hentry .entry-title a')
			.contains('Monetized and Public')
			.then($link => {
				$link[0].scrollIntoView();
				$link[0].click();
			})

		cy.get('head meta[name="monetization"]').should('have.attr', 'content', paymentPointer);
	});

	it('check that I can view single post set to monetised and public.', function() {
		cy.visit('/');

		cy.get('.hentry .entry-title a')
			.contains('Monetized and Public')
			.then($link => {
				$link[0].scrollIntoView();
				$link[0].click();
			})

		cy.get('.entry-content').should('contain', 'ID: TESTPOST1');
	});

	it('check that I can view single post set to no monetization.', function() {
		cy.visit('/');

		cy.get('.hentry .entry-title a')
			.contains('No Monetization')
			.click();

		cy.get('.entry-content').should('contain', 'ID: TESTPOST2');
		cy.get('head meta[name="monetization"]', {timeout: 0}).should('not.exist');
	} );
});
