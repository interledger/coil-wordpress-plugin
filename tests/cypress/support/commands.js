// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//

/**
 * Authenticate with WordPress.
 *
 * @param {string} username WordPress user name.
 * @param {string} password WordPress password.
 */
Cypress.Commands.add('logInToWordPress', (username, password) => {

	cy.request({
		method: 'POST',
		url: '/wp-login.php',
		form: true,
		body: {
			"log": username,
			"pwd": password,
		},
	});

	// Verify by asserting an authentication cookie exists.
	cy.getCookies().then((cookies) => {
		let authCookie = '';

		cookies.forEach(theCookie => {
			if (theCookie.name.startsWith('wordpress_logged_in_')) {
				authCookie = theCookie.name;
			}
		});

		expect(authCookie).to.include('wordpress_logged_in_');
	});
});

/**
 * Mock and start a (fake) web monetization session.
 */
Cypress.Commands.add('startWebMonetization', () => {

	cy.window().then((window) => {
		startMonetization(window);

		cy
			.reload()
			.then(window => {
				startMonetization(window);
			})
	});
});

/**
 * Mock and start a (fake) web monetization session.
 *
 * @param window
 */
function startMonetization(window) {
	const doc = window.document;

	// Shim the Web Monetization API: https://webmonetization.org/specification.html
	if (!doc.monetization) {
		doc.monetization = doc.createElement('div');
	}
	doc.monetization.state = 'started';

	// Re-init Coil.
	doc.dispatchEvent(new Event('coilstart'));

	window.Cypress.monetized = true;

	// Trigger the "user has paid $$$" event.
	doc.monetization.dispatchEvent(new Event('monetizationstart'));
}

/**
 * Stops fake monetization session. Must be ran after you are done testing
 * with monetization as it will affect subsequent tests otherwise
 */
Cypress.Commands.add('stopWebMonetization', () => {
	cy.window().then(window => {
		const doc = window.document;
		doc.monetization.state = 'stopped';
		window.Cypress.monetized = false;
		// Re-init Coil.
		doc.dispatchEvent(new Event('coilstart'));
	})
})
