/**
 * Site setting / option tests.
 */

describe('Plugin Settings', function () {
  beforeEach(() => {
    cy.logInToWordPress('admin', 'password');
    cy.visit('/wp-admin/');
	})

	it('check that admin users can access the plugins settings screen', function() {
		cy.get('#adminmenu')
			.find('div.wp-menu-name')
			.contains('Coil')
			.click();

			cy.title().should('contain', 'Coil ‹ coil — WordPress');
  });

	it('check that the payment pointer can be set', function() {
		cy.get('#adminmenu')
			.find('div.wp-menu-name')
			.contains('Coil')
			.click();

		cy.get('#coil-global-settings').click();

		const paymentPointer = Math.random().toString(36);
		cy.get('#coil_payment_pointer_id').as('paymentPointerField');
		cy.get('@paymentPointerField')
			.click()
			.clear()
			.type(paymentPointer);
		cy.get('#submit').click();

		cy.get('@paymentPointerField').should('have.value', paymentPointer);
		cy.get('.notice').should('have.class', 'notice-success');
	} );
});
