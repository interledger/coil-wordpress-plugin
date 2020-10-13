describe('Fully restricted posts', () => {

	beforeEach(() => {
		cy.logInToWordPress('admin', 'password')
	})

	it('Checks that you can edit text that appears on fully restricted posts', () => {
		cy.visit('/wp-admin/customize.php?autofocus[panel]=coil_customizer_settings_panel')
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

		cy.visit('/coil-members-only//');
		cy
			.contains(lockedMessage)
			.should('be.visible');

	})
})
