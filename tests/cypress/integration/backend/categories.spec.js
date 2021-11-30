/**
 * Site setting / option tests.
 */

describe( 'Category Settings', function() {
	beforeEach( () => {
		cy.logInToWordPress( 'admin', 'password' );
		cy.resetSite();
	} );

	it( 'checks that Coil category settings are rendered correctly on the "Add Term" screen', function() {
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );

		// Create a category
		cy.get( '#tag-name' ).type( 'democategory' );
		cy.get( '#tag-slug' ).type( 'democategoryslug' );

		// The Default setting should be selected in the dropdown and the radio options should not be present
		cy.get( '#monetization_dropdown' ).invoke('val').should('eq', 'default');
		cy.get('#monetization_dropdown').find('option:selected').should('have.text', 'Default (Enabled & public)');
		cy.get('#coil-radio-selection').should('have.attr', 'style', 'display: none');

		// When "Enabled" is selected the radio options should appear with "Everyone" selected
		cy.get( '#monetization_dropdown' ).select('Enabled');
		cy.get('#coil-radio-selection').should('not.have.attr', 'style', 'display: none');
		cy.get( '#public' ).should( 'be.checked' );
		// Check that changing the radio option value won

		// When "Disabled" is selected the radio options should disappear
		cy.get( '#monetization_dropdown' ).select('Disabled');
		cy.get('#coil-radio-selection').should('have.attr', 'style', 'display: none');

		// When "Enabled" is selected the radio options should appear with "Everyone" selected again
		cy.get( '#monetization_dropdown' ).select('Enabled');
		cy.get('#coil-radio-selection').should('not.have.attr', 'style', 'display: none');
		cy.get( '#public' ).should( 'be.checked' );

		// When "Default" is selected the radio options should disappear again
		cy.get( '#monetization_dropdown' ).select('Default (Enabled & public)');
		cy.get('#coil-radio-selection').should('have.attr', 'style', 'display: none');

		// TODO: 
		//INSERT INTO wp_options (option_name, option_value) VALUES ("coil_general_settings_group", 'a:1:{s:17:"post_monetization";s:13:"not-monetized";}');
		// Change the default post status to "Disabled"
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=general_settings' );
		cy.get( '#post_monetization_not-monetized' ).click();
		cy.get( '#submit' ).click();

		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );

		// Create a category
		cy.get( '#tag-name' ).type( 'democategory' );
		cy.get( '#tag-slug' ).type( 'democategoryslug' );

		// "Default (Disabled)" should now be the default text
		cy.get( '#monetization_dropdown' ).invoke('val').should('eq', 'default');
		cy.get('#monetization_dropdown').find('option:selected').should('have.text', 'Default (Disabled)');
		cy.get('#coil-radio-selection').should('have.attr', 'style', 'display: none');

		// Change the default post status to monetized and exclusive
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=general_settings' );
		cy.get( '#post_monetization_monetized' ).click();
		cy.get( '#submit' ).click();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
		cy.get( '#post_visibility_exclusive' ).click();
		cy.get( '#submit' ).click();

		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );

		// Create a category
		cy.get( '#tag-name' ).type( 'democategory' );
		cy.get( '#tag-slug' ).type( 'democategoryslug' );

		// "Default (Enabled & exclusive)" should now be the default text
		cy.get( '#monetization_dropdown' ).invoke('val').should('eq', 'default');
		cy.get('#monetization_dropdown').find('option:selected').should('have.text', 'Default (Enabled & exclusive)');
		cy.get('#coil-radio-selection').should('have.attr', 'style', 'display: none');

		// Change the default post status back to monetized and public
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=general_settings' );
		cy.get( '#post_monetization_monetized' ).click();
		cy.get( '#submit' ).click();
		cy.visit( '/wp-admin/admin.php?page=coil_settings&tab=exclusive_settings' );
		cy.get( '#post_visibility_public' ).click();
		cy.get( '#submit' ).click();
	
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );

		// Create a category
		cy.get( '#tag-name' ).type( 'democategory' );
		cy.get( '#tag-slug' ).type( 'democategoryslug' );

		// "Default (Enabled & public)" should now be the default text
		cy.get( '#monetization_dropdown' ).invoke('val').should('eq', 'default');
		cy.get('#monetization_dropdown').find('option:selected').should('have.text', 'Default (Enabled & public)');
		cy.get('#coil-radio-selection').should('have.attr', 'style', 'display: none');

	} );

	it( 'checks that Coil category settings are rendered correctly on the "Edit Term" screen', function() {
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );

		// Create a category
		cy.get( '#tag-name' ).type( 'democategory' );
		cy.get( '#tag-slug' ).type( 'democategoryslug' );

		// Leave it set to "Default"
		cy.get( '#submit' ).click();

		// Re-open and edit the category to check the correct status is selected
		const editCategory =
			'.row-actions span.edit a[aria-label="Edit “democategory”"]';
		cy.get( editCategory ).then( ( $element ) => {
			$element[ 0 ].click();
		} );

		// Re-open and edit the category to check the correct status is selected and change it to "Disabled".
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );
		cy.get( editCategory ).then( ( $element ) => {
			$element[ 0 ].click();
		} );

		// The Default setting should be selected in the dropdown and the radio options should not be present
		cy.get( '#monetization_dropdown' ).invoke('val').should('eq', 'default');
		cy.get('#monetization_dropdown').find('option:selected').should('have.text', 'Default (Enabled & public)');
		cy.get('#coil-radio-selection').should('have.attr', 'style', 'display: none');

		// When "Enabled" is selected the radio options should appear with "Everyone" selected
		cy.get( '#monetization_dropdown' ).select('Enabled');
		cy.get('#coil-radio-selection').should('not.have.attr', 'style', 'display: none');
		cy.get( '#public' ).should( 'be.checked' );
		// Check that changing the radio option value won

		// When "Disabled" is selected the radio options should disappear
		cy.get( '#monetization_dropdown' ).select('Disabled');
		cy.get('#coil-radio-selection').should('have.attr', 'style', 'display: none');

		// When "Enabled" is selected the radio options should appear with "Everyone" selected again
		cy.get( '#monetization_dropdown' ).select('Enabled');
		cy.get('#coil-radio-selection').should('not.have.attr', 'style', 'display: none');
		cy.get( '#public' ).should( 'be.checked' );

		// When "Default" is selected the radio options should disappear again
		cy.get( '#monetization_dropdown' ).select('Default (Enabled & public)');
		cy.get('#coil-radio-selection').should('have.attr', 'style', 'display: none');
	} );

	it( 'checks that Coil category settings can be updated', function() {
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );

		/* TODO: Remove - all taxonomies deleted before each test once database issues resolved.
		 * This is a workaround for WordPress hiding the Quick Actions row.
		 *
		 * Read https://docs.cypress.io/guides/core-concepts/conditional-testing.html#Element-existence
		 * before using this pattern anywhere else.
		 */
		const deleteCategory = '.row-actions span.delete a[aria-label="Delete “democategory”"]';
		cy.get( 'body' ).then( ( $body ) => {
			if ( $body.find( deleteCategory ).length ) {
				cy.get( deleteCategory ).click( { force: true } );
			}
		} );

		// Create a category
		cy.get( '#tag-name' ).type( 'democategory' );
		cy.get( '#tag-slug' ).type( 'democategoryslug' );

		// Make the category monetized and exclusive
		cy.get( '#monetization_dropdown' ).select('Enabled');
		cy.get( '#exclusive' ).click();
		cy.get( '#submit' ).click();

		// Re-open and edit the category to check the correct status is selected and change it to monetized and public.
		const editCategory =
			'.row-actions span.edit a[aria-label="Edit “democategory”"]';
		cy.get( editCategory ).then( ( $element ) => {
			$element[ 0 ].click();
		} );
		cy.get( '#monetization_dropdown' ).invoke('val').should('eq', 'monetized');
		cy.get( '#exclusive' ).should( 'be.checked' );
		cy.get( '#coil-visibility-settings > label[for="public"] input' ).click();
		cy.get( '.button' ).click();

		// Re-open and edit the category to check the correct status is selected and change it to "Disabled".
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );
		cy.get( editCategory ).then( ( $element ) => {
			$element[ 0 ].click();
		} );
		cy.get( '#monetization_dropdown' ).invoke('val').should('eq', 'monetized');
		cy.get( '#public' ).should( 'be.checked' );
		cy.get( '#monetization_dropdown' ).select('Disabled');
		cy.get( '.button' ).click();

		// Re-open and edit the category to check the correct status is selected
		cy.visit( '/wp-admin/edit-tags.php?taxonomy=category' );
		cy.get( editCategory ).then( ( $element ) => {
			$element[ 0 ].click();
		} );
		cy.get( '#monetization_dropdown' ).invoke('val').should('eq', 'not-monetized');
		cy.get('#coil-radio-selection').should('have.attr', 'style', 'display: none');
	} );
} );
