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
