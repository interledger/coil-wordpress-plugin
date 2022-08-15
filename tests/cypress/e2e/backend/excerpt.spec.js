/**
 * Excerpt settings.
*/

describe( 'Excerpt settings', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
	} );

	it( 'Checks Excerpt Settings defaults', () => {
		cy
			.get( '#post_excerpt' )
			.should( 'not.be.checked' );

		cy
			.get( '#page_excerpt' )
			.should( 'not.be.checked' );
	} );

	it( 'Checks Excerpt Settings can be changed', () => {
		cy
			.get( '#post_excerpt' )
			.click();

		cy
			.get( '#page_excerpt' )
			.click();

		cy
			.get( '#submit' )
			.click();

		cy
			.get( '#post_excerpt' )
			.should( 'be.checked' );

		cy
			.get( '#page_excerpt' )
			.should( 'be.checked' );
	} );
} );
