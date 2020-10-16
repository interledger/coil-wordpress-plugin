describe('Block visibility tests for non WM-enabled users', () => {
	beforeEach(() => {
		cy.visit('/block-visibility/')
	})

	it('Checks block visibility settings for blocks hidden to non WM-enabled users', () => {
		cy
			.get('.wm-shown h3')
			.invoke('text')
			.should('contain', 'This content is for Coil Members only. To access, join Coil and install the browser extension.')

		cy
			.get('.wm-shown p')
			.invoke('text')
			.should('contain', 'This content is for Coil Members only. To access, join Coil and install the browser extension.')
	})

	it('Checks block visibility settings for blocks shown to non WM-enabled users', () => {
		cy
			.get('.everyone-shown h3')
			.invoke('text')
			.should('not.contain', 'This content is for Coil Members only. To access, join Coil and install the browser extension.')

		cy
			.get('.everyone-shown img')
			.invoke('text')
			.should('not.contain', 'This content is for Coil Members only. To access, join Coil and install the browser extension.')
	})

	it('Checks block visibility settings for blocks hidden from WM-enabled users', () => {
		cy
			.get('.wm-hidden h3')
			.invoke('text')
			.should('not.contain', 'This content is for Coil Members only. To access, join Coil and install the browser extension.')

		cy
			.get('.wm-hidden ')
			.should('be.visible');
	})
})

describe('Block visibility tests for WM-enabled users', () => {
	beforeEach(() => {
		cy.visit('/block-visibility/')
		cy.startWebMonetization();
	})

	afterEach(() => {
		cy.stopWebMonetization();
	})

	it('Checks block visibility settings for blocks hidden to non WM-enabled users', () => {
		cy
			.get('.wm-shown h3')
			.invoke('text')
			.should('not.contain', 'This content is for Coil Members only. To access, join Coil and install the browser extension.')

		cy
			.get('.wm-shown p')
			.invoke('text')
			.should('not.contain', 'This content is for Coil Members only. To access, join Coil and install the browser extension.')
	})

	it('Checks block visibility settings for blocks shown to non WM-enabled users', () => {
		cy
			.get('.everyone-shown h3')
			.invoke('text')
			.should('not.contain', 'This content is for Coil Members only. To access, join Coil and install the browser extension.')

		cy
			.get('.everyone-shown img')
			.invoke('text')
			.should('not.contain', 'This content is for Coil Members only. To access, join Coil and install the browser extension.')
	})

	it('Checks block visibility settings for blocks hidden from WM-enabled users', () => {
		cy
			.get('.wm-hidden')
			.should('not.be.visible')
	})
})

