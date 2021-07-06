/**
 * Tests for options under the "Learn more button" Customiser panel.
 */

describe('Coil options panel', function () {
	beforeEach(() => {
		cy.logInToWordPress('admin', 'password');
	})

	it('checks that the donation bar be can be enabled/disabled', function() {
		cy.server();
		cy
			.route({method: 'POST', url: '/wp-admin/admin-ajax.php'})
			.as('settingsSubmitted');

		toggleDonationBar('uncheck');
		cy.visit('/monetized-and-public/');
		cy
			.get('.banner-message-inner')
			.should('not.exist');

		toggleDonationBar('check');
		cy.visit('/monetized-and-public/');
		cy
			.get('.banner-message-inner')
			.should('be.visible');
	});

	it('Checks that you can dissmiss the donation bar as a WM enabled user', () => {
		cy.visit('/monetized-and-public/');

		cy.startWebMonetization();

		cy
			.get('.banner-message-inner')
			.should('not.exist');

		cy.stopWebMonetization();
	})

	it('Checks that you can dissmiss the donation bar as a WM disabled user', () => {
		cy.visit('/monetized-and-public/');
		cy
			.get('.banner-message-inner')
			.should('be.visible');
		cy
			.get('#js-coil-banner-dismiss')
			.click();

		cy
			.get('.banner-message-inner')
			.should('not.exist');

		cy.reload();

		cy
			.get('.banner-message-inner')
			.should('not.exist');
	})
});

/**
 * Set the state of the Coil Promotion Bar option.
 *
 * @param {('check'|'uncheck')} checkboxState
 */
function toggleDonationBar(checkboxState) {
	cy.visit('/wp-admin/customize.php')

	cy.get('h3.accordion-section-title')
		.contains('Coil Web Monetization')
		.click()

	cy.get('#accordion-section-coil_customizer_section_options')
		.click();

	switch (checkboxState) {
		case 'check':
			cy.get('#_customize-input-coil_show_donation_bar')
				.click()
				.check();
			break;
		case 'uncheck':
			cy.get('#_customize-input-coil_show_donation_bar')
				.click()
				.uncheck();
			break;
	}

	cy.get('#save')
		.click({force: true});

	cy.wait('@settingsSubmitted')
}
