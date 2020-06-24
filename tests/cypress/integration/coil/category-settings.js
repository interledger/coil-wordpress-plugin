/**
 * Site setting / option tests.
 */

describe('Coil Settings', function () {
  beforeEach(() => {
    cy.logInToWordPress('admin', 'password');
  })

	it('checks that admin users can access the settings screen', function() {
    cy.visit('/wp-admin/');

		cy.get('#adminmenu').find('div.wp-menu-name').contains('Coil').as('coilSettings');
		cy.get('@coilSettings').click();

		cy.contains('Welcome to Coil Web Monetization for WordPress');
  });

	it('checks that Coil category settings can be updated', function() {
		cy.visit('/wp-admin/edit-tags.php?taxonomy=category');

		/*
		 * This is a workaround for WordPress hiding the Quick Actions row.
		 *
		 * Read https://docs.cypress.io/guides/core-concepts/conditional-testing.html#Element-existence
		 * before using this pattern anywhere else.
		 */
		const deleteCategory = '.row-actions span.delete a[aria-label="Delete “democategory”"]';
		cy.get('body').then(($body) => {
			if ($body.find(deleteCategory).length) {
				cy.get(deleteCategory).click({force: true});
			}
		});

		cy.get('#tag-name').type('democategory');
		cy.get('#tag-slug').type('democategoryslug');
		cy.get('#coil-category-settings > label[for="gate-all"]').click();
		cy.get('#submit').click();

		const editCategory = '.row-actions span.edit a[aria-label="Edit “democategory”"]';
		cy.get(editCategory).then(($element) => {
			$element[0].click();
		});

		cy.get('#coil-category-settings > label[for="gate-all"]').should('be.checked');
	});
});
