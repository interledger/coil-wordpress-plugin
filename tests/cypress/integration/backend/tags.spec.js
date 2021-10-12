/**
 * Site setting / option tests.
 */

describe( 'Tag Settings', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'checks that Coil tag settings can be updated', function() {
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=post_tag' );

		/*
		 * This is a workaround for WordPress hiding the Quick Actions row.
		 *
		 * Read https://docs.cypress.io/guides/core-concepts/conditional-testing.html#Element-existence
		 * before using this pattern anywhere else.
		 */
		const deleteTag =
			'.row-actions span.delete a[aria-label="Delete “demotag”"]';
		cy.get( 'body' ).then( ( $body ) => {
			if ( $body.find( deleteTag ).length ) {
				cy.get( deleteTag ).click( { force: true } );
			}
		} );

		// Create a tag
		cy.get( '#tag-name' ).type( 'demotag' );
		cy.get( '#tag-slug' ).type( 'demotagslug' );

		// Make the tag "fully gated"
		cy.get( '#coil-category-settings > label[for="gate-all"]' ).click();
		cy.get( '#submit' ).click();

		// Re-open and edit the tag to check the correct "gate-all" label is still applied and change it to "no-gating"
		const editTag =
			'.row-actions span.edit a[aria-label="Edit “demotag”"]';
		cy.get( editTag ).then( ( $element ) => {
			$element[ 0 ].click();
		} );
		cy.get( '#coil-category-settings > label[for="gate-all"] input' ).should(
			'be.checked',
		);
		cy.get( '#coil-category-settings > label[for="no-gating"] input' ).click();
		cy.get( '.button' ).click();

		// Re-open and edit the tag to check the correct "no-gating" label is still applied and change it to "no"
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=post_tag' );
		cy.get( editTag ).then( ( $element ) => {
			$element[ 0 ].click();
		} );
		cy.get( '#coil-category-settings > label[for="no-gating"] input' ).should(
			'be.checked',
		);
		cy.get( '#coil-category-settings > label[for="no"] input' ).click();
		cy.get( '.button' ).click();

		// Re-open and edit the tag to check the correct "no" label is still applied
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=post_tag' );
		cy.get( editTag ).then( ( $element ) => {
			$element[ 0 ].click();
		} );
		cy.get( '#coil-category-settings > label[for="no"] input' ).should(
			'be.checked',
		);
	} );
} );
