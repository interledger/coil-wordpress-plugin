describe( 'Excerpt behaviour for posts', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Checks that the excerpt respects coil settings', () => {
		setExcerptVisibility( true, 'post' );

		cy.visit( '/excerpt-post/' );

		cy
			.contains( 'This content should be visible as an excerpt.' )
			.should( 'be.visible' );

		setExcerptVisibility( false, 'post' );

		cy.visit( '/excerpt-post/' );
		cy
			.contains( 'This content should be visible as an excerpt.' )
			.should( 'not.be.visible' );
	} );
} );

describe( 'Excerpt behaviour for pages', () => {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'Checks that the excerpt respects coil settings', () => {
		setExcerptVisibility( true, 'page' );

		cy.visit( '/excerpt-page/' );

		cy
			.contains( 'This content should be visible as an excerpt.' )
			.should( 'be.visible' );

		setExcerptVisibility( false, 'page' );

		cy.visit( '/excerpt-page/' );
		cy
			.contains( 'This content should be visible as an excerpt.' )
			.should( 'not.be.visible' );
	} );
} );

/**
 * Sets whether excerpts are visible on posts
 *
 * @param {boolean} state for the excerpt setting
 * @param {String} postType indicates whether this is a page or post setting
 */
function setExcerptVisibility( state, postType ) {
	let checkboxID;
	if ( postType === 'post' ) {
		checkboxID = '#post_display_excerpt';
	} else if ( postType === 'page' ) {
		checkboxID = '#page_display_excerpt';
	}
	cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=excerpt_settings' );
	switch ( state ) {
		case true:
			cy
				.get( checkboxID )
				.check();
			break;

		case false:
			cy
				.get( checkboxID )
				.uncheck();
			break;
	}
	cy
		.get( '#submit' )
		.click();
}
