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

		cy
			.reload()
			.then( () => {
				startMonetization( window );
			} );
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
 * Reset site to original state.
 * All post and post meta data is removed and restored from the xml file.
 * Custom data stored in the database is individually named to be removed from the database.
 * If any extra Coil customizations are added they must be added to the delete query.
 */
Cypress.Commands.add( 'resetSite', () => {
	// Removes all custom data from the database
	cy.exec( 'wp db query \'DELETE FROM wp_options WHERE option_name IN ("coil_global_settings_group", "coil_content_settings_posts_group", "coil_content_settings_excerpt_group", "coil_messaging_settings_group", "coil_appearance_settings_group");\' --allow-root' );
	cy.exec( 'wp db query \'DELETE FROM wp_postmeta;\' --allow-root' );
	cy.exec( 'wp db query \'DELETE FROM wp_posts;\' --allow-root' );

	// Adds site data back into the database
	cy.exec( 'wp import cypress/fixtures/coil-automation-CI.xml --authors=create  --allow-root' );
	cy.exec( 'wp rewrite structure \'/%postname%/\' --allow-root' );

	// Adds a payment pointer in
	cy.visit( '/wp-admin/admin.php?page=coil_settings' );
	cy.get( '#coil-global-settings' ).click();
	cy.get( '#coil_payment_pointer_id' )
		.click()
		.clear()
		.type( 'https://example.com/.well-known/pay' );
	cy.get( '#submit' ).click();
} );
