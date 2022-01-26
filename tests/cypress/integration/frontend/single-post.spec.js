/**
 * Site setting / option tests.
 */
// This is the payment pointer that is set during the rest function.
const paymentPointer = 'https://example.com/.well-known/pay';

// Most of these tests assume you have the test posts loaded in your WordPress.
describe( 'Single Posts', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'check that the payment pointer is printed when viewing a single post.', function() {
		cy.visit( '/monetized-and-public/' );
		cy.get( 'head meta[name="monetization"]' ).should( 'have.attr', 'content', paymentPointer );
	} );

	it( 'check that I can view single post set to monetized and public.', function() {
		cy.visit( '/monetized-and-public/' );
		cy
			.get( '.entry-content > p' )
			.should( 'be.visible' )
			.should( 'contain', 'Lorem ipsum' );
	} );

	it( 'check that I can view single post set to no monetization.', function() {
		cy.visit( '/no-monetization/' );
		cy.get( 'head meta[name="monetization"]', { timeout: 0 } ).should( 'not.exist' );
		cy
			.get( '.entry-content > p' )
			.should( 'be.visible' )
			.should( 'contain', 'Everything is visible in this post with no monetization.' );
	} );

	it( 'without a browser extension, check that I cannot view a post set to members only.', function() {
		cy.visit( '/coil-members-only/' );

		// Main article content should be hidden with CSS.
		cy.get( '.entry-content:not(.coil-message-container)' ).should( 'not.be.visible' );

		// "This content is for members only".
		cy.get( '.coil-message-container' ).should( 'be.visible' );
	} );

	it( 'check that content is visible when exclusive content has been disabled.', function() {
		// Ensure exclusive content is disabled
		const optionString = 'a:1:{s:21:\\\"coil_exclusive_toggle\\\";b:0;}';
		cy.exec( 'wp db query \'DELETE FROM wp_options WHERE option_name IN ("coil_exclusive_settings_group");\' --allow-root' );
		cy.exec( 'wp db query \'INSERT INTO wp_options (option_name, option_value) VALUES ( \"coil_exclusive_settings_group\", \"' + optionString + '\");\' --allow-root' );

		cy.visit( '/coil-members-only/' );

		// Post should be public
		cy
			.get( 'body' )
			.should( 'have.class', 'coil-public' )
			.should( 'have.class', 'coil-monetized' );

		// Content should be visible.
		cy
			.contains( 'This is a test post for the Coil Members Only state.' )
			.should( 'be.visible' );

		cy
			.get( '.coil-message-inner' )
			.should( 'not.exist' );

		// Promotion bar should be visible.
		cy
			.get( '.banner-message-inner' )
			.should( 'be.visible' );
	} );
} );
