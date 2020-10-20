describe('Tests for visibility settings in editor', () => {
	beforeEach(() => {
		cy.logInToWordPress('admin', 'password');
	})

	it('Checks that visibility settings of a post can be changed in Gutenberg', () => {
		cy.visit('/wp-admin/post.php?post=72&action=edit')

		cy
			.get('.interface-complementary-area')
			.contains('Coil Web Monetization')
			.click({force: true});

		cy
			.get('.components-radio-control__input')
			// Selecting checked option
			.then(options => {
				if(options[0].checked) {
					return options[0];
				} else {
					return options[1];
				}
			})
			// Selecting the label text and saving it as an alias
			.next()
			.invoke('text')
			.as('checkedOptionText')

		cy
			.get('.components-radio-control__input')
			// Selecting unchecked option
			.then(options => {
				if(options[0].checked) {
					return options[1];
				} else {
					return options[0];
				}
			})
			.click()

		cy
			.get('.editor-post-publish-button')
			.click();

		cy.reload();

		cy
			.get('@checkedOptionText')
			.then((checkedOptionText) => {
				// Checking that the original checkbox is not checked
				cy
					.contains(checkedOptionText)
					.prev()
					.should('not.be.checked')
			})

	})
})
