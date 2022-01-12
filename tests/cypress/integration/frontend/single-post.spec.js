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
} );
