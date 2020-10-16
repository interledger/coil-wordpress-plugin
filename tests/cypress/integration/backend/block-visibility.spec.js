describe('Tests for block-level visibility settings', () => {
	beforeEach(() => {
		cy.logInToWordPress('admin', 'password');
	})

	it("Checks that a block\s visibility settings can be changed in the block editor", () => {
		cy.visit('/wp-admin/post.php?post=22&action=edit')

		// Removal nag modal and open panel.
		cy
			.get('.interface-complementary-area')
			.contains('Coil Web Monetization')
			.click({force: true});

		// Select split content mode.
		cy
			.get('.interface-complementary-area')
			.contains('Split Content')
			.prev('input[type="radio"]')
			.check();

		// Find and select the first block (a paragraph block).
		cy
			.window()
			.its('wp')
			.then(wp => {
				const blocks = wp.data.select( 'core/block-editor' ).getBlocks();

				// figure out how to "Select" it
				wp.data.dispatch( 'core/block-editor' ).selectBlock( blocks[0].clientId );
			});

		// Open Coil panel for the selected block and change visiblity state.
		cy
			.get('.interface-complementary-area')
			.contains('Coil Web Monetization')
			.click({force: true});
		cy
			.get('.interface-complementary-area')
			.contains('Show for monetized users')
			.prev('input[type="radio"]')
			.check();

		// Check that a "this block will be shown to monetized users only" message appear.
		// This is a CSS :before attribute, which we can't check for directly.
		cy
			.get('.coil-show-monetize-users:not(.has-warning)')
			.should('exist');

		cy
			.get('.coil-hide-monetize-users:not(.has-warning)')
			.should('not.exist');
	})
})
