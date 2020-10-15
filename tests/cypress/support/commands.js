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
 * Mock and start a (fake) web monetisation session.
 */
Cypress.Commands.add('startWebMonetization', () => {

	cy.document().then((doc) => {

		// Shim the Web Monetization API: https://webmonetization.org/specification.html
		if (! doc.monetization) {
			doc.monetization = doc.createElement('div');
		}
		doc.monetization.state = 'started';

		// Re-init Coil.
		doc.dispatchEvent(new Event('coilstart'));

		var unixtime_ms = new Date().getTime();
		while (new Date().getTime() < unixtime_ms + 1000) {}

		// Trigger the "user has paid $$$" event.
		doc.monetization.dispatchEvent(new Event('monetizationstart'));
	});
});
