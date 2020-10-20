const hiddenContentMessage = 'This content is for Coil Members only. To access, join Coil and install the browser extension.';

describe('Visibility of content blocks for non WM-enabled users', () => {
	beforeEach(() => {
		cy.visit('/block-visibility/')
	})

	it('Check visibility of content blocks hidden to non WM-enabled users', () => {
		cy
			.get('.wm-shown h3')
			.invoke('text')
			.should('contain', hiddenContentMessage)

		cy
			.get('.wm-shown p')
			.invoke('text')
			.should('contain', hiddenContentMessage)
	})

	it('Check visibility of content blocks shown to non WM-enabled users', () => {
		cy
			.get('.everyone-shown h3')
			.invoke('text')
			.should('not.contain', hiddenContentMessage)

		cy
			.get('.everyone-shown img')
			.invoke('text')
			.should('not.contain', hiddenContentMessage)
	})

	it('Check visibility of content blocks hidden from WM-enabled users', () => {
		cy
			.get('.wm-hidden h3')
			.invoke('text')
			.should('not.contain', hiddenContentMessage)

		cy
			.get('.wm-hidden ')
			.should('be.visible');
	})
})

describe('Check visibility of content blocks for WM-enabled users', () => {
	beforeEach(() => {
		cy.visit('/block-visibility/')
		cy.startWebMonetization();
	})

	afterEach(() => {
		cy.stopWebMonetization();
	})

	it('Check visibility of content blocks hidden to non WM-enabled users', () => {
		cy
			.get('.wm-shown h3')
			.invoke('text')
			.should('not.contain', hiddenContentMessage)

		cy
			.get('.wm-shown p')
			.invoke('text')
			.should('not.contain', hiddenContentMessage)
	})

	it('Check visibility of content blocks shown to non WM-enabled users', () => {
		cy
			.get('.everyone-shown h3')
			.invoke('text')
			.should('not.contain', hiddenContentMessage)

		cy
			.get('.everyone-shown img')
			.invoke('text')
			.should('not.contain', hiddenContentMessage)
	})

	it('Check visibility of content blocks hidden from WM-enabled users', () => {
		cy
			.get('.wm-hidden')
			.should('not.be.visible')
	})
})

