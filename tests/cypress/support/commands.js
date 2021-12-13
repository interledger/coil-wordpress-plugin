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
Cypress.Commands.add( 'logInToWordPress', ( username, password ) => {
	cy.request( {
		method: 'POST',
		url: '/wp-login.php',
		form: true,
		body: {
			log: username,
			pwd: password,
		},
	} );

	// Verify by asserting an authentication cookie exists.
	cy.getCookies().then( ( cookies ) => {
		let authCookie = '';

		cookies.forEach( theCookie => {
			if ( theCookie.name.startsWith( 'wordpress_logged_in_' ) ) {
				authCookie = theCookie.name;
			}
		} );

		expect( authCookie ).to.include( 'wordpress_logged_in_' );
	} );
} );

/**
 * Mock and start a (fake) web monetization session.
 */
Cypress.Commands.add( 'startWebMonetization', () => {
	cy.window().then( ( window ) => {
		startMonetization( window );
	} );
} );

/**
 * Mock and start a (fake) web monetization session.
 *
 * @param {object} window that is simulating the browser
 */
function startMonetization( window ) {
	const doc = window.document;

	// Shim the Web Monetization API: https://webmonetization.org/specification.html
	if ( ! doc.monetization ) {
		doc.monetization = doc.createElement( 'div' );
	}
	doc.monetization.state = 'started';

	// Re-init Coil.
	doc.dispatchEvent( new Event( 'coilstart' ) );

	window.Cypress.monetized = true;

	// Trigger the "user has paid $$$" event.
	doc.monetization.dispatchEvent( new Event( 'monetizationstart' ) );
}

/**
 * Stops fake monetization session. Must be ran after you are done testing
 * with monetization as it will affect subsequent tests otherwise
 */
Cypress.Commands.add( 'stopWebMonetization', () => {
	cy.window().then( window => {
		const doc = window.document;
		doc.monetization.state = 'stopped';
		window.Cypress.monetized = false;
		// Re-init Coil.
		doc.dispatchEvent( new Event( 'coilstart' ) );
	} );
} );

/**
 * Inserts settings into the wp_options table in the database
 * This function only supports strings
 *
 * @param optionName The name of the settings group
 * @param settings The data to be inserted in the form of an array of objects which have key and value properties.
 */
Cypress.Commands.add('addSetting', (optionName, settings) => {
	cy.exec( 'wp db query \'DELETE FROM wp_options WHERE option_name IN ("' + optionName + '");\' --allow-root' );
	let numItems = settings.length;
	let optionString = 'a:' + numItems + ':{';
	for ( let i = 0; i < numItems; i++ ) {
		let keyLength = settings[i].key.length;
		let valueLength = settings[i].value.length;
		optionString += 's:' + keyLength + ':\\\"' + settings[i].key + '\\\";'; 
		optionString += 's:' + valueLength + ':\\\"' + settings[i].value + '\\\";'; 
	}
	optionString += '}';

	cy.exec( 'wp db query \'INSERT INTO wp_options (option_name, option_value) VALUES ( \"' + optionName + '\", \"' + optionString + '\");\' --allow-root' );

});

/**
 * Reset site to original state.
 * All post and post meta data is removed and restored from the xml file.
 * Custom data stored in the database is individually named to be removed from the database.
 * If any extra Coil customizations are added they must be added to the delete query.
 */
Cypress.Commands.add( 'resetSite', () => {
	// Removes all custom data from the database
	cy.exec( 'wp db query \'DELETE FROM wp_options WHERE option_name IN ( "coil_general_settings_group", "coil_exclusive_settings_group", "coil_button_settings_group");\' --allow-root' );
	cy.exec( 'wp db query \'DELETE FROM wp_postmeta;\' --allow-root' );
	cy.exec( 'wp db query \'DELETE FROM wp_posts;\' --allow-root' );
	cy.exec( 'wp db query \'DELETE FROM wp_termmeta;\' --allow-root' );
	cy.exec( 'wp db query \'DELETE FROM wp_terms;\' --allow-root' );
	// Adds a payment pointer in
	cy.exec( 'wp db query \'INSERT INTO wp_options (option_name, option_value) VALUES ( \"coil_general_settings_group\", \"a:1:{s:20:\\\"coil_payment_pointer\\\";s:35:\\\"https:\/\/example.com\/.well-known\/pay\\\";}\");\' --allow-root' );

	// Adds site data back into the database
	cy.exec( 'wp import cypress/fixtures/coil-automation-CI.xml --authors=create  --allow-root' );
	cy.exec( 'wp rewrite structure \'/%postname%/\' --allow-root' );

} );
