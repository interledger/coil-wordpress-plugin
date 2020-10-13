/**
 * Tests for options under the "Learn more button" Customiser panel.
 */

describe('"Learn more button" panel', function () {
	beforeEach(() => {
		cy.logInToWordPress('admin', 'password');
	})

	it('checks that the "Get Coil to access" button text can be changed', function() {
		cy.server()
		cy.route({method: 'POST', url: '/wp-admin/admin-ajax.php'}).as('settingsSubmitted')

		cy.logInToWordPress('admin', 'password');
		cy.visit('/wp-admin/customize.php');

		cy.get('h3.accordion-section-title')
			.contains('Coil Web Monetization')
			.click()
		cy.get('h3.accordion-section-title')
			.contains('Learn more button')
			.click();

		var label = 'New Button Text ' + Date.now();
		cy.get('#\_customize-input-coil_learn_more_button_text').clear().type(label);
		cy.get('#save').click();
		cy.wait('@settingsSubmitted')
		cy.reload();

		cy.get('h3.accordion-section-title')
			.contains('Coil Web Monetization')
			.click()
		cy.get('h3.accordion-section-title')
			.contains('Learn more button')
			.click();

		cy.get('#\_customize-input-coil_learn_more_button_text').should('have.value', label);
	});
});


