describe('Except behaviour', () => {
	beforeEach(() => {
		cy.logInToWordPress('admin', 'password');
	})

	it('Checks that the excerpt is active when user is not authorised', () => {
		cy.visit('/excerpt-post/')
		cy
			.contains('This is the excerpt')
			.should('be.visible')
	})
})
