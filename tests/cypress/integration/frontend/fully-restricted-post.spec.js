describe('Fully restricted posts', () => {

	beforeEach(() => {
		cy.logInToWordPress('admin', 'password')
	})

	it('Checks that you can edit text that appears on fully restricted posts', () => {
		cy.visit('/wp-admin/customize.php')
		cy
			.contains('Coil Web Monetization')
			.click();

		cy
			.contains('Messages')
			.click();

		const lockedMessage = 'This post is fully locked!'

		cy
			.get('#_customize-input-coil_unsupported_message')
			.type(`{selectall}${lockedMessage}`)

		cy
			.get('#save')
			.click();

		cy.visit('/coil-members-only/');
		cy
			.contains(lockedMessage)
			.should('be.visible');
	})

	it('Checks that a VM enabled user can view monetized content', () => {
		cy.visit('/coil-members-only/');
		cy
			.contains('This is a test post for the Coil Members Only state.')
			.should('not.be.visible');

		cy.startWebMonetization();

		cy
			.contains('This is a test post for the Coil Members Only state.')
			.should('be.visible');

		cy.stopWebMonetization();
	})
})
