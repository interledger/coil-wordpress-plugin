describe('Padlock test', () => {

	beforeEach(() => {
		cy.logInToWordPress('admin', 'password');
	})

	it('Checks if a padlock appears when enabled', () => {
		cy.server()
		cy.route({method: 'POST', url: '/wp-admin/admin-ajax.php'}).as('settingsSubmitted')

		togglePadlock('check');

		cy.visit('/coil-members-only/')
		cy
			.get('.entry-title > .emoji')
			.should('have.attr', 'alt', '🔒')

		togglePadlock('uncheck');

		cy.visit('/coil-members-only/')
		cy
			.get('.entry-title > .emoji')
			.should('not.exist')
	})
})

/**
 * Checks or unchecks the display padlock option
 *
 * @param {('check'|'uncheck')} checkboxState
 */
function togglePadlock(checkboxState) {
	cy.visit('/wp-admin/customize.php')

	cy
		.contains('Coil Web Monetization')
		.click();

	cy
		.get('#accordion-section-coil_customizer_section_options')
		.click();

	switch(checkboxState) {
		case 'check':
			cy
				.get('#_customize-input-coil_title_padlock')
				.click()
				.check();
			break;
		case 'uncheck':
			cy
				.get('#_customize-input-coil_title_padlock')
				.click()
				.uncheck();
			break;
	}

	cy
		.get('#save')
		.click({force: true});

	cy.wait('@settingsSubmitted')
}
