describe('Block visibility tests', () => {
	beforeEach(() => {
		cy.visit('/block-visibility/')
	})

	it('Checks block visibility settings for blocks hidden to non WM-enabled users', () => {
		cy
			.contains('This should be hidden for non WM-enabled users')
			.should('have.class', 'coil-show-monetize-users')

		cy
			.contains('Lorem ipsum dolor sit amet')
			.should('have.class', 'coil-show-monetize-users');
	})

	it('Checks block visibility settings for blocks shown to non WM-enabled users', () => {
		cy
			.contains('This should be shown for non WM-enabled users')
			.should('not.have.class', 'coil-show-monetize-users');

		cy
			.get('.wp-block-image')
			.should('not.have.class', 'coil-show-monetize-users');
	})

	it('Checks block visibility settings for blocks hidden from WM-enabled users', () => {
		cy
			.contains('This should be hidden for WM-enabled users')
			.should('not.have.class', 'coil-show-monetize-users');

		cy
			.get('.wp-block-buttons')
			.should('not.have.class', 'coil-show-monetize-users');
	})
})

