/**
 * Site setting / option tests.
 */

describe( 'Category Settings', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'checks that Coil category settings can be updated', function() {
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );

		/*
		 * This is a workaround for WordPress hiding the Quick Actions row.
		 *
		 * Read https://docs.cypress.io/guides/core-concepts/conditional-testing.html#Element-existence
		 * before using this pattern anywhere else.
		 */
		const deleteCategory =
			'.row-actions span.delete a[aria-label="Delete “democategory”"]';
		cy.get( 'body' ).then( ( $body ) => {
			if ( $body.find( deleteCategory ).length ) {
				cy.get( deleteCategory ).click( { force: true } );
			}
		} );

		// Create a category
		cy.get( '#tag-name' ).type( 'democategory' );
		cy.get( '#tag-slug' ).type( 'democategoryslug' );

		// Make the category "fully gated"
		cy.get( '#coil-category-settings > label[for="gate-all"]' ).click();
		cy.get( '#submit' ).click();

		// Re-open and edit the category to check the correct "gate-all" label is still applied and change it to "no-gating"
		const editCategory =
			'.row-actions span.edit a[aria-label="Edit “democategory”"]';
		cy.get( editCategory ).then( ( $element ) => {
			$element[ 0 ].click();
		} );
		cy.get( '#coil-category-settings > label[for="gate-all"] input' ).should(
			'be.checked',
		);
		cy.get( '#coil-category-settings > label[for="no-gating"] input' ).click();
		cy.get( '.button' ).click();

		// Re-open and edit the category to check the correct "no-gating" label is still applied and change it to "no"
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );
		cy.get( editCategory ).then( ( $element ) => {
			$element[ 0 ].click();
		} );
		cy.get( '#coil-category-settings > label[for="no-gating"] input' ).should(
			'be.checked',
		);
		cy.get( '#coil-category-settings > label[for="no"] input' ).click();
		cy.get( '.button' ).click();

		// Re-open and edit the category to check the correct "no" label is still applied
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );
		cy.get( editCategory ).then( ( $element ) => {
			$element[ 0 ].click();
		} );
		cy.get( '#coil-category-settings > label[for="no"] input' ).should(
			'be.checked',
		);
	} );
} );
