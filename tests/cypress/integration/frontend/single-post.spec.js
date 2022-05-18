/**
 * Interacting with posts as a non WM-enabled user.
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
		cy.addSetting( 'coil_exclusive_settings_group', [
			{
				key: 'coil_exclusive_toggle',
				value: '0',
			},
		] );

		cy.visit( '/coil-members-only/' );

		// Post should be monetized and public.
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
	} );
} );
