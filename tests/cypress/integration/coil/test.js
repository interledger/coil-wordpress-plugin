describe('test', () => {

	beforeEach(() => {
		//cy.logInToWordPress('admin', 'password')
	})

	it('test', () => {
		cy.visit('/coil-members-only/');
		cy.contains('This is a test post for the Coil Members Only state.').should('be.hidden');

		cy.startWebMonetization();
		cy.contains('This is a test post for the Coil Members Only state.').should('be.visible');
	})
})
